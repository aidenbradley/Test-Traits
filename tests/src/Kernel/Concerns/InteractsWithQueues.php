<?php

namespace Drupal\Tests\test_traits\Kernel\Concerns;

use Drupal\Core\Queue\QueueInterface;

/** To be used in kernel tests */
trait InteractsWithQueues
{
    /** @var array */
    private $queues = [];

    /** @var bool */
    private $useReliableQueue = true;

    /** @var bool */
    private $dontUseReliableQueue = false;

    public function getQueue(string $queueName): QueueInterface
    {
        return $this->getQueueByName($queueName, $this->dontUseReliableQueue);
    }

    public function getReliableQueue(string $queueName): QueueInterface
    {
        return $this->getQueueByName($queueName, $this->useReliableQueue);
    }

    public function addToQueue(string $queueName, $data): self
    {
        $this->getQueue($queueName)->createItem($data);

        return $this;
    }

    public function processQueue(string $queueName): void
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

    public function dontUseReliableQueues(): self
    {
        $this->useReliableQueues = false;

        return $this;
    }

    private function getQueueByName(string $queueName, bool $useReliableQueue): QueueInterface
    {
        if (isset($this->queues[$queueName])) {
            return $this->queues[$queueName];
        }

        $this->queues[$queueName] = $this->container->get('queue')->get($queueName, $useReliableQueue);

        return $this->queues[$queueName];
    }
}
