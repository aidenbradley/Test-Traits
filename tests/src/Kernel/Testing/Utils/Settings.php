<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Utils;

use Drupal\Tests\test_traits\Kernel\Testing\Exceptions\SettingsFailed;

class Settings
{
    /** @var array */
    private $settings = [];

    public static function create(array $settings): self
    {
        return new self($settings);
    }

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function configOutsideDocroot(): bool
    {
        return str_contains($this->getSetting('config_sync_directory'), '../') !== false;
    }

    /** @return mixed */
    private function getSetting(string $setting)
    {
        if (isset($this->settings[$setting]) === false) {
            throw SettingsFailed::settingsDoesNotExist($setting);
        }

        return $this->settings[$setting];
    }

    public function getConfigSyncDirectory(): string
    {
        $strippedDirectoryLocation = str_replace('../', '', $this->getSetting('config_sync_directory'));

        return ltrim($strippedDirectoryLocation, '/');
    }
}
