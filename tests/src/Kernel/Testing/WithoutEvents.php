<?php

namespace Drupal\Tests\test_traits\Kernel\Testing;

use Drupal\Tests\test_traits\Kernel\Testing\Decorators\DecoratedDefinition;

trait WithoutEvents
{
    /** @var array */
    private $decoratedDefinitions = [];

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

        $subscribers = $this->container->findTaggedServiceIds('event_subscriber');

        foreach (array_keys($subscribers) as $subscriberName) {
            $this->container->removeDefinition($subscriberName);
        }

        return $this;
    }

    public function withoutEventsFromModule(string $module): self
    {
        $this->withoutEventsFromModules[$module] = $module;

        foreach ($this->getDecoratedDefinitions() as $definition) {
            if ($definition->hasProvider() && $definition->providerIs($module) === false) {
                continue;
            }

            $this->container->removeDefinition($definition->getServiceId());
        }

        return $this;
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

        foreach ($this->getDecoratedDefinitions() as $definition) {
            if ($definition->isClass($class) === false) {
                continue;
            }

            $this->container->removeDefinition($definition->getServiceId());
        }

        return $this;
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

            foreach ($this->getDecoratedDefinitions() as $definition) {
                if (method_exists($definition->getClass(), 'getSubscribedEvents') === false) {
                    continue;
                }

                $subscribedEvents = $definition->getClass()::getSubscribedEvents();

                if (in_array($eventName, array_keys($subscribedEvents)) === false) {
                    continue;
                }

                $this->container->removeDefinition($definition->getServiceId());
            }
        }

        return $this;
    }

    /** @return DecoratedDefinition[] */
    private function getDecoratedDefinitions(): array
    {
        if (isset($this->decoratedDefinitions)) {
            return $this->decoratedDefinitions;
        }

        $subscriberNames = array_keys($this->container->findTaggedServiceIds('event_subscriber'));

        $this->decoratedDefinitions = array_map(function (string $subscriberName) {
            return DecoratedDefinition::createFromDefinition(
                $this->container->getDefinition($subscriberName)
            );
        }, $subscriberNames);

        return $this->decoratedDefinitions;
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
}
