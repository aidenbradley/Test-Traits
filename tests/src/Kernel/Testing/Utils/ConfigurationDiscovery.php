<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Utils;

use Drupal\Tests\test_traits\Kernel\Testing\Exceptions\ConfigInstallFailed;

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
        $settings = $this->temporarilyIgnoreErrors(function() {
           return Settings::create($this->appRoot);
        });

        $root = $this->appRoot;

        if($settings->configOutsideDocroot()) {
            $rootParts = explode('/', $root);

            unset($rootParts[count($rootParts) - 1]);

            $root = implode('/', $rootParts);
        }

        return $root . '/' . $settings->getConfigSyncDirectory();
    }

    /** @return mixed */
    private function temporarilyIgnoreErrors(callable $callback)
    {
        $currentErrorReportingLevel = error_reporting();

        error_reporting(E_ALL & ~E_NOTICE);

        $result = $callback();

        error_reporting($currentErrorReportingLevel);

        return $result;
    }
}
