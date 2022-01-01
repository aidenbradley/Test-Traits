<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Core\Config\ConfigEvents;
use Drupal\Core\Routing\RoutingEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\language\EventSubscriber\ConfigSubscriber;
use Drupal\node\Routing\RouteSubscriber;
use Drupal\Tests\test_traits\Kernel\Testing\WithoutEventSubscribers;

class WithoutEventsTest extends KernelTestBase
{
    use WithoutEventSubscribers;

    /** @var ContainerAwareEventDispatcher */
    private $dispatcher;

    /** @test */
    public function without_events(): void
    {
        $this->assertNotEmpty($this->dispatcher()->getListeners());

        $this->withoutSubscribers();

        $this->assertEmpty($this->dispatcher()->getListeners());
    }

    /** @test */
    public function globally_ignores_events_after_enabling_module(): void
    {
        $this->withoutSubscribers();

        $this->enableModules([
            'language',
            'node',
        ]);

        $this->assertSubscriberNotListening('node.route_subscriber');
        $this->assertSubscriberNotListening('language.config_subscriber');
    }

    /** @test */
    public function without_events_class_list(): void
    {
        $this->enableModules([
            'language',
            'node',
        ]);

        $this->withoutSubscribers([
            RouteSubscriber::class, // node.route_subscriber
            ConfigSubscriber::class, // language.config_subscriber
        ]);

        $this->assertSubscriberNotListening('node.route_subscriber');
        $this->assertSubscriberNotListening('language.config_subscriber');
    }

    /** @test */
    public function removes_events_by_class_after_enabling_module(): void
    {
        $this->withoutSubscribers([
            RouteSubscriber::class, // node.route_subscriber
            ConfigSubscriber::class, // language.config_subscriber
        ]);

        $this->enableModules([
            'language',
            'node',
        ]);

        $this->assertSubscriberNotListening('node.route_subscriber');
        $this->assertSubscriberNotListening('language.config_subscriber');
    }

    /** @test */
    public function without_events_listening_for(): void
    {
        $this->enableModules([
            'node',
        ]);

        $this->assertNotEmpty($this->dispatcher()->getListeners(ConfigEvents::SAVE));

        $this->withoutSubscribersForEvents(ConfigEvents::SAVE);

        $this->assertEmpty($this->dispatcher()->getListeners(ConfigEvents::SAVE));
    }

    /** @test */
    public function remove_events_by_subscribed_event_after_enabling_modules(): void
    {
        $this->withoutSubscribersForEvents(RoutingEvents::ALTER);

        $this->enableModules([
            'node',
        ]);

        $this->assertEmpty($this->dispatcher()->getListeners(RoutingEvents::ALTER));
    }

    /** @test */
    public function without_event_with_class_string_and_service_id(): void
    {
        $this->enableModules([
            'language',
            'node',
        ]);

        $this->withoutSubscribers([
            RouteSubscriber::class, // node.route_subscriber
            'language.config_subscriber',
        ]);

        $this->assertSubscriberNotListening('node.route_subscriber');
        $this->assertSubscriberNotListening('language.config_subscriber');
    }

    /** @test */
    public function removes_event_with_class_string_and_service_id_after_enabling_modules(): void
    {
        $this->withoutSubscribers([
            RouteSubscriber::class, // node.route_subscriber
            'language.config_subscriber',
        ]);

        $this->enableModules([
            'language',
            'node',
        ]);

        $this->assertSubscriberNotListening('node.route_subscriber');
        $this->assertSubscriberNotListening('language.config_subscriber');
    }

    private function dispatcher(): ContainerAwareEventDispatcher
    {
        if (isset($this->dispatcher) === false) {
            $this->dispatcher = $this->container->get('event_dispatcher');
        }

        return $this->dispatcher;
    }

    private function assertSubscriberNotListening(string $subscriber): void
    {
        $this->assertFalse(in_array($subscriber, $this->container->get('event_dispatcher')->getListeners()));
    }
}
