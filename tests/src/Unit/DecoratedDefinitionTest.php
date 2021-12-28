<?php

namespace Drupal\Tests\test_traits\Unit;

use Drupal\Tests\test_traits\Kernel\Testing\Decorators\DecoratedDefinition;
use Drupal\Tests\UnitTestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\Definition;

class DecoratedDefinitionTest extends UnitTestCase
{
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
    public function in_class_list(): void
    {
        $definition = $this->createDefinitionWithoutProvider();
        $definition->getClass()->willReturn('Drupal\my_module\Class');

        $decoratorWithoutProvider = DecoratedDefinition::createFromDefinition($definition->reveal());

        $this->assertFalse($decoratorWithoutProvider->classInList([
            'Drupal\my_module\FirstClass',
            'Drupal\my_module\SecondClass',
        ]));

        $this->assertTrue($decoratorWithoutProvider->classInList([
            'Drupal\my_module\Class',
            'Drupal\my_module\FirstClass',
            'Drupal\my_module\SecondClass',
        ]));
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
