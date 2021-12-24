<?php

namespace Drupal\test_traits_queue\Factory;

use Drupal\test_traits_queue\Queue\ReliableCreateNodeQueue;

class QueueReliableCreateNodeFactory
{
    public function get(string $name) {
        return ReliableCreateNodeQueue::create($name);
    }
}
