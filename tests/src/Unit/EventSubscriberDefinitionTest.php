<?php

namespace Drupal\Tests\test_traits\Unit;

use Drupal\Core\Routing\RoutingEvents;
use Drupal\Tests\test_traits\Kernel\Testing\Decorators\EventSubscriberDefinition;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\Definition;

class EventSubscriberDefinitionTest extends UnitTestCase
{
    /** @test */
    public function subscribes_to(): void
    {
        $definition = $this->prophesize(Definition::class);

        $definition->getClass()->willReturn(__CLASS__);

        $decoratedDefinition = EventSubscriberDefinition::createFromDefinition($definition->reveal());

        $this->assertTrue($decoratedDefinition->subscribesTo(RoutingEvents::ALTER));
        $this->assertTrue($decoratedDefinition->subscribesTo(RoutingEvents::DYNAMIC));
        $this->assertFalse($decoratedDefinition->subscribesTo('random_event.name'));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RoutingEvents::ALTER => 'alterRoutes',
            RoutingEvents::DYNAMIC => 'dynamicRoutes'
        ];
    }
}
