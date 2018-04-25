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

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Utility\ExtensionUtility;

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$extensionName = GeneralUtility::underscoredToUpperCamelCase($_EXTKEY);
$pluginName = strtolower($extensionName);

$languageFile = 'EXT:' . $_EXTKEY . '/Resources/Private/Language/';
$languageFile.= 'locallang_csh_tx_nagiosextensionlist_domain_model_accesshistory.xlf';

ExtensionUtility::registerPlugin(
    'SchamsNet.' . $_EXTKEY,
    $pluginName,
    'Nagios Extensionlist'
);

ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript',
    'Nagios Extensionlist'
);

ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_nagiosextensionlist_domain_model_accesshistory',
    $languageFile
);

ExtensionManagementUtility::allowTableOnStandardPages(
    'tx_nagiosextensionlist_domain_model_accesshistory'
);
