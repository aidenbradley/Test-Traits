<?php

namespace Drupal\helpers\Concerns\Tests;

use Drupal\Core\Queue\QueueInterface;

/** To be used in kernel tests */
trait InteractsWithQueues
{
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    protected $container;

    /** @var array */
    protected $queues = [];

    public function getQueue(string $queueName): QueueInterface
    {
        if (isset($this->queues[$queueName])) {
            return $this->queues[$queueName];
        }

        $this->queues[$queueName] = $this->container->get('queue')->get($queueName);

        return $this->queues[$queueName];
    }

    public function addToQueue(string $queueName, $data): void
    {
        $this->getQueue($queueName)->createItem($data);
    }

    private function processQueue(string $queueName, ?string $queueWorker = null): void
    {
        if ($queueWorker === null) {
            $queueWorker = $queueName;
        }

        $queue = $this->getQueue($queueName);

        $queueWorker = $this->container->get('plugin.manager.queue_worker')->createInstance($queueWorker);

        while ($item = $queue->claimItem()) {
            if ($item instanceof \stdClass === false) {
                return;
            }

            $queueWorker->processItem($item->data);
            $queue->deleteItem($item);
        }
    }
}
