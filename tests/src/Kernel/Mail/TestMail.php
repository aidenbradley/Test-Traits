<?php

namespace Drupal\Tests\test_traits\Kernel\Mail;

class TestMail
{
    /** @var array */
    protected $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public static function createFromValues(array $values): self
    {
        return new static($values);
    }

    public function getTo(): ?string
    {
        return $this->getValue('to');
    }

    public function getSubject(): ?string
    {
        return $this->getValue('subject');
    }

    public function getBody(): ?string
    {
        return $this->getValue('body');
    }

    /** @return mixed */
    public function getParam(string $param)
    {
        if (isset($this->values['params'][$param]) === false) {
            return null;
        }

        return $this->values['params'][$param];
    }

    public function toArray(): array
    {
        return $this->values;
    }

    /** @return mixed */
    private function getValue(string $keyName)
    {
        return $this->values[$keyName] ?? null;
    }
}
