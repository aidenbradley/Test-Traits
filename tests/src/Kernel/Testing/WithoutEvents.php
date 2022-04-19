<?php

namespace Drupal\Tests\test_traits\Kernel\Testing;

use Illuminate\Support\Collection;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

trait WithoutEvents
{
    /** @var array */
    private $firedEvents;

    /** @var array */
    private $expectedEvents;

    /** @var array */
    private $nonExpectedEvents;

    /** Mock the event dispatcher. All dispatched events are collected */
    public function withoutEvents(): self
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);

        $dispatcher->dispatch(Argument::any(), Argument::any())->will([
            $this, 'registerDispatchedEvent'
        ]);

        $this->container->set('event_dispatcher', $dispatcher->reveal());

        return $this;
    }

    public function expectsEvents($events): self
    {
        $this->expectedEvents = (array) $events;

        return $this->withoutEvents();
    }

    public function doesntExpectEvents($events): self
    {
        $this->nonExpectedEvents = (array) $events;

        return $this->withoutEvents();
    }

    public function assertDispatched($event, ?callable $callback = null): self
    {
        $firedEvents = $this->getFiredEvents($event);

        $this->assertTrue($firedEvents->isNotEmpty(), $event . ' event was not dispatched');

        if ($callback) {
            $this->assertTrue($callback($firedEvents->first()));
        }

        return $this;
    }

    public function assertNotDispatched($event): self
    {
        $this->assertTrue($this->getFiredEvents($event)->isEmpty(), $event . ' event was dispatched');

        return $this;
    }

    public function registerDispatchedEvent($arguments): void
    {
        $this->firedEvents[$arguments[1]] = $arguments[0];
    }

    protected function tearDown(): void
    {
        if (isset($this->expectedEvents)) {
            foreach ($this->expectedEvents as $event) {
                $this->assertDispatched($event);
            }
        }

        if (isset($this->nonExpectedEvents)) {
            foreach ($this->nonExpectedEvents as $event) {
                $this->assertNotDispatched($event);
            }
        }

        parent::teardown();
    }
    /**
     * Get fired events.
     * You can optionally pass an event name or event class to filter the list against
     */
    public function getFiredEvents(?string $event = null): Collection
    {
        return collect($this->firedEvents)->when($event, function(Collection $events, $event) {
            return $events->filter(function($object, string $name) use ($event) {
                return get_class($object) === $event || $name === $event;
            });
        });
    }
}
