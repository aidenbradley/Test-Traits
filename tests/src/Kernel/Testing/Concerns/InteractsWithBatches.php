<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

trait InteractsWithBatches
{
    public function runBatch(string $redirect = '/'): self
    {
        $batch =& batch_get();

        $batch['progressive'] = false;

        batch_process($redirect);

        return $this;
    }
}
