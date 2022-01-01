<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Decorators;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DecoratedListener
{
    /** @var \Symfony\Component\EventDispatcher\EventSubscriberInterface|null */
    private $listener;

    /** @var array|null */
    private $callable;

    public static function createFromArray(array $listener): self
    {
        return new self($listener);
    }

    public function __construct(array $listener)
    {
        $this->listener = $listener[0] ?? null;
        $this->callable = $listener[1] ?? null;
    }

    public function getServiceId(): ?string
    {
        if (isset($this->listener) === false) {
            return null;
        }

        if (property_exists($this->listener, '_serviceId') === false) {
            return null;
        }

        return $this->listener->_serviceId;
    }

    public function getClass(): ?string
    {
        if (isset($this->listener) === false) {
            return null;
        }

        return get_class($this->listener);
    }

    public function inList(array $listeners): bool
    {
        return in_array($this->getClass(), $listeners) || in_array($this->getServiceId(), $listeners);
    }

    public function getOriginal(): EventSubscriberInterface
    {
        return $this->listener;
    }
}
