<?php

namespace Drupal\Tests\test_traits\Kernel\Testing;

use Drupal\Tests\test_traits\Kernel\Testing\Decorators\EventSubscriberDefinition as Definition;
use Illuminate\Support\Collection;

trait WithoutEvents
{
    /** @var array */
    private $decoratedDefinitions;

    /** @var bool */
    private $ignoreAllEvents = false;

    /** @var array */
    private $withoutEventsFromModules = [];

    /** @var array */
    private $withoutEventsFromClasses = [];

    /** @var array */
    private $withoutEventsListeningFor = [];

    /** @var array */
    private $removedDefinitions = [];

    /** @var Collection */
    private $ignoredListeners = [];

    /**
     * Prevents any events from triggering.
     *
     * @param string|array $listeners
     */
    public function withoutEvents($listeners = []): self
    {
        if ($listeners === []) {
            $this->ignoreAllEvents = true;

            $listeners = $this->getDefinitions()->map->getServiceId()->toArray();
        }

        foreach ($listeners as $listener) {
            $this->addListenersToIgnore(function(Definition $definition) use ($listener) {
                return $definition->getClass() === $listener || $definition->getServiceId() === $listener;
            });
        }

        return $this->removeDefinitions();
    }

    public function withoutEventsFromModule(string $module): self
    {
        $this->withoutEventsFromModules[$module] = $module;

        $this->addListenersToIgnore(function(Definition $definition) use ($module) {
            return $definition->hasProvider() && $definition->providerIs($module);
        });

        return $this->removeDefinitions();
    }

    public function withoutEventsFromModules(array $modules): self
    {
        foreach ($modules as $module) {
            $this->withoutEventsFromModule($module);
        }

        return $this;
    }

    public function withoutEventFromClass(string $class): self
    {
        $this->withoutEventsFromClasses[$class] = $class;

        $this->addListenersToIgnore(function(Definition $definition) use ($class) {
            return $definition->isClass($class);
        });

        return $this->removeDefinitions();
    }

    public function withoutEventsFromClasses(array $classes): self
    {
        foreach ($classes as $class) {
            $this->withoutEventFromClass($class);
        }

        return $this;
    }

    /** @param string|array $eventNames */
    public function withoutEventsListeningFor($eventNames): self
    {
        $this->withoutEventsListeningFor = array_merge($this->withoutEventsListeningFor, (array) $eventNames);

        foreach ((array)$eventNames as $eventName) {
            $this->addListenersToIgnore(function(Definition $definition) use ($eventName) {
                return $definition->subscribesTo($eventName);
            });
        }

        return $this->removeDefinitions();
    }

    private function addListenersToIgnore(\Closure $filter = null): self
    {
        $this->ignoredListeners = $this->getDefinitions()->filter($filter)->merge($this->ignoredListeners);

        return $this;
    }

    protected function enableModules(array $modules): void
    {
        parent::enableModules($modules);

        $this->decoratedDefinitions = null;

        if ($this->ignoreAllEvents) {
            $this->withoutEvents();
        }

        $ignoreEventsFromModules = collect($modules)->filter(function(string $module) {
            return isset($this->withoutEventsFromModules[$module]);
        })->toArray();

        $this->withoutEventsFromModules($ignoreEventsFromModules)
            ->withoutEventsFromClasses($this->withoutEventsFromClasses)
            ->withoutEventsListeningFor($this->withoutEventsListeningFor);
    }

    /** @return Definition[] */
    private function getDefinitions(): Collection
    {
        if (isset($this->decoratedDefinitions) === false) {
            $eventSubscribers = $this->container->findTaggedServiceIds('event_subscriber');

            $this->decoratedDefinitions = collect($eventSubscribers)->keys()->map(function(string $serviceId) {
                return $this->container->getDefinition($serviceId);
            })->mapInto(Definition::class);
        }

        return $this->decoratedDefinitions;
    }

    private function removeDefinitions(): self
    {
        foreach ($this->ignoredListeners as $listener) {
            $this->container->removeDefinition($listener->getServiceId());
        }

        return $this;
    }
}
