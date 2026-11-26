<?php

declare(strict_types=1);

namespace SchamsNet\NagiosExtensionlist\Command;

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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Fetches the configuration file generated on-the-fly by the frontend plugin of this extension
 * and stores it as a static file in a local file storage (sys_file_storage), e.g. fileadmin/nagios/check_typo3.cfg.
 * The web server can then deliver the file without TYPO3 - even if TYPO3 is currently broken.
 *
 * The file is written directly (not via FAL), as FAL may reject the file extension ".cfg"
 * (see $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['security.system.enforceAllowedFileExtensions']).
 *
 * The file is only rewritten if the date of the latest extension upload has changed, or if the
 * existing file is older than --max-age hours.
 */
class WriteConfigurationFileCommand extends Command
{
    private const EXTENSION_KEY = 'nagios_extensionlist';
    private const DEFAULT_SOURCE_PATH = 'check_typo3.txt';
    private const DEFAULT_TARGET_FILE = 'nagios/check_typo3.cfg';
    private const DEFAULT_MAX_AGE_HOURS = 24;
    private const TIMESTAMP_PATTERN = '/^extensionlist\.count\.latest-extension-upload\.timestamp\s*=\s*(\d+)\s*$/m';

    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly SiteFinder $siteFinder,
        private readonly StorageRepository $storageRepository,
        private readonly ExtensionConfiguration $extensionConfiguration
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'max-age',
                'm',
                InputOption::VALUE_REQUIRED,
                'Rewrite the file after this many hours at the latest, even if the extension list did not change',
                (string)self::DEFAULT_MAX_AGE_HOURS
            )
            ->addOption(
                'url',
                'u',
                InputOption::VALUE_REQUIRED,
                'URL to fetch the configuration from (default: base of the configured site + configured path)'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Always rewrite the file'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $maxAgeHours = (int)$input->getOption('max-age');
        if ($maxAgeHours < 1) {
            $io->error('--max-age must be a positive number of hours.');
            return Command::INVALID;
        }

        try {
            $targetFile = $this->getTargetFile();
            $url = (string)($input->getOption('url') ?: $this->getDefaultUrl());
        } catch (\Throwable $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        $io->writeln('Fetching ' . $url);
        try {
            $response = $this->requestFactory->request($url, 'GET', [
                'http_errors' => false,
                'timeout' => 60,
                'headers' => ['Cache-Control' => 'no-cache'],
            ]);
        } catch (\Throwable $e) {
            $io->error('Request failed, existing file is kept: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $content = (string)$response->getBody();
        if ($response->getStatusCode() !== 200) {
            $io->error('Unexpected HTTP status ' . $response->getStatusCode() . ', existing file is kept.');
            return Command::FAILURE;
        }

        $newTimestamp = $this->extractTimestamp($content);
        if ($newTimestamp === null || !$this->isValidConfiguration($content)) {
            $io->error('Response is not a valid Nagios configuration, existing file is kept.');
            if ($output->isVerbose()) {
                $io->writeln(mb_substr($content, 0, 500));
            }
            return Command::FAILURE;
        }

        if (!$input->getOption('force') && is_file($targetFile)) {
            $existingTimestamp = $this->extractTimestamp((string)file_get_contents($targetFile));
            $age = time() - (int)filemtime($targetFile);
            if ($existingTimestamp === $newTimestamp && $age < $maxAgeHours * 3600) {
                $io->success(sprintf(
                    'Extension list unchanged (latest upload %s), file is %d minutes old - nothing to do.',
                    date('Y-m-d H:i', $newTimestamp),
                    intdiv($age, 60)
                ));
                return Command::SUCCESS;
            }
        }

        try {
            $this->writeFileAtomically($targetFile, $content);
        } catch (\Throwable $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        $io->success(sprintf(
            'Written %s (latest extension upload %s).',
            $targetFile,
            date('Y-m-d H:i', $newTimestamp)
        ));
        return Command::SUCCESS;
    }

    /**
     * Base of the configured site (first site, if none is configured) + configured path.
     * The base respects the base variants of the current application context.
     */
    private function getDefaultUrl(): string
    {
        $siteIdentifier = $this->getSetting('sourceSiteIdentifier');
        if ($siteIdentifier !== '') {
            $site = $this->siteFinder->getSiteByIdentifier($siteIdentifier);
        } else {
            $sites = $this->siteFinder->getAllSites();
            $site = reset($sites);
            if ($site === false) {
                throw new \RuntimeException('No site configured, use --url to define the source URL.', 1764150000);
            }
        }
        $path = ltrim($this->getSetting('sourcePath') ?: self::DEFAULT_SOURCE_PATH, '/');
        return rtrim((string)$site->getBase(), '/') . '/' . $path;
    }

    /**
     * Absolute path of the target file: base path of the configured local storage
     * (default storage, if none is configured) + configured path/filename within this storage.
     */
    private function getTargetFile(): string
    {
        $storageUid = (int)$this->getSetting('storageUid');
        $storage = $storageUid > 0
            ? $this->storageRepository->findByUid($storageUid)
            : $this->storageRepository->getDefaultStorage();
        if ($storage === null) {
            throw new \RuntimeException(
                $storageUid > 0 ? 'File storage ' . $storageUid . ' not found.' : 'No default file storage configured.',
                1764150004
            );
        }
        if ($storage->getDriverType() !== 'Local') {
            throw new \RuntimeException(
                'File storage ' . $storage->getUid() . ' ("' . $storage->getName() . '") is no local storage.',
                1764150005
            );
        }

        $storageConfiguration = $storage->getConfiguration();
        $basePath = rtrim((string)($storageConfiguration['basePath'] ?? ''), '/');
        if ($basePath === '') {
            throw new \RuntimeException('File storage ' . $storage->getUid() . ' has no base path.', 1764150006);
        }
        if (($storageConfiguration['pathType'] ?? '') === 'relative') {
            $basePath = Environment::getPublicPath() . '/' . $basePath;
        }
        if (!is_dir($basePath)) {
            throw new \RuntimeException('Base path "' . $basePath . '" of file storage ' . $storage->getUid() . ' does not exist.', 1764150007);
        }

        $filePath = trim($this->getSetting('configurationFile'), " \t\n\r\0\x0B/");
        if ($filePath === '') {
            $filePath = self::DEFAULT_TARGET_FILE;
        }
        if (!GeneralUtility::validPathStr($filePath)) {
            throw new \InvalidArgumentException('Invalid target path "' . $filePath . '"', 1764150001);
        }
        return $basePath . '/' . $filePath;
    }

    private function getSetting(string $name): string
    {
        try {
            return trim((string)$this->extensionConfiguration->get(self::EXTENSION_KEY, $name));
        } catch (\Throwable) {
            return '';
        }
    }

    private function extractTimestamp(string $content): ?int
    {
        return preg_match(self::TIMESTAMP_PATTERN, $content, $matches) ? (int)$matches[1] : null;
    }

    /**
     * Guards against TYPO3 error pages ("Oops, an error occurred!") and incomplete responses.
     */
    private function isValidConfiguration(string $content): bool
    {
        return preg_match('/^nagios-typo3-configuration\.id\s*=\s*\S+/m', $content) === 1
            && str_contains($content, '# End of file');
    }

    /**
     * Writes to a temporary file in the target directory and renames it, so the web server
     * never delivers a partially written file.
     */
    private function writeFileAtomically(string $targetFile, string $content): void
    {
        $directory = dirname($targetFile);
        if (!is_dir($directory)) {
            GeneralUtility::mkdir_deep($directory);
        }
        $tempFile = $directory . '/.' . basename($targetFile) . '.' . bin2hex(random_bytes(4)) . '.tmp';
        if (file_put_contents($tempFile, $content) !== strlen($content)) {
            @unlink($tempFile);
            throw new \RuntimeException('Could not write temporary file ' . $tempFile, 1764150002);
        }
        GeneralUtility::fixPermissions($tempFile);
        if (!rename($tempFile, $targetFile)) {
            @unlink($tempFile);
            throw new \RuntimeException('Could not move temporary file to ' . $targetFile, 1764150003);
        }
    }
}
