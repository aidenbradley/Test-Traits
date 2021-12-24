<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\Core\Site\Settings;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\test_traits_queue\Queue\ReliableCreateNodeQueue;
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

        $nodes = $nodeStorage->loadByProperties([
            'title' => 'test title',
        ]);

        $node = reset($nodes);

        $this->assertInstanceOf(Node::class, $node);
        $this->assertEquals('test title', $node->title->value);
    }

    /** @test */
    public function reliable_queues(): void
    {
        $this->enableModules([
            'test_traits_queue',
        ]);

        $this->container->set('queue', $this->customQueueFactory());

        $this->assertInstanceOf(
            ReliableCreateNodeQueue::class,
            $this->useReliableQueues()->getQueue('create_node_worker')
        );
    }

    /** @return mixed */
    private function customQueueFactory()
    {
        return new class extends QueueFactory
        {
            public function __construct()
            {
                parent::__construct(Settings::getInstance());

                $this->container = \Drupal::getContainer();
            }

            public function get($name, $reliable = false): QueueInterface
            {
                if ($name == 'create_node_worker' && $reliable) {
                    $this->queues[$name] = $this->container->get(
                        'queue_reliable_service_create_node_worker'
                    )->get($name);

                    return $this->queues[$name];
                }

                return parent::get($name, $reliable);
            }
        };
    }
}
