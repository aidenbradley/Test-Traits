<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\Tests\test_traits\Kernel\Testing\WithoutEvents;
use Drupal\Tests\token\Kernel\KernelTestBase;

class WithoutEventsTest extends KernelTestBase
{
    use WithoutEvents;

    /** @test */
    public function without_events(): void
    {
        $this->withoutEvents();
    }
}
