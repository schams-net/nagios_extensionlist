<?php
namespace SchamsNet\NagiosExtensionlist\Domain\Repository;

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

/**
 * Repository: Extensionlist
 */
class ExtensionlistRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * Set default ordering for the entire repository
     *
     * @var array
     */
    protected $defaultOrderings = array(
        'extension_key' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
        'integer_version' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
    );

    /**
     * Initialize
     *
     * @access public
     * @return viod
     */
    public function initializeObject()
    {
        /** @var $querySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
        $querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Returns the last updated extension
     *
     * @access public
     * @return stdObject
     */
    public function findLastUpdatedExtension() {
        $query = $this->createQuery();
        $query->setOrderings(array('last_updated' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING));
        $query->setLimit(1);
        return $query->execute();
    }
}
