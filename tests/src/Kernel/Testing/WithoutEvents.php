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

    private $listenersToIgnore = [];

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

        $this->listenersToIgnore = array_merge($this->listenersToIgnore, $listeners);

        return $this->removeDefinitionsAgain();
    }

    public function withoutEventsFromModule(string $module): self
    {
        $this->listenersToIgnore = array_merge($this->listenersToIgnore, [
            $module,
        ]);

        return $this->removeDefinitionsAgain();
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
        $this->listenersToIgnore = array_merge($this->listenersToIgnore, [
            $class,
        ]);

        return $this->removeDefinitionsAgain();
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
        $this->listenersToIgnore = array_merge($this->listenersToIgnore, (array)$eventNames);

        return $this->removeDefinitionsAgain();
    }

    protected function enableModules(array $modules): void
    {
        parent::enableModules($modules);

        $this->decoratedDefinitions = null;

        if ($this->ignoreAllEvents) {
            $this->withoutEvents();
        }

        $ignoreEventsFromModules = collect($modules)->filter(function (string $module) {
            return isset($this->withoutEventsFromModules[$module]);
        })->toArray();

        $this->withoutEventsFromModules($ignoreEventsFromModules)->removeDefinitionsAgain();
    }

    /** @return Definition[] */
    private function getDefinitions(): Collection
    {
        if (isset($this->decoratedDefinitions) === false) {
            $eventSubscribers = $this->container->findTaggedServiceIds('event_subscriber');

            $this->decoratedDefinitions = collect($eventSubscribers)->keys()->map(function (string $serviceId) {
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

    private function removeDefinitionsAgain(): self
    {
        $definitions = $this->getDefinitions();

        foreach ($this->listenersToIgnore as $listener) {
            if ($this->container->has($listener)) {
                $this->container->removeDefinition($listener);

                continue;
            }

            if ($this->container->get('module_handler')->moduleExists($listener)) {
                (clone $definitions)->filter(function (Definition $definition) use ($listener) {
                    return $definition->hasProvider() && $definition->providerIs($listener);
                })->each(function (Definition $listener) {
                   $this->container->removeDefinition($listener->getServiceId());
                });

                continue;
            }

            (clone $definitions)->filter(function (Definition $definition) use ($listener) {
                return $definition->isClass($listener) || $definition->subscribesTo($listener);
            })->each(function (Definition $listener) {
                $this->container->removeDefinition($listener->getServiceId());
            });
        }

        return $this;
    }
}
