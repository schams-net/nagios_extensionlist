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

$extensionKey = 'nagios_extensionlist';
$extensionPath = ExtensionManagementUtility::extPath($extensionKey);
$tableName = 'tx_nagiosextensionlist_domain_model_accesshistory';
$tableNameSimplified = 'tx_nagiosextensionslist_accesshistory';
$languageFile = $extensionKey . '/Resources/Private/Language/locallang.db.xlf';

return array(
    'ctrl' => array(
        'title' => 'LLL:EXT:' . $languageFile . ':' . $tableNameSimplified . '.title',
        'label' => 'remote_address',
        'label_alt' => 'x_forwarded_for',
        'tstamp' => 'tstamp',
        'cruser_id' => 'cruser_id',
        'crdate' => 'crdate',
        'default_sortby' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden'
        ),
        'readonly' => 1,
        'searchFields' => 'remove_address,x_forwarded_for',
        'dynamicConfigFile' => $extensionPath . 'Configuration/TCA/' . $tableNameSimplified . '.php',
        'iconfile' => 'EXT:' . $extensionKey . '/Resources/Public/Icons/' . $tableName . '.png'
    ),
    'interface' => array(
        'showRecordFieldList' => implode(
            ',',
            array(
                'hidden',
                'remote_address',
                'x_forwarded_for',
                'country_code',
                'nagios_plugin_version',
                'nagios_version',
                'useragent',
                'request'
            )
        )
    ),
    'types' => array(
        '0' => array(
            'showitem' => implode(
                ',',
                array(
                    'hidden;;1;;1-1-1',
                    'remote_address',
                    'x_forwarded_for',
                    'country_code',
                    'nagios_plugin_version',
                    'nagios_version',
                    'useragent',
                    'request'
                )
            )
        )
    ),
    'palettes' => array(
        '1' => array(
            'showitem' => ''
        )
    ),
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config'  => array(
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'remote_address' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:' . $languageFile . ':' . $tableNameSimplified . '.remote_address',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '250',
                'eval' => 'required,trim',
            )
        ),
        'x_forwarded_for' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:' . $languageFile . ':' . $tableNameSimplified . '.x_forwarded_for',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '250',
                'eval' => 'required,trim',
            )
        ),
        'country_code' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:' . $languageFile . ':' . $tableNameSimplified . '.country_code',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '250',
                'eval' => 'required,trim',
            )
        ),
        'nagios_plugin_version' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:' . $languageFile . ':' . $tableNameSimplified . '.nagios_plugin_version',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '250',
                'eval' => 'required,trim',
            )
        ),
        'nagios_version' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:' . $languageFile . ':' . $tableNameSimplified . '.nagios_version',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '250',
                'eval' => 'required,trim',
            )
        ),
        'useragent' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:' . $languageFile . ':' . $tableNameSimplified . '.useragent',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '250',
                'eval' => 'required,trim',
            )
        ),
        'request' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:' . $languageFile . ':' . $tableNameSimplified . '.request',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '250',
                'eval' => 'required,trim',
            )
        ),
    )
);
