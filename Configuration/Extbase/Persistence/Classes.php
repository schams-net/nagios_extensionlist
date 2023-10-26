<?php

declare(strict_types=1);

namespace SchamsNet\NagiosExtensionlist;

use SchamsNet\NagiosExtensionlist\Domain\Model\Extensionlist;

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
return [
    Extensionlist::class => [
        'tableName' => 'tx_extensionmanager_domain_model_extension',
    ],
];
