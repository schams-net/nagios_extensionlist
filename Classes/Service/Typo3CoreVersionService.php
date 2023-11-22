<?php
declare(strict_types=1);
namespace SchamsNet\NagiosExtensionlist\Service;

/*
 * This file is part of the TYPO3 Extension "Nagios Extensionlist"
 *
 * Based on the work contributed by atlantis dx GmbH <info@atlantisdx.de>.
 * @see \TYPO3\CMS\Install\Service\TYPO3CoreVersionService
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please see
 * https://www.gnu.org/licenses/gpl.html
 */

use SchamsNet\NagiosExtensionlist\CoreVersion\CoreRelease;
use SchamsNet\NagiosExtensionlist\CoreVersion\MajorRelease;
use SchamsNet\NagiosExtensionlist\Service\Exception\RemoteFetchException;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend as Cache;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

class Typo3CoreVersionService
{
    /**
     * Base URI of the TYPO3 version REST API
     */
    protected string $apiBaseUrl = 'https://get.typo3.org/api/v1/';

    /**
     * Support type of the TYPO3 major release. Valid options are:
     * 'community' (standard maintenance), 'elts' (ELTS maintenance), 'outdated' (unsupported)
     */
    private array $availableMajorReleases = [];

    /**
     * @TODO
     */
    private FrontendInterface $cache;

    /**
     * @TODO
     */
    private array $releases = [];

    /**
     * @TODO
     */
    public function __construct(FrontendInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Fetch data from TYPO3 API or return data from cache if available
     * @throws RemoteFetchException
     */
    protected function fetchFromRemote(string $url): array
    {
        $cacheHash = md5($url);
        $releases = $this->cache->get($cacheHash);
        if (is_array($releases) && array_key_exists($url, $releases)) {
            // Use data from cache
            $this->releases = $releases;
        } else {
            // Retrieve data from TYPO3 API
            $apiUrl = $this->apiBaseUrl . $url;
            $json = GeneralUtility::getUrl($apiUrl);

            if (!$json) {
                $this->throwFetchException($url);
            }
            $this->releases[$url] = json_decode($json, true);
            $this->cache->set($cacheHash, $this->releases);
        }
        return $this->releases[$url];
    }

    /**
     * Helper method to throw same exception in multiple places
     * @throws RemoteFetchException
     */
    protected function throwFetchException(string $url): void
    {
        throw new RemoteFetchException(
            'Fetching ' .
            $url .
            ' failed. Check if the instance can establish a connection to the TYPO3 API.',
            1380897593
        );
    }

    /**
     * Returns all TYPO3 versions grouped by maintenance state
     * @throws RemoteFetchException
     */
    private function getAvailableMajorReleases(): array
    {
        if (empty($this->availableMajorReleases)) {
            $url = 'major';
            $result = $this->fetchFromRemote($url);

            $this->availableMajorReleases = [
                'community' => [],
                'elts' => [],
                'outdated' => [],
            ];

            foreach ($result as $release) {
                $majorRelease = MajorRelease::fromApiResponse($release);
                if ($majorRelease->getMaintenanceType() !== MajorRelease::RELEASE_MAINTENANCE_OUTDATED) {
                    $majorRelease->setCoreReleases($this->getCoreReleasesByMajorRelease($majorRelease));
                    $majorRelease->setLatestSecurityPatchRelease(
                        $this->getLatestSecurityRelevantVersionForMajor($majorRelease)
                    );
                }
                $this->availableMajorReleases[$majorRelease->getMaintenanceType()][$majorRelease->getVersion()] = $majorRelease;
            }
        }
        return $this->availableMajorReleases;
    }

    /**
     * Returns all major TYPO3 releases, including supported/maintained and unsupported versions
     */
    public function getAllMajorReleases(): array
    {
        $majorVersions = $this->getAvailableMajorReleases();
        return array_merge(
            $majorVersions[MajorRelease::RELEASE_MAINTENANCE_COMMUNITY],
            $majorVersions[MajorRelease::RELEASE_MAINTENANCE_ELTS],
            $majorVersions[MajorRelease::RELEASE_MAINTENANCE_OUTDATED]
        );
    }

    /**
     * Returns all currently supported/maintained major TYPO3 releases (these are typically community and ELTS releases)
     */
    public function getSupportedMajorReleases(): array
    {
        $majorVersions = $this->getAvailableMajorReleases();
        return array_merge(
            $majorVersions[MajorRelease::RELEASE_MAINTENANCE_COMMUNITY],
            $majorVersions[MajorRelease::RELEASE_MAINTENANCE_ELTS]
        );
    }

    /**
     * Returns all unsupported/unmaintained ("outdated") TYPO3 releases
     */
    public function getOutdatedMajorVersions(): array
    {
        $availableMajorVersions = $this->getAvailableMajorReleases();
        return $availableMajorVersions[MajorRelease::RELEASE_MAINTENANCE_OUTDATED];
    }

    /**
     * Returns all TYPO3 Core releases of a specific version
     * @throws \TYPO3\CMS\Install\Service\Exception\RemoteFetchException
     */
    private function getCoreReleasesByMajorRelease(MajorRelease $majorRelease): array
    {
        $majorReleaseVersion = $majorRelease->getVersion();
        if (!array_key_exists($majorReleaseVersion, $this->releases)) {
            $url = 'major/' . $majorReleaseVersion . '/release';
            $result = $this->fetchFromRemote($url);
            $this->releases[$majorReleaseVersion] = [];
            foreach ($result as $release) {
                if (!empty($release)) {
                    $this->releases[$majorReleaseVersion][] =
                        CoreRelease::fromApiResponse($release);
                }
            }
        }
        return $this->releases[$majorReleaseVersion];
    }

    /**
     * Returns true if the release is outdated, otherwise false
     */
    private function isOutdatedMajorRelease(MajorRelease $majorVersion): bool
    {
        $availableMajorVersions = $this->getAvailableMajorReleases();
        return array_key_exists($majorVersion->getVersion(), $availableMajorVersions['outdated']);
    }

    /**
     * Returns the latest security patch release for a specific major version
     */
    private function getLatestSecurityRelevantVersionForMajor(MajorRelease $majorVersion): ?CoreRelease
    {
        $url = 'major/' . $majorVersion->getVersion() . '/release/latest/security';
        $result = $this->fetchFromRemote($url);
        return CoreRelease::fromApiResponse($result);
    }
}
