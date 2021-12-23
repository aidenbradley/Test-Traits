<?php

namespace Drupal\Tests\test_traits\Kernel\Exceptions;

class ConfigInstallFailed extends \Exception
{
    public const CONFIGURATION_DOES_NOT_EXIST = 1;

    /** @return static */
    public static function doesNotExist(string $configFile)
    {
        return new static(
            'Configuration file ' . $configFile . ' does not exist',
            self::CONFIGURATION_DOES_NOT_EXIST
        );
    }
}
