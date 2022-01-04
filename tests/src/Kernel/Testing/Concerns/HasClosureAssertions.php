<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

trait HasClosureAssertions
{
    private $callbackAssertions;

    protected function addClosureAssertion(\Closure $closure, $args = null): self
    {
        $this->callbackAssertions[] = [
            'closure' => $closure,
            'args' => $args,
        ];

        return $this;
    }

    protected function teardown(): void
    {
        foreach ($this->callbackAssertions as $assertionCallbacks) {
            $assertionCallbacks['closure'](
                $assertionCallbacks['args']
            );
        }

        parent::teardown();
    }
}
