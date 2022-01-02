<?php

namespace Drupal\Tests\test_traits\Kernel\Testing;

use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

trait WithoutEvents
{
    /** @var array */
    public $firedEvents;

    /** Mock the event dispatcher. All dispatched events are collected */
    public function withoutEvents(): self
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);

        $dispatcher->dispatch(Argument::any(), Argument::type('string'))->will([
            $this, 'registerDispatchedEvent'
        ]);

        $this->container->set('event_dispatcher', $dispatcher->reveal());

        return $this;
    }

    public function assertEventFired(string $event): self
    {
        $assertEventName = collect($this->firedEvents)->keys()->filter(function(string $eventName) use ($event) {
            return $eventName === $event;
        });

        $assertEventClass = collect($this->firedEvents)->values()->filter(function($eventName) use ($event) {
            return get_class($eventName) === $event;
        });

        $this->assertTrue($assertEventName->isNotEmpty() || $assertEventClass->isNotEmpty());

        return $this;
    }

    public function registerDispatchedEvent($arguments): void
    {
        $this->firedEvents[$arguments[1]] = $arguments[0];
    }
}
