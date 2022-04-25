<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\InstallsFields;

class InstallsFieldsTest extends KernelTestBase
{
    use InstallsFields;

    protected static $modules = [
        'system',
        'node',
        'user',
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->installEntitySchema('user');
        $this->installEntitySchema('node');
    }

    /** @test */
    public function installing_field_sets_up_dependencies(): void
    {
        $this->assertFalse($this->container->get('module_handler')->moduleExists('field'));

        $this->enableModules([
            'text',
        ]);

        $this->setConfigDirectory(__DIR__ . '/__fixtures__/config/sync/node/bundles');
        $this->installBundles('node', 'page');

        $this->setConfigDirectory(__DIR__ . '/__fixtures__/config/sync/node/fields');

        $nodeStorage = $this->container->get('entity_type.manager')->getStorage('node');

        $node = $nodeStorage->create([
            'nid' => 1,
            'type' => 'page',
            'title' => 'Node',
        ]);
        $node->save();

        $this->installField('body', 'node', 'page');

        $node = $nodeStorage->load(1);

        $this->assertTrue($node->hasField('body'));

        $this->assertTrue($this->container->get('module_handler')->moduleExists('field'));
    }

    /** @test */
    public function install_single_field(): void
    {
        $this->enableModules([
            'text',
        ]);

        $this->setConfigDirectory(__DIR__ . '/__fixtures__/config/sync/node/bundles');
        $this->installBundles('node', 'page');

        $this->setConfigDirectory(__DIR__ . '/__fixtures__/config/sync/node/fields');

        $nodeStorage = $this->container->get('entity_type.manager')->getStorage('node');

        $node = $nodeStorage->create([
            'nid' => 1,
            'type' => 'page',
            'title' => 'Node',
        ]);
        $node->save();

        $this->assertFalse($node->hasField('body'));

        $this->installField('body', 'node', 'page');

        $node = $nodeStorage->load(1);

        $this->assertTrue($node->hasField('body'));
    }

    /** @test */
    public function install_multiple_fields(): void
    {
        $this->enableModules([
            'text',
        ]);

        $this->setConfigDirectory(__DIR__ . '/__fixtures__/config/sync/node/bundles');
        $this->installBundles('node', 'page');

        $this->setConfigDirectory(__DIR__ . '/__fixtures__/config/sync/node/fields');

        $nodeStorage = $this->container->get('entity_type.manager')->getStorage('node');

        $node = $nodeStorage->create([
            'nid' => 1,
            'type' => 'page',
            'title' => 'Node',
        ]);
        $node->save();

        $this->assertFalse($node->hasField('body'));
        $this->assertFalse($node->hasField('field_boolean'));

        $this->installFields([
            'body',
            'field_boolean_field',
        ], 'node', 'page');

        $node = $nodeStorage->load(1);

        $this->assertTrue($node->hasField('body'));
        $this->assertTrue($node->hasField('field_boolean_field'));
    }

    /** @test */
    public function install_all_fields_for_entity(): void
    {
        $this->enableModules([
            'text',
        ]);

        $this->setConfigDirectory(__DIR__ . '/__fixtures__/config/sync/node/bundles');
        $this->installBundles('node', 'page');

        $this->setConfigDirectory(__DIR__ . '/__fixtures__/config/sync/node/fields');

        $nodeStorage = $this->container->get('entity_type.manager')->getStorage('node');

        $node = $nodeStorage->create([
            'nid' => 1,
            'type' => 'page',
            'title' => 'Node',
        ]);
        $node->save();

        $this->assertFalse($node->hasField('body'));
        $this->assertFalse($node->hasField('field_boolean'));

        $this->installAllFieldsForEntity('node', 'page');

        $node = $nodeStorage->load(1);

        $this->assertTrue($node->hasField('body'));
        $this->assertTrue($node->hasField('field_boolean_field'));
    }
}
