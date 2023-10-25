<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace SchamsNet\NagiosExtensionlist\CoreVersion;

class MajorRelease
{
    public const RELEASE_MAINTENANCE_COMMUNITY = 'community';
    public const RELEASE_MAINTENANCE_ELTS = 'elts';
    public const RELEASE_MAINTENANCE_OUTDATED = 'outdated';

    private string $maintenanceType;
    private ?CoreRelease $latestSecurityPatchRelease;
    private array $coreReleases;

    protected string $version;
    protected ?string $lts = null;
    protected string $title;
    protected MaintenanceWindow $maintenanceWindow;

    public function __construct(string $version, ?string $lts, string $title, MaintenanceWindow $maintenanceWindow)
    {
        $this->version = $version;
        $this->lts = $lts;
        $this->title = $title;
        $this->maintenanceWindow = $maintenanceWindow;

        if ($this->maintenanceWindow->isSupportedByCommunity()) {
            $this->maintenanceType = self::RELEASE_MAINTENANCE_COMMUNITY;
        } elseif ($this->maintenanceWindow->isSupportedByElts()) {
            $this->maintenanceType = self::RELEASE_MAINTENANCE_ELTS;
        } else {
            $this->maintenanceType = self::RELEASE_MAINTENANCE_OUTDATED;
        }
    }

    public function getLatestSecurityPatchRelease(): ?CoreRelease
    {
        return $this->latestSecurityPatchRelease;
    }

    public function setLatestSecurityPatchRelease(?CoreRelease $latestSecurityPatchRelease): void
    {
        $this->latestSecurityPatchRelease = $latestSecurityPatchRelease;
    }

    public function getCoreReleases(): array
    {
        return $this->coreReleases;
    }

    public function setCoreReleases(array $coreReleases): void
    {
        $this->coreReleases = $coreReleases;
    }

    public function getMaintenanceType(): string
    {
        return $this->maintenanceType;
    }

    public static function fromApiResponse(array $response): self
    {
        $maintenanceWindow = MaintenanceWindow::fromApiResponse($response);
        $ltsVersion = isset($response['lts']) ? (string)$response['lts'] : null;

        return new self((string)$response['version'], $ltsVersion, $response['title'], $maintenanceWindow);
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getLts(): ?string
    {
        return $this->lts;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMaintenanceWindow(): MaintenanceWindow
    {
        return $this->maintenanceWindow;
    }

    public function getMaintainedUntil()
    {
        return $this->maintenanceWindow->getCommunitySupport();
    }

    public function getEltsUntil()
    {
        return $this->maintenanceWindow->getEltsSupport();
    }

    public function containsInsecureReleases(): bool
    {
        return $this->maintenanceType === self::RELEASE_MAINTENANCE_OUTDATED || $this->latestSecurityPatchRelease !== null;
    }

    public function getNagiosString(): string
    {
        if ($this->maintenanceType === self::RELEASE_MAINTENANCE_OUTDATED) {
            $versionString = $this->getVersion();
            while (mb_substr_count($versionString, '.') < 2) {
                $versionString .= '.x';
            }
            $insecureVersions[] = $versionString;
        } else {
            /** @var CoreRelease $coreRelease */
            foreach ($this->coreReleases as $coreRelease) {
                $versionString = $coreRelease->getVersion();
                if (version_compare($versionString, $this->getLts()) === -1) {
                    if (mb_substr_count($versionString, '.') == 2) {
                        $insecureVersions[] = mb_substr($versionString, 0, mb_strrpos($versionString, '.')) . '.x';
                    }
                } elseif (version_compare(
                    $versionString,
                    $this->latestSecurityPatchRelease->getVersion()
                ) === -1) {
                    $insecureVersions[] = $versionString;
                }
            }
        }
        return implode(',', $insecureVersions);
    }
}
