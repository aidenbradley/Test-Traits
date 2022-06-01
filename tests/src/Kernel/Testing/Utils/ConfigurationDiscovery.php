<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Utils;

class ConfigurationDiscovery
{
    /** @var string */
    private $appRoot;

    /** @var string */
    private $settingsLocation = '/sites/default/settings.php';

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
        /** @var Settings $settings */
        $settings = $this->temporarilySupressErrors(function() {
            return $this->loadSettings();
        });

        $root = $this->when($settings->configOutsideDocroot(), function() {
            $rootParts = explode('/', $this->appRoot);

            unset($rootParts[count($rootParts) - 1]);

            return implode('/', $rootParts);
        }, $this->appRoot);

        return $root . '/' . $settings->getConfigSyncDirectory();
    }

    private function loadSettings(): Settings
    {
        $settings = [];

        require $this->appRoot . '/' . ltrim($this->settingsLocation, '/');

        return Settings::create($settings);
    }

    /** @return mixed */
    private function temporarilySupressErrors(callable $callback)
    {
        $currentErrorReportingLevel = error_reporting();

        error_reporting(E_ALL & ~E_NOTICE);

        $result = $callback();

        error_reporting($currentErrorReportingLevel);

        return $result;
    }

    /**
     * @param mixed $value
     * @param mixed $default
     * @return $this|mixed
     */
    public function when($value, callable $callback, $default = null)
    {
        if ($value) {
            return $callback($value);
        }

        return $default;
    }
}
