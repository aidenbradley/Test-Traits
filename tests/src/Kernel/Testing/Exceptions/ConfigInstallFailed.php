<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Exceptions;

class ConfigInstallFailed extends \Exception
{
    public const CONFIGURATION_DOES_NOT_EXIST = 1;

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
