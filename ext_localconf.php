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

use \TYPO3\CMS\Core\Log\LogLevel;
use \TYPO3\CMS\Core\Log\Writer\FileWriter;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Utility\ExtensionUtility;

$extensionName = GeneralUtility::underscoredToUpperCamelCase($_EXTKEY);
$pluginName = strtolower($extensionName);

ExtensionUtility::configurePlugin(
    'SchamsNet.' . $_EXTKEY,
    $pluginName,
    array(
        'Extensionlist' => 'listInsecureExtensions'
    ),
    array(
        'Extensionlist' => 'listInsecureExtensions',
    )
);

// logging
$logging = array(
    LogLevel::INFO => array(
        'TYPO3\CMS\Core\Log\Writer\FileWriter' => array(
            'logFile' => 'typo3temp/logs/' . date('Ymd') . '.' . $extensionName . '.log'
        )
    )
);

// ...
$GLOBALS['TYPO3_CONF_VARS']['LOG']['SchamsNet'][$extensionName]['Controller']['writerConfiguration'] = $logging;

unset($logging);
