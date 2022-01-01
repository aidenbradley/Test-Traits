<?php

namespace Drupal\Tests\test_traits\Kernel\Testing;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Tests\test_traits\Kernel\Testing\Decorators\DecoratedEventDispatcher as Dispatcher;
use Drupal\Tests\test_traits\Kernel\Testing\Decorators\DecoratedListener as Listener;
use Illuminate\Support\Collection;

trait WithoutEventSubscribers
{
    /** @var array */
    private $ignoredSubscribers;

    /** @var array */
    private $ignoredEvents;

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
        $this->dispatcher()->getListeners()->when($listeners, function (Collection $collection, $listeners) {
            return $collection->filter(function (Listener $listener) use ($listeners) {
                return in_array($listener->getClass(), $listeners) || in_array($listener->getServiceId(), $listeners);
            });
        })->each(function (Listener $listener) {
            $this->removeSubscriber($listener);
        });

        return $this;
    }

    private function removeSubscriber(Listener $listener, ?string $event = null): self
    {
        $this->ignoredSubscribers[$listener->getServiceId()] = $listener;

        if ($event) {
            $this->ignoredEvents[$event] = $event;
        }

        $this->container->get('event_dispatcher')->removeSubscriber($listener->getOriginal());

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
            $this->dispatcher()->getListeners($event)->each(function (Listener $listener) use ($event) {
                $this->removeSubscriber($listener, $event);
            });
        }

        return $this;
    }

    protected function enableModules(array $modules): void
    {
        parent::enableModules($modules);

        if (isset($this->ignoredSubscribers) === false) {
            return;
        }

        $this->withoutSubscribers(
            collect($this->ignoredSubscribers)->keys()->toArray()
        );
        $this->withoutSubscribersForEvents(
            collect($this->ignoredEvents)->keys()->toArray()
        );
    }

    private function dispatcher(): Dispatcher
    {
        return Dispatcher::create($this->container->get('event_dispatcher'));
    }
}
