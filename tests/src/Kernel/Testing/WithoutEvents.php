<?php

namespace Drupal\Tests\test_traits\Kernel\Testing;

// needs to listen for modules being enabled during test runs so
// we can collate any new subscribers
use Drupal\Tests\test_traits\Kernel\Testing\Decorators\DecoratedDefinition;

trait WithoutEvents
{
    public function withoutEvents(): self
    {
        $subscribers = $this->container->findTaggedServiceIds('event_subscriber');

        foreach (array_keys($subscribers) as $subscriberName) {
            $this->container->removeDefinition($subscriberName);
        }

        return $this;
    }

    public function withoutEventsFromModule(string $module): self
    {
        $subscribers = $this->container->findTaggedServiceIds('event_subscriber');

        foreach (array_keys($subscribers) as $subscriberName) {
            $definition = DecoratedDefinition::createFromDefinition(
                $this->container->getDefinition($subscriberName)
            );

            if ($definition->hasProvider() && $definition->providerIs($module) === false) {
                continue;
            }

            $this->container->removeDefinition($subscriberName);
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

    public function withoutEventsFromClasses(array $classes): self
    {
        $subscribers = $this->container->findTaggedServiceIds('event_subscriber');

        foreach (array_keys($subscribers) as $subscriberName) {
            $definition = DecoratedDefinition::createFromDefinition(
                $this->container->getDefinition($subscriberName)
            );

            if ($definition->classInList($classes) === false) {
                continue;
            }

            $this->container->removeDefinition($subscriberName);
        }

        return $this;
    }
}
