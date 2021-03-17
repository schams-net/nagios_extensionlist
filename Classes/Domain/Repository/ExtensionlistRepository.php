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

use \TYPO3\CMS\Extbase\Persistence\QueryInterface;
use \TYPO3\CMS\Extbase\Persistence\Repository;
use \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;

/**
 * Repository: Extensionlist
 */
class ExtensionlistRepository extends Repository
{
    /**
     * Set default ordering for the entire repository
     *
     * @var array
     */
    protected $defaultOrderings = [
        'extension_key' => QueryInterface::ORDER_ASCENDING,
        'integer_version' => QueryInterface::ORDER_ASCENDING
    ];

    /**
     * Initialize
     *
     * @access public
     * @return void
     */
    public function initializeObject(): void
    {
        /** @var $querySettings Typo3QuerySettings */
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Returns the last updated extension
     *
     * @access public
     * @return QueryResult
     */
    public function findLastUpdatedExtension(): QueryResult
    {
        $query = $this->createQuery();
        $query->setOrderings(['last_updated' => QueryInterface::ORDER_DESCENDING]);
        $query->setLimit(1);
        return $query->execute();
    }
}
