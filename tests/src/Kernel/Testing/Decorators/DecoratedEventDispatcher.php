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

    /** @return mixed */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->dispatcher, $name)) {
            return $this->dispatcher->$name(...$arguments);
        }

        return $this;
    }

    /** @return mixed */
    public function __get(string $name)
    {
        if (property_exists($this->dispatcher, $name)) {
            return $this->dispatcher->$name;
        }

        return $this;
    }

    /** @param mixed $value */
    public function __set(string $name, $value): self
    {
        if (property_exists($this->dispatcher, $name)) {
            $this->dispatcher->$name = $value;
        }

        return $this;
    }
}
