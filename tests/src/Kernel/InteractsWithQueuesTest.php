<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\Tests\test_traits\Kernel\Concerns\InteractsWithQueues;

class InteractsWithQueuesTest extends KernelTestBase
{
    use InteractsWithQueues;

    /** @test */
    public function add_to_queue(): void
    {
        $this->addToQueue('create_node_worker', [
            'title' => 'test title',
        ]);

        $this->assertEquals(1, $this->getQueue('create_node_worker')->numberOfItems());
    }

    /** @test */
    public function process_queue()
    {
        $this->enableModules([
            'node',
            'user',
            'test_traits_queue',
        ]);
        $this->installEntitySchema('node');
        $this->installEntitySchema('user');

        $nodeStorage = $this->container->get('entity_type.manager')->getStorage('node');

        $this->assertEmpty($nodeStorage->loadMultiple());

        $this->addToQueue('create_node_worker', [
            'title' => 'test title',
        ]);

        $this->processQueue('create_node_worker');

        $this->assertNotEmpty($nodeStorage->loadMultiple());

        $node = $nodeStorage->loadByProperties([
            'title' => 'test title',
        ]);

        $this->assertInstanceOf(Node::class, reset($node));
    }
}
