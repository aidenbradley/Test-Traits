<?php

namespace Drupal\Tests\test_traits\Unit;

use Drupal\Tests\test_traits\Kernel\Testing\Decorators\DecoratedDefinition;
use Drupal\Tests\UnitTestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\Definition;

class DecoratedDefinitionTest extends UnitTestCase
{
    /** @test */
    public function has_properties(): void
    {
        $definition = $this->createDefinitionWithoutProvider();
        $definition->getProperties()->willReturn([]);

        $decoratedDefinition = DecoratedDefinition::createFromDefinition(
            $definition->reveal()
        );

        $this->assertFalse($decoratedDefinition->hasProperties());

        $definition = $this->createDefinitionWithoutProvider();
        $definition->getProperties()->willReturn([
            '_serviceId' => 'my_service.id'
        ]);

        $decoratedDefinition = DecoratedDefinition::createFromDefinition(
            $definition->reveal()
        );

        $this->assertTrue($decoratedDefinition->hasProperties());
    }

    /** @test */
    public function has_service_id(): void
    {
        $definition = $this->createDefinitionWithoutProvider();
        $definition->getProperties()->willReturn([]);

        $decoratedDefinition = DecoratedDefinition::createFromDefinition(
            $definition->reveal()
        );

        $this->assertFalse($decoratedDefinition->hasServiceid());

        $definition = $this->createDefinitionWithoutProvider();
        $definition->getProperties()->willReturn([
            '_serviceId' => 'my_service.id'
        ]);

        $decoratedDefinition = DecoratedDefinition::createFromDefinition(
            $definition->reveal()
        );

        $this->assertTrue($decoratedDefinition->hasServiceid());
    }

    /** @test */
    public function get_service_id(): void
    {
        $definition = $this->createDefinitionWithoutProvider();
        $definition->getProperties()->willReturn([]);

        $decoratedDefinition = DecoratedDefinition::createFromDefinition(
            $definition->reveal()
        );

        $this->assertNull($decoratedDefinition->getServiceId());

        $definition = $this->createDefinitionWithoutProvider();
        $definition->getProperties()->willReturn([
            '_serviceId' => 'my_service.id'
        ]);

        $decoratedDefinition = DecoratedDefinition::createFromDefinition(
            $definition->reveal()
        );

        $this->assertEquals('my_service.id', $decoratedDefinition->getServiceId());
    }

    /** @test */
    public function has_provider(): void
    {
        $decoratorWithoutProvider = DecoratedDefinition::createFromDefinition(
            $this->createDefinitionWithoutProvider()->reveal()
        );

        $this->assertFalse($decoratorWithoutProvider->hasProvider());

        $decoratorWithProvider = DecoratedDefinition::createFromDefinition(
            $this->createDefinitionWithProvider('test_traits')->reveal()
        );

        $this->assertTrue($decoratorWithProvider->hasProvider());
    }

    /** @test */
    public function get_provider(): void
    {
        $decoratorWithoutProvider = DecoratedDefinition::createFromDefinition(
            $this->createDefinitionWithoutProvider()->reveal()
        );

        $this->assertNull($decoratorWithoutProvider->getProvider());

        $decoratorWithProvider = DecoratedDefinition::createFromDefinition(
            $this->createDefinitionWithProvider('test_traits')->reveal()
        );

        $this->assertEquals('test_traits', $decoratorWithProvider->getProvider());
    }

    /** @test */
    public function provider_is(): void
    {
        $decoratorWithoutProvider = DecoratedDefinition::createFromDefinition(
            $this->createDefinitionWithoutProvider()->reveal()
        );

        $this->assertFalse($decoratorWithoutProvider->providerIs('test_traits'));

        $decoratorWithProvider = DecoratedDefinition::createFromDefinition(
            $this->createDefinitionWithProvider('test_traits')->reveal()
        );

        $this->assertTrue($decoratorWithProvider->providerIs('test_traits'));
    }

    /** @test */
    public function is_class(): void
    {
        $definition = $this->createDefinitionWithoutProvider();
        $definition->getClass()->willReturn('Drupal\my_module\Class');

        $decoratorWithoutProvider = DecoratedDefinition::createFromDefinition($definition->reveal());

        $this->assertFalse($decoratorWithoutProvider->isClass('Drupal\my_module\FirstClass'));

        $this->assertTrue($decoratorWithoutProvider->isClass('Drupal\my_module\Class'));
    }

    private function createDefinitionWithoutProvider(): ObjectProphecy
    {
        $definition = $this->prophesize(Definition::class);

        $definition->getTags()->willReturn([
            '_provider' => [
                0 => [],
            ],
        ]);

        return $definition;
    }

    private function createDefinitionWithProvider(string $provider): ObjectProphecy
    {
        $definition = $this->prophesize(Definition::class);

        $definition->getTags()->willReturn([
            '_provider' => [
                0 => [
                    'provider' => $provider,
                ],
            ],
        ]);

        return $definition;
    }
}
