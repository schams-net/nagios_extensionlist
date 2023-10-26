<?php
declare(strict_types=1);
namespace SchamsNet\NagiosExtensionlist\Service;

/*
 * This file is part of the TYPO3 Extension "Nagios Extensionlist"
 *
 * Author: Michael Schams <schams.net>
 * Website: https://schams.net
 *
 * Based on the work contributed by atlantis dx GmbH <info@atlantisdx.de>.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please see
 * https://www.gnu.org/licenses/gpl.html
 */

use \TYPO3\CMS\Install\Service\CoreVersionService;

class Typo3CoreVersionService extends CoreVersionService
{
    private $releasesCache = [];

    /**
     * Returns a list of available major versions.
     */
    public function getAvailableMajorVersions(): array
    {
        return ['8', '9', '10', '11', '12'];
    }

    /**
     * @TODO
     */
    public function getReleasesForMajor($majorVersion)
    {
        $releases = [];
        if (!array_key_exists($majorVersion, $this->releasesCache)) {
            $url = 'major/' . $majorVersion . '/release';
            $this->releasesCache[$majorVersion] = $this->fetchFromRemote($url);
        }
        foreach ($this->releasesCache[$majorVersion] as $release) {
            $releases[] = $release['version'];
        }
        return $releases;
    }

    /**
     * @TODO
     */
    public function getInsecureReleasesForMajor($majorVersion)
    {
        $insecureVersions = [];

        $versions = $this->getReleasesForMajor($majorVersion);
        $latestSecurityVersion = $this->getLatestSecurityRelevantVersionForMajor($majorVersion);

        foreach ($versions as $version) {
            if (version_compare($version, $latestSecurityVersion) === -1) {
                $insecureVersions[] = $version;
            }
        }
        return $insecureVersions;
    }

    /**
     * @TODO return type hint string|bool ?
     */
    public function getLatestSecurityRelevantVersionForMajor($majorVersion): string
    {
        $url = 'major/' . $majorVersion . '/release/latest/security';
        $result = $this->fetchFromRemote($url);
        return $result['version'] ?? false;
    }
}
