<?php

namespace Drupal\Tests\test_traits\Kernel\Concerns;

use Drupal\Core\Queue\QueueInterface;

/** To be used in kernel tests */
trait InteractsWithQueues
{
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    /** @var array */
    private $queues = [];

    private $useReliableQueues = false;

    public function getQueue(string $queueName): QueueInterface
    {
        if (isset($this->queues[$queueName])) {
            return $this->queues[$queueName];
        }

        $this->queues[$queueName] = $this->container->get('queue')->get($queueName, $this->useReliableQueues);

        return $this->queues[$queueName];
    }

    public function addToQueue(string $queueName, $data): self
    {
        $this->getQueue($queueName)->createItem($data);

        return $this;
    }

    private function processQueue(string $queueName): void
    {
        $queue = $this->getQueue($queueName);

        $queueWorker = $this->container->get('plugin.manager.queue_worker')->createInstance($queueName);

        while ($item = $queue->claimItem()) {
            if ($item instanceof \stdClass === false) {
                return;
            }

            $queueWorker->processItem($item->data);
            $queue->deleteItem($item);
        }
    }

    public function useReliableQueues(): self
    {
        $this->useReliableQueues = true;

        return $this;
    }
}
