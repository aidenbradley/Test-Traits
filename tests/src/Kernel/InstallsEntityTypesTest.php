<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\InstallsEntityTypes;

class InstallsEntityTypesTest extends KernelTestBase
{
    use InstallsEntityTypes;

    protected static $modules = [
        'system',
        'node',
        'user',
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->installEntitySchema('user');

        $this->setConfigDirectory(__DIR__ . '/__fixtures__/config/sync/node/bundles');
    }

    /** @test */
    public function install_bundle(): void
    {
        $this->installEntitySchema('node');

        $nodeTypeStorage = $this->container->get('entity_type.manager')->getStorage('node_type');

        $this->assertEmpty($nodeTypeStorage->loadMultiple());

        $this->installBundles('node', 'page');

        $nodeTypes = $nodeTypeStorage->loadMultiple();

        $this->assertNotEmpty($nodeTypes);

        $pageNodeType = reset($nodeTypes);

        $this->assertEquals('page', $pageNodeType->id());
    }

    /** @test */
    public function install_bundles(): void
    {
        $this->installEntitySchema('node');

        $nodeTypeStorage = $this->container->get('entity_type.manager')->getStorage('node_type');

        $this->assertEmpty($nodeTypeStorage->loadMultiple());

        $bundlesToInstall = [
            'page',
            'news',
        ];

        $this->installBundles('node', $bundlesToInstall);

        $nodeTypes = $nodeTypeStorage->loadMultiple();

        $this->assertNotEmpty($nodeTypes);

        $nodeTypeIds = array_map(function(NodeType $nodeType) {
            return $nodeType->id();
        }, $nodeTypes);

        $this->assertEquals($bundlesToInstall, array_values($nodeTypeIds));
    }

    /** @test */
    public function install_entity_schema_with_bundles(): void
    {
        $entityTypeManager = $this->container->get('entity_type.manager');

        $nodeEntityTypeDefinition = $entityTypeManager->getDefinition('node');

        $this->assertFalse($this->container->get('database')->schema()->tableExists(
            $nodeEntityTypeDefinition->getDataTable()
        ));

        $this->assertEmpty($entityTypeManager->getStorage('node_type')->loadMultiple());

        $bundlesToInstall = [
            'page',
            'news',
        ];

        $this->installEntitySchemaWithBundles('node', $bundlesToInstall);

        $this->assertTrue($this->container->get('database')->schema()->tableExists(
            $nodeEntityTypeDefinition->getDataTable()
        ));

        $nodeTypeIds = array_map(function(NodeType $nodeType) {
            return $nodeType->id();
        }, $entityTypeManager->getStorage('node_type')->loadMultiple());

        $this->assertEquals($bundlesToInstall, array_values($nodeTypeIds));
    }
}
