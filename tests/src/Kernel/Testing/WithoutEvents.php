<?php

namespace Drupal\Tests\test_traits\Kernel\Testing;

use Drupal\Tests\test_traits\Kernel\Testing\Decorators\EventSubscriberDefinition;

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

    /**
     * Prevents any events from triggering.
     */
    public function withoutEvents(): self
    {
        $this->ignoreAllEvents = true;

        return $this->removeDefinitions();
    }

    public function withoutEventsFromModule(string $module): self
    {
        $this->withoutEventsFromModules[$module] = $module;

        return $this->removeDefinitions(function(EventSubscriberDefinition $definition) use ($module) {
            return $definition->hasProvider() && $definition->providerIs($module) === false;
        });
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

        return $this->removeDefinitions(function(EventSubscriberDefinition $definition) use ($class) {
            return $definition->isClass($class) === false;
        });
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
        foreach ((array)$eventNames as $eventName) {
            $this->withoutEventsListeningFor[$eventName] = $eventName;

            return $this->removeDefinitions(function(EventSubscriberDefinition $definition) use ($eventName) {
                return $definition->subscribesTo($eventName) === false;
            });
        }

        return $this;
    }

    protected function enableModules(array $modules): void
    {
        parent::enableModules($modules);

        $this->decoratedDefinitions = null;

        if ($this->ignoreAllEvents) {
            $this->withoutEvents();
        }

        foreach ($modules as $module) {
            if (isset($this->withoutEventsFromModules[$module]) === false) {
                continue;
            }

            $this->withoutEventsFromModule($module);
        }

        if (isset($this->withoutEventsFromClasses)) {
            $this->withoutEventsFromClasses($this->withoutEventsFromClasses);
        }

        if (isset($this->withoutEventsListeningFor)) {
            $this->withoutEventsListeningFor($this->withoutEventsListeningFor);
        }
    }

    /** @return EventSubscriberDefinition[] */
    private function getEventSubscriberDefinitions(): array
    {
        if (isset($this->decoratedDefinitions)) {
            return $this->decoratedDefinitions;
        }

        $subscriberNames = array_keys($this->container->findTaggedServiceIds('event_subscriber'));

        $this->decoratedDefinitions = array_map(function (string $subscriberName) {
            return EventSubscriberDefinition::createFromDefinition(
                $this->container->getDefinition($subscriberName)
            );
        }, $subscriberNames);

        return $this->decoratedDefinitions;
    }

    private function removeDefinitions(\Closure $filter = null): self
    {
        foreach ($this->getEventSubscriberDefinitions() as $definition) {
            if ($filter !== null && $filter($definition)) {
                continue;
            }

            $this->container->removeDefinition($definition->getServiceId());
        }

        return $this;
    }
}
