<?php

namespace Drupal\Tests\test_traits\Kernel\Testing;

trait WithoutEvents
{
    public function withoutEvents(): self
    {
        return $this;
    }
}
