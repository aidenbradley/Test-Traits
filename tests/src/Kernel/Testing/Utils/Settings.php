<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Utils;

use Drupal\Tests\test_traits\Kernel\Testing\Exceptions\ConfigInstallFailed;

class Settings
{
    /** @var string */
    private $settingsLocation = '/sites/default/settings.php';

    /** @var array */
    private $siteSettings;

    public static function create(string $appRoot): self
    {
        return (new self())->setSettings($appRoot);
    }

    public function configOutsideDocroot(): bool
    {
        if (isset($this->siteSettings['config_sync_directory']) === false) {
            throw ConfigInstallFailed::directoryDoesNotExist();
        }

        return str_contains($this->siteSettings['config_sync_directory'], '../') !== false;
    }

    public function getSettings(): array
    {
        return $this->siteSettings;
    }

    public function getConfigSyncDirectory(): string
    {
        $strippedDirectoryLocation = str_replace('../', '', $this->siteSettings['config_sync_directory']);

        return ltrim($strippedDirectoryLocation, '/');
    }

    private function setSettings(string $appRoot): self
    {
        $settings = [];

        require $appRoot . '/' . ltrim($this->settingsLocation, '/');

        $this->siteSettings = $settings;

        return $this;
    }
}
