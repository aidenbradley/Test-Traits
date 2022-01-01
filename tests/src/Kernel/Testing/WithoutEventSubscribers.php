<?php

namespace Drupal\Tests\test_traits\Kernel\Testing;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Illuminate\Support\Collection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

trait WithoutEventSubscribers
{
    /** @var array */
    private $ignoredSubscribers;

    /** @var ContainerAwareEventDispatcher */
    private $dispatcher;

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
    public function withoutSubscribers($listeners = []): self
    {
        collect($this->dispatcher()->getListeners())
            ->values()
            ->collapse()
            ->when($listeners, function(Collection $collection, $listeners) {
                return $collection->filter(function (array $listener) use ($listeners) {
                    return in_array(get_class($listener[0]), $listeners) || in_array($listener[0]->_serviceId, $listeners);
                });
            })->each(function (array $listener) {
                $this->removeSubscriber($listener[0]);
            });

        return $this;
    }

    private function removeSubscriber(EventSubscriberInterface $subscriber): self
    {
        $this->ignoredSubscribers[$subscriber->_serviceId] = $subscriber;

        $this->dispatcher()->removeSubscriber($subscriber);

        return $this;
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
        foreach ((array)$eventNames as $event) {
            foreach ($this->dispatcher()->getListeners($event) as $listener) {
                $this->removeSubscriber($listener[0]);
            }
        }

        return $this;
    }

    protected function enableModules(array $modules): void
    {
        parent::enableModules($modules);

        if (isset($this->ignoredSubscribers) === false) {
            return;
        }

        $this->withoutSubscribers(array_keys($this->ignoredSubscribers));
    }

    private function dispatcher(): ContainerAwareEventDispatcher
    {
        if (isset($this->dispatcher) === false) {
            $this->dispatcher = $this->container->get('event_dispatcher');
        }

        return $this->dispatcher;
    }
}
