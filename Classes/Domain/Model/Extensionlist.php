<?php
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
     * @var integer
     */
    protected $integerVersion = 0;

    /**
     * @var integer
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
    public function setExtensionKey($extensionKey)
    {
        $this->extensionKey = $extensionKey;
    }

    /**
     * @return string
     */
    public function getExtensionKey()
    {
        return $this->extensionKey;
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle($title)
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
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return integer
     */
    public function getIntegerVersion()
    {
        return $this->integerVersion;
    }

    /**
     * @param integer $reviewState
     * @return void
     */
    public function setReviewState($reviewState)
    {
        $this->reviewState = $reviewState;
    }

    /**
     * @return integer
     */
    public function getReviewState()
    {
        return $this->reviewState;
    }

    /**
     * @param \DateTime $lastUpdated
     * @return void
     */
    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }
}
