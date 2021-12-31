<?php

namespace Drupal\Tests\test_traits\Kernel\Testing;

use Drupal\Tests\test_traits\Kernel\Testing\Collections\EventSubscriberCollection;
use Drupal\Tests\test_traits\Kernel\Testing\Decorators\EventSubscriberDefinition as Definition;
use Illuminate\Support\Collection;

trait WithoutEventSubscribers
{
    /** @var Definition[] */
    private $definitionCollection;

    /** @var bool */
    private $ignoreAllSubscribers = false;

    /** @var array */
    private $ignoredSubscribers = [];

    /**
     * Prevents event subscribers from acting when an event is triggered.
     * Pass one or a list containing either class strings or service IDs.
     *
     * @code
     *     $this->withoutSubscribers('Drupal\node\Routing\RouteSubscriber')
     *     $this->withoutSubscribers('language.config_subscriber')
     *     $this->withoutSubscribers([
     *         'Drupal\node\Routing\RouteSubscriber',
     *         'language.config_subscriber',
     *     ]);
     * @endcode
     *
     * @param string|array $listeners
     */
    public function withoutSubscribers($subscribers = []): self
    {
        if ($subscribers === []) {
            $this->ignoreAllSubscribers = true;

            $subscribers = $this->getDefinitions()->getServiceIds();
        }

        return $this->ignore($subscribers);
    }

    /**
     * Define one or a list of modules to prevent their listeners
     * from acting when an event is triggered
     *
     * @code
     *     $this->withoutModuleSubscribers('node')
     *     $this->withoutModuleSubscribers([
     *         'node',
     *         'language',
     *     ]);
     * @endcode
     *
     * @param string|array $modules
     */
    public function withoutModuleSubscribers($modules): self
    {
        return $this->ignore($modules);
    }

    /**
     * Define one or a list of event names to prevent listeners
     * acting when these events are triggered
     *
     * @code
     *     $this->withoutSubscribersForEvents(\Drupal\Core\Routing\RoutingEvents::ALTER)
     *     $this->withoutSubscribersForEvents('routing.route_finished')
     *     $this->withoutSubscribersForEvents([
     *         '\Drupal\Core\Routing\RoutingEvents::ALTER',
     *         'routing.route_finished',
     *     ]);
     * @endcode
     *
     * @param string|array $eventNames
     */
    public function withoutSubscribersForEvents($eventNames): self
    {
        return $this->ignore($eventNames);
    }

    protected function enableModules(array $modules): void
    {
        parent::enableModules($modules);

        $this->definitionCollection = null;

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

    private function getDefinitions(): EventSubscriberCollection
    {
        if (isset($this->definitionCollection) === false) {
            $this->definitionCollection = EventSubscriberCollection::create($this->container);
        }

        return $this->definitionCollection;
    }

    private function removeDefinitions(): self
    {
        foreach ($this->ignoredSubscribers as $listener) {
            $this->getDefinitions()
                ->removeDefinitionsWhere(function (Definition $definition) use ($listener) {
                    return $definition->getServiceId() === $listener;
                })
                ->removeDefinitionsWhere(function (Definition $definition) use ($listener) {
                    return $definition->hasProvider() && $definition->providerIs($listener);
                })
                ->removeDefinitionsWhere(function (Definition $definition) use ($listener) {
                    return $definition->classIs($listener) || $definition->subscribesTo($listener);
                });
        }

        return $this;
    }
}
