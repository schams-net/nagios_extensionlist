<?php
namespace SchamsNet\NagiosExtensionlist\Controller;

/*
 * This file is part of the TYPO3 Extension "Nagios Extensionlist"
 *
 * Author: Michael Schams <schams.net>
 * Website: https://schams.net
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please see
 * https://www.gnu.org/licenses/gpl.html
 */

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

use \SchamsNet\NagiosExtensionlist\Domain\Repository\AccesshistoryRepository;
use \SchamsNet\NagiosExtensionlist\Domain\Repository\ExtensionlistRepository;

/**
 * Extensionlist Controller
 */
class ExtensionlistController extends ActionController
{
    /**
     * Extensionlist Repository
     *
     * @access protected
     * @var \SchamsNet\NagiosExtensionlist\Domain\Repository\ExtensionlistRepository
     */
    protected $extensionlistRepository;

    /**
     * Accesshistory Repository
     *
     * @access protected
     * @var \SchamsNet\NagiosExtensionlist\Domain\Repository\AccesshistoryRepository
     */
    protected $accesshistoryRepository;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Inject Extensionlist Repository.
     *
     * @access public
     * @param \SchamsNet\NagiosExtensionlist\Domain\Repository\ExtensionlistRepository $extensionlistRepository
     */
    public function injectExtensionlistRepository(ExtensionlistRepository $extensionlistRepository)
    {
        $this->extensionlistRepository = $extensionlistRepository;
    }

    /**
     * Inject Accesshistory Repository.
     *
     * @access public
     * @param \SchamsNet\NagiosExtensionlist\Domain\Repository\AccesshistoryRepository $accesshistoryRepository
     */
    public function injectAccesshistoryRepository(AccesshistoryRepository $accesshistoryRepository)
    {
        $this->accesshistoryRepository = $accesshistoryRepository;
    }

    /**
     * Generates list of insecure extensions.
     *
     * @access public
     * @return void
     */
    public function listInsecureExtensionsAction()
    {
        // store client details in access history
        $this->storeClientDetails();

        $insecureExtensions = $this->extensionlistRepository->findByReviewState(-1);
        $insecureExtensionsAndVersionCsv = $this->convertVersionsToCommaSeparatedValues($insecureExtensions);

        // Determine status of extension list by retrieving most recent record
        $lastUpdated = $this->extensionlistRepository->findLastUpdatedExtension();

        // Make sure, extension list is not empty
        if (is_object($lastUpdated) && $lastUpdated->count() > 0) {
            $lastUpdated = $lastUpdated->getFirst()->getLastUpdated();
            $this->view->assignMultiple(
                array(
                    'configurationError' => false,
                    'configurationFileHostName' => GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY'),
                    'configurationFileDate' => date('Ymd'),
                    'configurationFileDateHumanReadable' => date('d/M/Y'),
                    'configurationFileDateTimestamp' => time(),
                    'configurationFileIdentifier' => $this->getConfigurationFileId($insecureExtensionsAndVersionCsv),
                    'extensionCountLastUpdated' => $lastUpdated,
                    'extensionCountInsecureExtensions' => count($insecureExtensionsAndVersionCsv),
                    'extensionCountInsecureVersions' => $insecureExtensions->count(),
                    'extensionlist' => $insecureExtensionsAndVersionCsv
                )
            );
        } else {
            $this->view->assign('configurationError', true);
        }
    }

    /**
     * Returns an array of extensions (key) and comma-separated-list of versions (value).
     *
     * @access private
     * @param TYPO3\CMS\Extbase\Persistence\Generic\QueryResult Insecure extensions
     * @return array
     */
    private function convertVersionsToCommaSeparatedValues($insecureExtensions)
    {
        // init
        $extensionlist = array();

        foreach ($insecureExtensions as $uid => $extension) {
            $key = $extension->getExtensionKey();
            $title = $extension->getTitle();
            $version = $extension->getVersion();
            //$integerVersion = $extension->getIntegerVersion();
            $versionList = $version;

            if (array_key_exists($key, $extensionlist)) {
                $versionList = $extensionlist[$key]['versionList'] . ',' . $version;
            }

            $extensionlist[$key] = array(
                'extensionKey' => $key,
                'title' => $title,
                'versionList' => $versionList
            );
        }

        return $extensionlist;
    }

    /**
     * Returns a unique identification string based on the current list of insecure extensions.
     * FOr example: '54391-C872F-E1C8B-4F980'.
     *
     * @access private
     * @param array Insecure extensions and version
     * @return string Identification string
     */
    private function getConfigurationFileId(array $insecureExtensionsAndVersionCsv)
    {
        $identificationString = '-';
        $identificationArray = str_split(substr(md5(serialize($insecureExtensionsAndVersionCsv)), 0, 20), 5);
        return strtoupper(implode('-', $identificationArray));
    }

    /**
     * Stores details of client accessing the instance as an access history record.
     *
     * @access private
     * @return void
     */
    private function storeClientDetails()
    {
        $accesshistory = $this->objectManager->get('SchamsNet\\NagiosExtensionlist\\Domain\\Model\\Accesshistory');
        $accesshistory->setRemoteAddress(GeneralUtility::getIndpEnv('REMOTE_ADDR'));
        $accesshistory->setXForwardedFor($this->extractFromHttpHeader(array('X-FORWARDED-FOR')));
        $accesshistory->setCountryCode($this->extractFromHttpHeader(array('CLOUDFRONT-VIEWER-COUNTRY')));
        $accesshistory->setNagiosPluginVersion('');
        $accesshistory->setNagiosVersion('');
        $accesshistory->setUseragent($this->extractFromHttpHeader(array('USER_AGENT')));
        $accesshistory->setRequest(GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'));
        $this->accesshistoryRepository->add($accesshistory);
        $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager')->persistAll();
    }

    /**
     * Returns the given HTTP header, if it exists
     *
     * @access private
     * @param array List of HTTP headers to search for. Must be upper case and without leading 'HTTP_' e.g. 'USER_AGENT'
     * @return string Value of the HTTP header if found
     */
    private function extractFromHttpHeader(array $httpHeader = array())
    {
        foreach ($_SERVER as $httpHeaderKey => $httpHeaderValue) {
            $httpHeaderKey = preg_replace('/^HTTP_/', '', trim(strtoupper($httpHeaderKey)));
            if (is_string($httpHeaderKey) && !empty($httpHeaderKey)
            && in_array($httpHeaderKey, $httpHeader)
            && is_string($httpHeaderValue) && !empty($httpHeaderValue) ) {
                return $httpHeaderValue;
            }
        }
        return '';
    }
}
