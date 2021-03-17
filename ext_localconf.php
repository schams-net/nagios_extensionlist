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

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(
    function () {
        // Register frontend plugin
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'NagiosExtensionlist',
            'Extensionlist',
            [
                \SchamsNet\NagiosExtensionlist\Controller\ExtensionlistController::class => 'listInsecureExtensions',
            ],
            // non-cacheable actions
            [
                \SchamsNet\NagiosExtensionlist\Controller\ExtensionlistController::class => 'listInsecureExtensions',
            ],
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
        );
    }
);
