<?php

namespace Drupal\Tests\test_traits\Kernel\Base;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Concerns\InstallsModules;

abstract class EnableModuleKernelTest extends KernelTestBase
{
    use InstallsModules;

    /** @var array */
    private $entityTypeDefinitionsPreInstall;

    protected function setUp()
    {
        parent::setUp();

        $this->entityTypeDefinitionsPreInstall = array_keys(
            $this->container->get('entity_type.manager')->getDefinitions()
        );
    }

    abstract public function module(): string;

    /** @test */
    public function install_module_definitions(): void
    {
        $this->installModuleWithDependencies($this->module());

        $moduleEntityDefinitions = array_diff(
            array_keys($this->container->get('entity_type.manager')->getDefinitions()),
            $this->entityTypeDefinitionsPreInstall,
        );

        foreach($moduleEntityDefinitions as $moduleEntityDefinition) {
            $this->installEntitySchema($moduleEntityDefinition);
        }
    }
}
