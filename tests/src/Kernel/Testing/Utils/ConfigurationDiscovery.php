<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Utils;

use Drupal\Core\Site\Settings;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ConfigurationDiscovery
{
    /** @var bool */
    public $autoDiscoverConfigDirectory = false;

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
            if ($this->autoDiscoverConfigDirectory) {
                return $this->loadSettingsFromFinder();
            }

            return $this->loadSettings();
        });

        return $this->appRoot . '/' . ltrim($settings->get('config_sync_directory'), '/');
    }

    private function loadSettings()
    {
        $settings = [];

        require $this->appRoot . '/' . ltrim($this->settingsLocation, '/');

        return new Settings($settings);
    }

    /** Added method incase the location of settings.php changes in future Drupal versions */
    private function loadSettingsFromFinder(): array
    {
        $settings = [];

        $finder = Finder::create()
            ->ignoreUnreadableDirs()
            ->ignoreDotFiles(true)
            ->name('settings.php')
            ->filter(function(SplFileInfo $file) {
                return str_contains($file->getPathname(), 'simpletest') === false;
            })
            ->in($this->appRoot);

        foreach ($finder as $directory) {
            require $directory->getPathname();
        }

        return $settings;
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
}
