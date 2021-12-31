<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Collections;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Tests\test_traits\Kernel\Testing\Decorators\EventSubscriberDefinition as Definition;
use Illuminate\Support\Collection;

class EventSubscriberCollection
{
    /** @var ContainerBuilder */
    private $container;

    /** @var \Illuminate\Support\Collection  */
    private $definitions;

    public static function create(ContainerBuilder $container): self
    {
        return new self($container);
    }

    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
        $this->definitions = $this->getDefinitions();
    }

    public function removeDefinitionsWhere(\Closure $closure): self
    {
        $this->definitions->filter($closure)->each(function (Definition $listener) {
            $this->container->removeDefinition($listener->getServiceId());
        });

        return $this;
    }

    public function getServiceIds(): array
    {
        return $this->definitions->map->getServiceId()->toArray();
    }

    private function getDefinitions(): Collection
    {
        $eventSubscribers = $this->container->findTaggedServiceIds('event_subscriber');

        return collect($eventSubscribers)->keys()->map(function (string $serviceId) {
            return $this->container->getDefinition($serviceId);
        })->mapInto(Definition::class);
    }
}
