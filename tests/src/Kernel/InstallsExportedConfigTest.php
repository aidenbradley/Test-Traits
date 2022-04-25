<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\image\Entity\ImageStyle;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\InstallsExportedConfig;
use Drupal\Tests\test_traits\Kernel\Testing\Exceptions\ConfigInstallFailed;
use Drupal\user\Entity\Role;

class InstallsExportedConfigTest extends KernelTestBase
{
    use InstallsExportedConfig {
        configDirectory as InstallsExportedConfigDirectory;
    }

    protected static $modules = [
        'system',
        'node',
        'user',
    ];

    /** @var string */
    private $customConfigDirectory;

    /** @test */
    public function throws_exception_for_bad_config(): void
    {
        $this->useVfsConfigDirectory();

        $this->installEntitySchema('node');

        try {
            $this->installExportedConfig('node.type.page');
        } catch (ConfigInstallFailed $exception) {
            $this->assertEquals(ConfigInstallFailed::CONFIGURATION_DOES_NOT_EXIST, $exception->getCode());
            $this->assertEquals('node.type.page', $exception->getFailingConfigFile());
        }
    }
}
