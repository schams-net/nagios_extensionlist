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

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Nagios Extensionlist',
    'description' => 'Generates a list of all extensions for EXT:nagios, based on the extension list in the current TYPO3 instance.',
    'category' => 'fe',
    'version' => '1.0.0',
    'state' => 'beta',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearcacheonload' => false,
    'author' => 'Michael Schams (schams.net)',
    'author_email' => 'schams.net',
    'author_company' => 'https://schams.net',
    'constraints' => array (
        'depends' => array (
            'php' => '5.5.0-7.0.99',
            'typo3' => '7.0.0-8.99.99',
        ),
        'conflicts' => array (
        ),
        'suggests' => array (
        ),
    ),
);
