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

        return $this->ignore($listeners);
    }

    public function withoutEventsFromModule(string $module): self
    {
        return $this->ignore($module);
    }

    public function withoutEventsFromModules(array $modules): self
    {
        foreach ($modules as $module) {
            $this->withoutEventsFromModule($module);
        }

        return $this;
    }

    /** @param string|array $eventNames */
    public function withoutEventsListeningFor($eventNames): self
    {
        return $this->ignore($eventNames);
    }

    /** @param string|array $services */
    private function ignore($services): self
    {
        $this->listenersToIgnore = array_merge($this->listenersToIgnore, (array)$services);

        return $this->removeDefinitions();
    }

    protected function enableModules(array $modules): void
    {
        parent::enableModules($modules);

        $this->decoratedDefinitions = null;

        if ($this->ignoreAllEvents) {
            $this->withoutEvents();
        }

        $ignoreEventsFromModules = collect($modules)->filter(function (string $module) {
            return in_array($module, $this->listenersToIgnore);
        })->toArray();

        $this->ignore($ignoreEventsFromModules);
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
        foreach ($this->listenersToIgnore as $listener) {
            if ($this->container->has($listener)) {
                $this->container->removeDefinition($listener);

                continue;
            }

            if ($this->container->get('module_handler')->moduleExists($listener)) {
                $this->getDefinitions()->filter(function (Definition $definition) use ($listener) {
                    return $definition->hasProvider() && $definition->providerIs($listener);
                })->each(function (Definition $listener) {
                    $this->container->removeDefinition($listener->getServiceId());
                });

                continue;
            }

            $this->getDefinitions()->filter(function (Definition $definition) use ($listener) {
                return $definition->isClass($listener) || $definition->subscribesTo($listener);
            })->each(function (Definition $listener) {
                $this->container->removeDefinition($listener->getServiceId());
            });
        }

        return $this;
    }
}
