<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Decorators;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Illuminate\Support\Collection;

class DecoratedEventDispatcher
{
    /** @var ContainerAwareEventDispatcher */
    private $dispatcher;

    public static function create(ContainerAwareEventDispatcher $dispatcher): self
    {
        return new self($dispatcher);
    }

    public function __construct(ContainerAwareEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function getListeners(?string $event = null): Collection
    {
        return collect($this->dispatcher->getListeners($event))->unless($event, function(Collection $listeners) {
            return $listeners->values()->collapse();
        })->mapInto(DecoratedListener::class);
    }
}
