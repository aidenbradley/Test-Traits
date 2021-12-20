<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Concerns\InstallsExportedConfig;
use Drupal\Tests\test_traits\Kernel\Exceptions\ConfigInstallFailed;

class InstallsExportedConfigTest extends KernelTestBase
{
    use InstallsExportedConfig {
        configDirectory as InstallsExportedConfigDirectory;
    }

    protected static $modules = [
        'test_traits',
        'system',
        'node',
        'user',
    ];

    /** @var bool */
    private $useVfsConfigDirectory = false;

    /** @test */
    public function throws_exception_for_bad_config(): void
    {
        $this->useVfsConfigDirectory = true;

        $this->installEntitySchema('node');

        $this->expectException(ConfigInstallFailed::class);
        $this->expectExceptionCode(ConfigInstallFailed::CONFIGURATION_DOES_NOT_EXIST);

        $this->installExportedConfig('node.type.page');
    }

    /** @test */
    public function installs_config(): void
    {
        $nodeTypeStorage = $this->container->get('entity_type.manager')->getStorage('node_type');

        $this->assertEmpty($nodeTypeStorage->loadMultiple());

        $this->installExportedConfig('node.type.page');

        $nodeTypes = $nodeTypeStorage->loadMultiple();

        $this->assertNotEmpty($nodeTypes);

        $pageNodeType = reset($nodeTypes);

        $this->assertEquals('page', $pageNodeType->id());
    }

    public function configDirectory(): string
    {
        if ($this->useVfsConfigDirectory) {
            return $this->InstallsExportedConfigDirectory();
        }

        // providing our own directory with config we can test against
        return __DIR__ . '/__fixtures__/config/sync';
    }
}
