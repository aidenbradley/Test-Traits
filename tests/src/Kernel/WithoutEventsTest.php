<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\Core\Routing\RoutingEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\language\EventSubscriber\ConfigSubscriber;
use Drupal\node\Routing\RouteSubscriber;
use Drupal\Tests\test_traits\Kernel\Testing\WithoutEventSubscribers;

class WithoutEventsTest extends KernelTestBase
{
    use WithoutEventSubscribers;

    /** @test */
    public function without_events(): void
    {
        $this->assertNotEmpty($this->container->findTaggedServiceIds('event_subscriber'));

        $this->withoutSubscribers();

        $this->assertEmpty($this->container->findTaggedServiceIds('event_subscriber'));
    }

    /** @test */
    public function globally_ignores_events_after_enabling_module(): void
    {
        $this->withoutSubscribers();

        $this->enableModules([
            'language',
            'node',
        ]);

        $this->assertFalse($this->container->hasDefinition('node.route_subscriber'));
        $this->assertFalse($this->container->hasDefinition('language.config_subscriber'));
    }

    /** @test */
    public function without_events_from_module(): void
    {
        $eventSubscribersBeforeEnable = array_keys(
            $this->container->findTaggedServiceIds('event_subscriber')
        );

        $this->enableModules([
            'language',
        ]);

        $eventSubscribersAfterEnable = array_keys(
            $this->container->findTaggedServiceIds('event_subscriber')
        );

        $languageEventSubscribers = array_diff(
            $eventSubscribersAfterEnable,
            $eventSubscribersBeforeEnable,
        );

        $this->withoutModuleSubscribers('language');

        foreach ($languageEventSubscribers as $languageEventSubscriber) {
            $this->assertFalse($this->container->hasDefinition($languageEventSubscriber));
        }
    }

    /** @test */
    public function without_events_from_modules(): void
    {
//        $this->markTestIncomplete(
//            'Need to figure out how to obtain container registrations via service providers that don\'t have a provider set'
//        );

        $eventSubscribersBeforeEnable = array_keys(
            $this->container->findTaggedServiceIds('event_subscriber')
        );

        $this->enableModules([
            'language',
            'node',
        ]);

        $eventSubscribersAfterEnable = array_keys(
            $this->container->findTaggedServiceIds('event_subscriber')
        );

        $languageEventSubscribers = array_diff(
            $eventSubscribersAfterEnable,
            $eventSubscribersBeforeEnable,
        );

        $this->withoutModuleSubscribers([
            'language',
            'node',
        ]);

        foreach ($languageEventSubscribers as $languageEventSubscriber) {
            $this->assertFalse($this->container->hasDefinition($languageEventSubscriber), $languageEventSubscriber . ' is in container');
        }
    }

    /** @test */
    public function removes_events_by_module_after_enabling_module(): void
    {
        $this->withoutModuleSubscribers([
            'language',
            'node',
        ]);

        $this->enableModules([
            'language',
            'node',
        ]);

        $this->assertFalse($this->container->hasDefinition('node.route_subscriber'));
        $this->assertFalse($this->container->hasDefinition('language.config_subscriber'));
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

        $this->assertFalse($this->container->hasDefinition('node.route_subscriber'));
        $this->assertFalse($this->container->hasDefinition('language.config_subscriber'));
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

        $this->assertFalse($this->container->hasDefinition('node.route_subscriber'));
        $this->assertFalse($this->container->hasDefinition('language.config_subscriber'));
    }

    /** @test */
    public function without_events_listening_for(): void
    {
        $this->enableModules([
            'node',
        ]);

        $this->assertTrue($this->container->hasDefinition('node.route_subscriber'));

        $this->withoutSubscribersForEvents(RoutingEvents::ALTER);

        $this->assertFalse($this->container->hasDefinition('node.route_subscriber'));
    }

    /** @test */
    public function remove_events_by_subscribed_event_after_enabling_modules(): void
    {
        $this->withoutSubscribersForEvents(RoutingEvents::ALTER);

        $this->enableModules([
            'node',
        ]);

        $this->assertFalse($this->container->hasDefinition('node.route_subscriber'));
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

        $this->assertFalse($this->container->hasDefinition('node.route_subscriber'));
        $this->assertFalse($this->container->hasDefinition('language.config_subscriber'));
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

        $this->assertFalse($this->container->hasDefinition('node.route_subscriber'));
        $this->assertFalse($this->container->hasDefinition('language.config_subscriber'));
    }
}
