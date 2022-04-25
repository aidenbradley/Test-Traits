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

        $this->assertInstanceOf(NodeType::class, $nodeTypeStorage->load('page'));
    }

    /** @test */
    public function install_bundles(): void
    {
        $this->installEntitySchema('node');

        $nodeTypeStorage = $this->container->get('entity_type.manager')->getStorage('node_type');

        $this->assertEmpty($nodeTypeStorage->loadMultiple());

        $this->installBundles('node', [
            'page',
            'news',
        ]);

        $this->assertInstanceOf(NodeType::class, $nodeTypeStorage->load('page'));
        $this->assertInstanceOf(NodeType::class, $nodeTypeStorage->load('news'));
    }

    /** @test */
    public function install_entity_schema_with_bundles(): void
    {
        $entityTypeManager = $this->container->get('entity_type.manager');

        $nodeEntityTypeDefinition = $entityTypeManager->getDefinition('node');

        $this->assertFalse($this->container->get('database')->schema()->tableExists(
            $nodeEntityTypeDefinition->getDataTable()
        ));

        $nodeTypeStorage = $entityTypeManager->getStorage('node_type');

        $this->assertEmpty($nodeTypeStorage->loadMultiple());

        $this->installEntitySchemaWithBundles('node', [
            'page',
            'news',
        ]);

        $this->assertTrue($this->container->get('database')->schema()->tableExists(
            $nodeEntityTypeDefinition->getDataTable()
        ));

        $this->assertInstanceOf(NodeType::class, $nodeTypeStorage->load('page'));
        $this->assertInstanceOf(NodeType::class, $nodeTypeStorage->load('news'));
    }
}
