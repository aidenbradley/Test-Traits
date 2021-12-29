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

    /** @var array */
    private $removedDefinitions = [];

    /** @var array */
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

            $listeners = $this->getEventSubscriberDefinitions();
        }

        $this->ignoredListeners = array_merge($this->ignoredListeners, $listeners);

        return $this->removeDefinitions();
    }

    public function withoutEventsFromModule(string $module): self
    {
        $this->withoutEventsFromModules[$module] = $module;

        foreach ($this->getEventSubscriberDefinitions() as $definition) {
            if ($definition->hasProvider() && $definition->providerIs($module) === false) {
                continue;
            }

            $this->ignoredListeners = array_merge($this->ignoredListeners, [
                $definition
            ]);
        }

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

        foreach ($this->getEventSubscriberDefinitions() as $definition) {
            if ($definition->isClass($class) === false) {
                continue;
            }

            $this->ignoredListeners = array_merge($this->ignoredListeners, [
                $definition
            ]);
        }

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
            foreach ($this->getEventSubscriberDefinitions() as $definition) {
                if ($definition->subscribesTo($eventName) === false) {
                    continue;
                }

                $this->ignoredListeners = array_merge($this->ignoredListeners, [
                    $definition
                ]);
            }
        }

        return $this->removeDefinitions();
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

        if (isset($this->withoutEventsFromClasses) && $this->withoutEventsFromClasses !== []) {
            $this->withoutEventsFromClasses($this->withoutEventsFromClasses);
        }

        if (isset($this->withoutEventsListeningFor) && $this->withoutEventsListeningFor !== []) {
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
        foreach ($this->ignoredListeners as $listener) {
            $this->container->removeDefinition($listener->getServiceId());
        }

        return $this;
    }
}
