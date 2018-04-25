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

$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY);
$pluginName = strtolower($extensionName);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'SchamsNet.' . $_EXTKEY,
    $pluginName,
    array(
        'Extensionlist' => 'listInsecureExtensions'
    ),
    array(
        'Extensionlist' => 'listInsecureExtensions',
    )
);

$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY);
$typoScript = $extensionPath . 'Configuration/TypoScript/setup.txt';

#if (is_readable($typoScript)) {
#   \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
#      file_get_contents($typoScript)
#    );
#}

// logging
$logging = array(
    \TYPO3\CMS\Core\Log\LogLevel::INFO => array(
        'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => array(
            'logFile' => 'typo3temp/logs/' . date('Ymd') . '.' . $extensionName . '.log'
        )
    )
);

// ...
$GLOBALS['TYPO3_CONF_VARS']['LOG']['SchamsNet'][$extensionName]['Controller']['writerConfiguration'] = $logging;

unset($logging);