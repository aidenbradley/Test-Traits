<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Decorators;

use Symfony\Component\DependencyInjection\Definition;

class DecoratedDefinition
{
    /** @var Definition */
    private $definition;

    public static function createFromDefinition(Definition $definition): self
    {
        return new self($definition);
    }

    public function __construct(Definition $definition)
    {
        $this->definition = $definition;
    }

    public function hasProperties(): bool
    {
        return $this->definition->getProperties() !== [];
    }

    public function getProperties(): array
    {
        return $this->definition->getProperties();
    }

    public function hasServiceid(): bool
    {
        if ($this->hasProperties() === false) {
            return false;
        }

        $properties = $this->getProperties();

        if (isset($properties['_serviceId']) === false || $properties['_serviceId'] === '') {
            return false;
        }

        return true;
    }

    public function getServiceId(): ?string
    {
        if ($this->hasServiceId() === false) {
            return null;
        }

        return $this->getProperties()['_serviceId'];
    }

    public function hasProvider(): bool
    {
        $tags = $this->definition->getTags();

        if (isset($tags['_provider']) === false) {
            return false;
        }

        if (isset($tags['_provider'][0]) === false) {
            return false;
        }

        if (isset($tags['_provider'][0]['provider']) === false) {
            return false;
        }

        return true;
    }

    public function getProvider(): ?string
    {
        if ($this->hasProvider() === false) {
            return null;
        }

        return $this->definition->getTags()['_provider'][0]['provider'];
    }

    public function providerIs(string $module): bool
    {
        if ($this->hasProvider() === false) {
            return false;
        }

        return $this->getProvider() === $module;
    }

    public function getClass(): ?string
    {
        return $this->definition->getClass();
    }

    public function isClass(string $class): bool
    {
        return $this->getClass() === $class;
    }

    /** @return mixed */
    public function __call(string $method, array $arguments)
    {
        if (method_exists($this->definition, $method)) {
            $return = $this->definition->$method(...$arguments);

            if ($return instanceof Definition) {
                return $return;
            }
        }

        return $this;
    }

    /** @return mixed */
    public function __get(string $name)
    {
        if (property_exists($this->definition, $name)) {
            return $this->definition->$name;
        }

        return $this;
    }

    public function __set(string $name, $value): void
    {
        if (property_exists($this->definition, $name) === false) {
            return;
        }

        $this->definition->$name = $value;
    }
}
