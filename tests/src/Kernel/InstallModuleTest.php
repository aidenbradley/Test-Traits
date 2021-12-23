<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Tests\test_traits\Kernel\Base\EnableModuleKernelTest;

class InstallModuleTest extends EnableModuleKernelTest
{
    public function module(): string
    {
        return 'test_traits_entity_install';
    }

    /**
     * @test
     *
     * The TestInstallEntity declares a base field definition of type "text".
     * Since this plugin definition exists in the "text" module then the
     * install will fail when installing the entity schema definition.
     * The "text" module must be declared as a dependency.
     */
    public function module_and_entity_schema_install_fails_without_correct_dependencies(): void
    {
        $this->enableModules([
            $this->module(),
        ]);

        try {
            $this->installEntitySchema('test_install_entity');

            $this->fail('The entity schema should fail to install. Read the test comment for more information');
        } catch (PluginNotFoundException $exception) {
            $this->assertTrue(
                str_contains($exception->getMessage(), 'The "text" plugin does not exist')
            );
        }

        $this->installModuleWithDependencies($this->module());

        try {
            $this->installEntitySchema('test_install_entity');
        } catch (PluginNotFoundException $exception) {
            $this->fail('The entity schema should correctly install. Read the test comment for more information');
        }
    }
}
