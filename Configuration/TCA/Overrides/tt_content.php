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

call_user_func(function () {
    // Register the plugin
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'NagiosExtensionlist',
        'Extensionlist',
        'Nagios Extensionlist',
        //'EXT:t3rrific_testdrive/Resources/Public/Icons/Extension.png'
        'content-form'
    );
});
