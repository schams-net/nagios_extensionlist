<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace SchamsNet\NagiosExtensionlist\Service\Exception;

use TYPO3\CMS\Core\Exception as CoreException;

/**
 * An exception thrown if fetching a resource from a remote server fails
 */
class RemoteFetchException extends CoreException {}
