<?php

namespace Drupal\Tests\test_traits\Kernel\Testing;

use Drupal\Tests\test_traits\Kernel\Testing\Decorators\EventSubscriberDefinition as Definition;
use Illuminate\Support\Collection;

trait WithoutEventSubscribers
{
    /** @var Definition[] */
    private $definitions;

    /** @var bool */
    private $ignoreAllSubscribers = false;

    /** @var array */
    private $ignoredSubscribers = [];

    /**
     * Prevents any events from triggering.
     *
     * @param string|array $listeners
     */
    public function withoutSubscribers($subscribers = []): self
    {
        if ($subscribers === []) {
            $this->ignoreAllSubscribers = true;

            $subscribers = $this->getDefinitions()->map->getServiceId()->toArray();
        }

        return $this->ignore($subscribers);
    }

    /** @param string|array $modules */
    public function withoutModuleSubscribers($modules): self
    {
        return $this->ignore($modules);
    }

    /** @param string|array $eventNames */
    public function withoutSubscribersForEvents($eventNames): self
    {
        return $this->ignore($eventNames);
    }

    protected function enableModules(array $modules): void
    {
        parent::enableModules($modules);

        $this->definitions = null;

        if ($this->ignoreAllSubscribers) {
            $this->withoutSubscribers();

            return;
        }

        $ignoreModules = collect($modules)->filter(function (string $module) {
            return in_array($module, $this->ignoredSubscribers);
        })->toArray();

        $this->ignore($ignoreModules);
    }

    /** @param string|array $services */
    private function ignore($services): self
    {
        $this->ignoredSubscribers = array_merge($this->ignoredSubscribers, (array)$services);

        return $this->removeDefinitions();
    }

    /** @return Definition[] */
    private function getDefinitions(): Collection
    {
        if (isset($this->definitions) === false) {
            $eventSubscribers = $this->container->findTaggedServiceIds('event_subscriber');

            $this->definitions = collect($eventSubscribers)->keys()->map(function (string $serviceId) {
                return $this->container->getDefinition($serviceId);
            })->mapInto(Definition::class);
        }

        return $this->definitions;
    }

    private function removeDefinitions(): self
    {
        foreach ($this->ignoredSubscribers as $listener) {
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
