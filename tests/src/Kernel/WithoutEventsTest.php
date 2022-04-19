<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\Component\EventDispatcher\Event;
use Drupal\locale\LocaleEvent;
use Drupal\Tests\test_traits\Kernel\Testing\WithoutEvents;
use Drupal\Tests\token\Kernel\KernelTestBase;

class WithoutEventsTest extends KernelTestBase
{
    use WithoutEvents;

    /** @test */
    public function without_events(): void
    {
        $this->withoutEvents();

        $this->container->get('event_dispatcher')->dispatch(new Event(), 'test_event');

        $this->assertDispatched('test_event');
        $this->assertDispatched(Event::class);
    }

    /** @test */
    public function expects_events_class_string(): void
    {
        $this->expectsEvents(Event::class);

        $this->container->get('event_dispatcher')->dispatch(new Event(), 'test_event');
    }

    /** @test */
    public function expects_events_event_name(): void
    {
        $this->expectsEvents('test_event');

        $this->container->get('event_dispatcher')->dispatch(new Event(), 'test_event');
    }

    /** @test */
    public function doesnt_expect_events_class_string(): void
    {
        $this->doesntExpectEvents(Event::class);

        $this->container->get('event_dispatcher')->dispatch(new LocaleEvent([]), 'second_event');
    }

    /** @test */
    public function doesnt_expect_events_event_name(): void
    {
        $this->doesntExpectEvents('first_event');

        $this->container->get('event_dispatcher')->dispatch(new Event(), 'second_event');
    }

    /** @test */
    public function assert_dispatched_with_callback(): void
    {
        $this->expectsEvents('test_event');

        $event = new Event();
        $event->title = 'hello';

        $this->container->get('event_dispatcher')->dispatch($event, 'test_event');

        $this->assertDispatched('test_event', function(Event $firedEvent) use ($event) {
            return $firedEvent->title === $event->title;
        });

        $this->assertDispatched(Event::class, function(Event $firedEvent) use ($event) {
            return $firedEvent->title === $event->title;
        });
    }
}
