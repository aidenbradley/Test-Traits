<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\InstallsModules;

class InstallsModulesTest extends KernelTestBase
{
    use InstallsModules;

    /** @test */
    public function installs_dependencies(): void
    {
        $moduleHandler = $this->container->get('module_handler');

        $expectedDependencies = [
            'system',
            'link',
            'text',
            'file',
            'image',
        ];

        foreach ($expectedDependencies as $dependency) {
            $this->assertFalse($moduleHandler->moduleExists($dependency));
        }

        $this->installModuleWithDependencies('test_traits_dependencies');

        foreach ($expectedDependencies as $dependency) {
            $this->assertTrue($moduleHandler->moduleExists($dependency));
        }
    }
}
