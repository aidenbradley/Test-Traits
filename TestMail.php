<?php

namespace Drupal\helpers\Tests;

class TestMail
{
    /** @var array */
    protected $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function getTo(): ?string
    {
        return $this->getValue('to');
    }

    public function getSubject(): ?string
    {
        return $this->getValue('submit');
    }

    public function getBody(): ?string
    {
        return $this->getValue('body');
    }

    /** @return mixed */
    private function getValue(string $keyName)
    {
        return $this->values[$keyName] ?? null;
    }
}