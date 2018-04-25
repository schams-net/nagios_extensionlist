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
 * Model: Accesshistory
 */
class Accesshistory extends AbstractEntity
{
    /**
     * @var string
     */
    protected $remoteAddress = '';

    /**
     * @var string
     */
    protected $xForwardedFor = '';

    /**
     * @var string
     */
    protected $countryCode = '';

    /**
     * @var string
     */
    protected $nagiosPluginVersion = '';

    /**
     * @var string
     */
    protected $nagiosVersion = '';

    /**
     * @var string
     */
    protected $useragent = '';

    /**
     * @var string
     */
    protected $request = '';

    /**
     * @var \DateTime
     */
    protected $crDate = 0;

    /**
     * @param string $remoteAddress
     * @return void
     */
    public function setRemoteAddress($remoteAddress)
    {
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * @return string
     */
    public function getRemoteAddress()
    {
        return $this->remoteAddress;
    }

    /**
     * @param string $xForwardedFor
     * @return void
     */
    public function setXForwardedFor($xForwardedFor)
    {
        $this->xForwardedFor = $xForwardedFor;
    }

    /**
     * @return string
     */
    public function getXForwardedFor()
    {
        return $this->xForwardedFor;
    }

    /**
     * @param string $countryCode
     * @return void
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $nagiosPluginVersion
     * @return void
     */
    public function setNagiosPluginVersion($nagiosPluginVersion)
    {
        $this->nagiosPluginVersion = $nagiosPluginVersion;
    }

    /**
     * @return string
     */
    public function getNagiosPluginVersion()
    {
        return $this->nagiosPluginVersion;
    }

    /**
     * @param string $nagiosVersion
     * @return void
     */
    public function setNagiosVersion($nagiosVersion)
    {
        $this->nagiosVersion = $nagiosVersion;
    }

    /**
     * @return string
     */
    public function getNagiosVersion()
    {
        return $this->nagiosVersion;
    }

    /**
     * @param string $useragent
     * @return void
     */
    public function setUseragent($useragent)
    {
        $this->useragent = $useragent;
    }

    /**
     * @return string
     */
    public function getUseragent()
    {
        return $this->useragent;
    }

    /**
     * @param \DateTime $crDate
     * @return void
     */
    public function setCrDate($crDate)
    {
        $this->crDate = $crDate;
    }

    /**
     * @return \DateTime
     */
    public function getCrDate()
    {
        return $this->crDate;
    }

    /**
     * @param string $request
     * @return void
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }
}
