<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Exceptions;

class ConfigInstallFailed extends \Exception
{
    public const CONFIGURATION_DOES_NOT_EXIST = 1;

    public const DIRECTORY_DOES_NOT_EXIST = 2;

    /** @var string */
    private $failingConfigFile = '';

    /** @return static */
    public static function doesNotExist(string $configFile)
    {
        $exception = new static(
            'Configuration file ' . $configFile . ' does not exist',
            self::CONFIGURATION_DOES_NOT_EXIST
        );

        return $exception->setFailingConfigFile($configFile);
    }

    /** @return static */
    public static function directoryDoesNotExist(string $settingsFileLocation)
    {
        return new static(
            'The `config_sync_directory` setting does not exist in ' . $settingsFileLocation,
            self::DIRECTORY_DOES_NOT_EXIST
        );
    }

    public function getFailingConfigFile(): string
    {
        return $this->failingConfigFile;
    }

    private function setFailingConfigFile(string $configFile): self
    {
        $this->failingConfigFile = $configFile;

        return $this;
    }
}
