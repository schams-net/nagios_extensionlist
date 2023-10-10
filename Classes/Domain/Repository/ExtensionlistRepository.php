<?php
declare(strict_types=1);
namespace SchamsNet\NagiosExtensionlist\Domain\Repository;

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

use SchamsNet\NagiosExtensionlist\Domain\Repository\Traits\ExtensionlistTrait;
use \TYPO3\CMS\Extbase\Persistence\QueryInterface;
use \TYPO3\CMS\Extbase\Persistence\Repository;
use \TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Repository: Extensionlist
 */
class ExtensionlistRepository extends Repository
{
    use ExtensionlistTrait;

    /**
     * Set default ordering for the entire repository
     */
    protected $defaultOrderings = [
        'extension_key' => QueryInterface::ORDER_ASCENDING,
        'integer_version' => QueryInterface::ORDER_ASCENDING
    ];

    /**
     * Returns the last updated extension
     */
    public function findLastUpdatedExtension(): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->setOrderings(['last_updated' => QueryInterface::ORDER_DESCENDING]);
        $query->setLimit(1);
        return $query->execute();
    }
}
