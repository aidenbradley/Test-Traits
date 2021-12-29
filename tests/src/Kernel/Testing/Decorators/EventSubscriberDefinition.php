<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Decorators;

use Symfony\Component\DependencyInjection\Definition;

class EventSubscriberDefinition extends DecoratedDefinition
{
    public function __construct(Definition $definition)
    {
        parent::__construct($definition);

        assert(
            method_exists($this->definition->getClass(), 'getSubscribedEvents')
        );
    }

    public function subscribesTo(string $event): bool
    {
        $subscribedEvents = $this->definition->getClass()::getSubscribedEvents();

        return in_array($event, array_keys($subscribedEvents));
    }
}
