<?php

namespace Drupal\Tests\test_traits\Kernel\Testing;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Tests\test_traits\Kernel\Testing\Collections\EventSubscriberCollection;
use Drupal\Tests\test_traits\Kernel\Testing\Decorators\EventSubscriberDefinition as Definition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

trait WithoutEventSubscribers
{
    /** @var Definition[] */
    private $definitionCollection;

    /** @var bool */
    private $ignoreAllSubscribers = false;

    /** @var array */
    private $ignoredSubscribers = [];

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
    public function withoutSubscribers($subscribers = []): self
    {
        $listeners = collect($this->dispatcher()->getListeners())->values()->collapse();

        if ($subscribers !== []) {
            $listeners->filter(function (array $subscriber) use ($subscribers) {
                $listener = $subscriber[0];

                return in_array(get_class($listener), $subscribers) || in_array($listener->_serviceId, $subscribers);
            });
        }

        $listeners->each(function (array $listener) {
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
                $this->dispatcher()->removeSubscriber($listener[0]);
            }
        }

        return $this;
    }

    protected function enableModules(array $modules): void
    {
        parent::enableModules($modules);

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
