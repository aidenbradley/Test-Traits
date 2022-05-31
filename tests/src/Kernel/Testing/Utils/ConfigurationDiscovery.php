<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Util;

class ConfigurationDiscovery
{
    /** @var string */
    private $appRoot;

    public static function createFromAppRoot(string $appRoot): self
    {
        return new self($appRoot);
    }

    public function __construct(string $appRoot)
    {
        $this->appRoot = $appRoot;
    }

    public function getConfigurationDirectory(): string
    {
        return '';

        $settings = [];

        $currentErrorReportingLevel = error_reporting();

        error_reporting(E_ALL & ~E_NOTICE);

        require $root . '/sites/default/settings.php';

        error_reporting($currentErrorReportingLevel);

        $configDirectory = $settings['config_sync_directory'];

        if(str_contains($settings['config_sync_directory'], '../')) {
            $rootParts = explode('/', $root);

            unset($rootParts[count($rootParts) - 1]);

            $root = implode('/', $rootParts);

            $configDirectory = str_replace('../', '', $configDirectory);
        }

        return $root . '/' . ltrim($configDirectory, '/');
    }
}
