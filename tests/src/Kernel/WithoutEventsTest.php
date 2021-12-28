<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\language\EventSubscriber\ConfigSubscriber;
use Drupal\node\Routing\RouteSubscriber;
use Drupal\Tests\test_traits\Kernel\Testing\WithoutEvents;

class WithoutEventsTest extends KernelTestBase
{
    use WithoutEvents;

    /** @test */
    public function without_events(): void
    {
        $this->assertNotEmpty($this->container->findTaggedServiceIds('event_subscriber'));

        $this->withoutEvents();

        $this->assertEmpty($this->container->findTaggedServiceIds('event_subscriber'));
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

        $this->withoutEventsFromModule('language');

        foreach ($languageEventSubscribers as $languageEventSubscriber) {
            $this->assertFalse($this->container->hasDefinition($languageEventSubscriber));
        }
    }

    /** @test */
    public function without_events_from_modules(): void
    {
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

        $this->withoutEventsFromModules([
            'language',
            'node',
        ]);

        foreach ($languageEventSubscribers as $languageEventSubscriber) {
            $this->assertFalse($this->container->hasDefinition($languageEventSubscriber), $languageEventSubscriber . ' is in container');
        }
    }

    /** @test */
    public function without_events_class_list(): void
    {
        $this->enableModules([
            'language',
            'node',
        ]);

        $this->withoutEventsFromClasses([
            RouteSubscriber::class, // node.route_subscriber
            ConfigSubscriber::class, // language.config_subscriber
        ]);

        $this->assertFalse($this->container->hasDefinition('node.route_subscriber'));
        $this->assertFalse($this->container->hasDefinition('language.config_subscriber'));
    }
}
