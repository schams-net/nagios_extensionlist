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

use \TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Model: Extensionlist
 */
class Extensionlist extends AbstractEntity
{
    /**
     * @var string
     */
    protected $extensionKey = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $version = '';

    /**
     * @var int
     */
    protected $integerVersion = 0;

    /**
     * @var int
     */
    protected $reviewState = '';

    /**
     * @var \DateTime
     */
    protected $lastUpdated = 0;

    /**
     * @param string $extensionKey
     * @return void
     */
    public function setExtensionKey(string $extensionKey): void
    {
        $this->extensionKey = $extensionKey;
    }

    /**
     * @return string
     */
    public function getExtensionKey(): string
    {
        return $this->extensionKey;
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $version
     * @return void
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return int
     */
    public function getIntegerVersion(): int
    {
        return $this->integerVersion;
    }

    /**
     * @param int $reviewState
     * @return void
     */
    public function setReviewState(int $reviewState): void
    {
        $this->reviewState = $reviewState;
    }

    /**
     * @return int
     */
    public function getReviewState(): int
    {
        return $this->reviewState;
    }

    /**
     * @param \DateTime $lastUpdated
     * @return void
     */
    public function setLastUpdated(\DateTime $lastUpdated): void
    {
        $this->lastUpdated = $lastUpdated;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdated(): \DateTime
    {
        return $this->lastUpdated;
    }
}
