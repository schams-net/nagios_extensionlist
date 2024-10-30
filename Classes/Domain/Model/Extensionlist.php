<?php

declare(strict_types=1);

namespace SchamsNet\NagiosExtensionlist\Domain\Model;

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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Model: Extensionlist
 */
class Extensionlist extends AbstractEntity
{
    protected $extensionKey = '';
    protected $title = '';
    protected $version = '';
    protected $integerVersion = 0;
    protected $reviewState = '';
    protected $lastUpdated = 0;

    /**
     * Set extension key
     */
    public function setExtensionKey(string $extensionKey): void
    {
        $this->extensionKey = $extensionKey;
    }

    /**
     * Get extension key
     */
    public function getExtensionKey(): string
    {
        return $this->extensionKey;
    }

    /**
     * Set title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * Get version
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Get version as integer
     */
    public function getIntegerVersion(): int
    {
        return $this->integerVersion;
    }

    /**
     * Set review state
     */
    public function setReviewState(int $reviewState): void
    {
        $this->reviewState = $reviewState;
    }

    /**
     * Get review state
     */
    public function getReviewState(): int
    {
        return $this->reviewState;
    }

    /**
     * Set date/time (time stamp) of last update
     */
    public function setLastUpdated(\DateTime $lastUpdated): void
    {
        $this->lastUpdated = $lastUpdated;
    }

    /**
     * Get date/time (time stamp) of last update
     */
    public function getLastUpdated(): \DateTime
    {
        return $this->lastUpdated;
    }
}
