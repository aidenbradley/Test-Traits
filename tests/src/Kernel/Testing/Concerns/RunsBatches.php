<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

trait RunsBatches
{
    public function runLatestBatch(string $redirect = '/'): self
    {
        $batch =& batch_get();

        $batch['progressive'] = false;

        batch_process($redirect);

        return $this;
    }
}
