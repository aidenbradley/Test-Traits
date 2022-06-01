<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Exceptions;

class SettingsFailed extends \Exception
{
    public const SETTINGS_DOES_NOT_EXIST = 1;

    public static function settingsDoesNotExist(string $setting): self
    {
        return new self(
            'Settings `' . $setting . '` does not exist in settings.php',
            self::SETTINGS_DOES_NOT_EXIST
        );
    }
}
