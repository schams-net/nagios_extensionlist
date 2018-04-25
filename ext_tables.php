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

//$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY);
$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY);
$pluginName = strtolower($extensionName);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'SchamsNet.' . $_EXTKEY,
    $pluginName,
    'Nagios Extensionlist'
);

/*
$pluginSignature = str_replace('_', '', $_EXTKEY) . '_help';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
*/

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
	$_EXTKEY, 'Configuration/TypoScript',
	'Nagios Extensionlist'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
	'tx_nagiosextensionlist_domain_model_accesshistory',
	'EXT:nagios_extensionlist/Resources/Private/Language/locallang_csh_tx_nagiosextensionlist_domain_model_accesshistory.xlf'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
	'tx_nagiosextensionlist_domain_model_accesshistory'
);
