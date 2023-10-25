<?php

namespace SchamsNet\NagiosExtensionlist\Service;

use SchamsNet\NagiosExtensionlist\CoreVersion\CoreRelease;
use SchamsNet\NagiosExtensionlist\CoreVersion\MajorRelease;
use SchamsNet\NagiosExtensionlist\Service\Exception\RemoteFetchException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 *  Copyright notice
 *
 *  (c) 2023 atlantis dx GmbH <info@atlantisdx.de>, atlantis dx GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 *
 * @see \TYPO3\CMS\Install\Service\TYPO3CoreVersionService
 ***************************************************************/
class TYPO3CoreVersionService
{
    /**
     * Base URI for TYPO3 Version REST api
     *
     * @var string
     */
    protected $apiBaseUrl = 'https://get.typo3.org/api/v1/';

    /**
     * support type categorized storage for major versions of TYPO3
     * internal usage
     * 'community' => normal maintenance
     * 'elts' => elts maintenance
     * 'outdated' => no maintenance
     *
     * @var array[string][]MajorRelease
     */
    private array $availableMajorReleases = [];

    /**
     * Cache the TYPO3 API response in memory
     * internal usage
     * @var array[version][CoreRelease]
     */
    private array $releasesCache = [];

    /**
     * use in memory cache to reduce API calls
     * @param string $url
     * @return array
     * @throws RemoteFetchException
     */
    protected function fetchFromRemote(string $url): array
    {
        if (!array_key_exists($url, $this->releasesCache)) {
            $apiUrl = $this->apiBaseUrl . $url;
            $json = GeneralUtility::getUrl($apiUrl);

            if (!$json) {
                $this->throwFetchException($url);
            }
            $this->releasesCache[$url] = json_decode($json, true);
        }

        return $this->releasesCache[$url];
    }

    /**
     * Helper method to throw same exception in multiple places
     *
     * @param string $url
     * @throws RemoteFetchException
     */
    protected function throwFetchException(string $url): void
    {
        throw new RemoteFetchException(
            'Fetching ' .
            $url .
            ' failed. Maybe this instance can not connect to the remote system properly.',
            1380897593
        );
    }

    /**
     * get all TYPO3 versions grouped by maintenance state
     *
     * @return array{community: MajorRelease[], elts: MajorRelease[], outdated: MajorRelease[]}
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

                if ($majorRelease->getMaintenanceType(
                ) !== MajorRelease::RELEASE_MAINTENANCE_OUTDATED) {
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

    public function getAllMajorReleases()
    {
        $majorVersions = $this->getAvailableMajorReleases();

        return array_merge(
            $majorVersions[MajorRelease::RELEASE_MAINTENANCE_COMMUNITY],
            $majorVersions[MajorRelease::RELEASE_MAINTENANCE_ELTS],
            $majorVersions[MajorRelease::RELEASE_MAINTENANCE_OUTDATED]
        );
    }

    /**
     * get all TYPO3 versions which are maintained
     * normally all community and elts releases
     *
     * @return array{MajorRelease}
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
     * get all TYPO3 versions which are not maintained
     *
     * @return array{MajorRelease}
     */
    public function getOutdatedMajorVersions(): array
    {
        $availableMajorVersions = $this->getAvailableMajorReleases();

        return $availableMajorVersions[MajorRelease::RELEASE_MAINTENANCE_OUTDATED];
    }

    /**
     * get all core releases of a specific version
     * @param MajorRelease $majorRelease
     * @return array[CoreRelease]
     * @throws \TYPO3\CMS\Install\Service\Exception\RemoteFetchException
     */
    private function getCoreReleasesByMajorRelease(
        \SchamsNet\NagiosExtensionlist\CoreVersion\MajorRelease $majorRelease
    ): array {
        $majorReleaseVersion = $majorRelease->getVersion();
        if (!array_key_exists($majorReleaseVersion, $this->releasesCache)) {
            $url = 'major/' . $majorReleaseVersion . '/release';
            $result = $this->fetchFromRemote($url);
            $this->releasesCache[$majorReleaseVersion] = [];
            foreach ($result as $release) {
                if (!empty($release)) {
                    $this->releasesCache[$majorReleaseVersion][] =
                        CoreRelease::fromApiResponse($release);
                }
            }
        }
        return $this->releasesCache[$majorReleaseVersion];
    }

    private function isOutdatedMajorRelease(MajorRelease $majorVersion)
    {
        $availableMajorVersions = $this->getAvailableMajorReleases();

        return array_key_exists($majorVersion->getVersion(), $availableMajorVersions['outdated']);
    }

    private function getLatestSecurityRelevantVersionForMajor(
        MajorRelease $majorVersion
    ): ?CoreRelease {
        $url = 'major/' . $majorVersion->getVersion() . '/release/latest/security';
        $result = $this->fetchFromRemote($url);
        return CoreRelease::fromApiResponse($result);
    }
}
