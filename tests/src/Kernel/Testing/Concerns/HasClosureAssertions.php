<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

trait HasClosureAssertions
{
    private $callbackAssertions;

    /**
     * This is to get around the serialization issue with PHPUnit and closures
     * For now, closure assertions are deferred to the teardown of the test
     */
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
        if (isset($this->callbackAssertions)) {
            foreach ($this->callbackAssertions as $assertionCallbacks) {
                $assertionCallbacks['closure'](
                    $assertionCallbacks['args']
                );
            }
        }

        parent::teardown();
    }
}
