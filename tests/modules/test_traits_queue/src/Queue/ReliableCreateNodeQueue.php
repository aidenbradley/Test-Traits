<?php

namespace Drupal\test_traits_queue\Queue;

use Drupal\Core\Queue\QueueInterface;

class ReliableCreateNodeQueue implements QueueInterface
{
    /** @var string */
    private $name;

    public static function create(string $name): self
    {
        return new self($name);
    }

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function createItem($data)
    {
        // silence is golden
    }

    public function numberOfItems()
    {
        // silence is golden
    }

    public function claimItem($lease_time = 3600)
    {
        // silence is golden
    }

    public function deleteItem($item)
    {
        // silence is golden
    }

    public function releaseItem($item)
    {
        // silence is golden
    }

    public function createQueue()
    {
        // silence is golden
    }

    public function deleteQueue()
    {
        // silence is golden
    }
}
