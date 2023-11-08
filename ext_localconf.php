<?php

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

use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use SchamsNet\NagiosExtensionlist\Controller\ExtensionlistController;

defined('TYPO3') or die();

(function () {

    // register frontend plugin
    ExtensionUtility::configurePlugin(
        'NagiosExtensionlist',
        'Extensionlist',
        [ExtensionlistController::class => 'generateResponse'],
        [ExtensionlistController::class => 'generateResponse']
    );

    // configure the caching framework
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['nagios_extensionlist'] ??= [];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['nagios_extensionlist']['frontend'] ??= VariableFrontend::class;
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['nagios_extensionlist']['backend'] ??= FileBackend::class;
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['nagios_extensionlist']['options']['defaultLifetime'] ??= 14400;

})();
