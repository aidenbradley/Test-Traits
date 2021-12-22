<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Concerns\InteractsWithQueues;

class InteractsWithQueuesTest extends KernelTestBase
{
    use InteractsWithQueues;

    /** @test */
    public function get_queue(): void
    {

    }
}
