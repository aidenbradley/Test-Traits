<?php

namespace Drupal\Tests\test_traits\Kernel\Mail;

use PHPUnit\Framework\Assert;

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

    public function assertSentTo(string $to): self
    {
        Assert::assertEquals($to, $this->getTo());

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->getValue('subject');
    }

    public function assertSubject(string $subject): self
    {
        Assert::assertEquals($subject, $this->getSubject());

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->getValue('body');
    }

    /** @param mixed body */
    public function assertBody($body): self
    {
        Assert::assertEquals($body, $this->getBody());

        return $this;
    }

    /** @return mixed */
    public function getParam(string $param)
    {
        if (isset($this->values['params'][$param]) === false) {
            return null;
        }

        return $this->values['params'][$param];
    }

    /**
     * @param mixed $value
     *
     * The closure will pass back the value attributed to the given parameter
     */
    public function assertParam(string $parameter, $value, ?\Closure $assertionCallback = null): self
    {
        $paramValue = $this->getParam($parameter);

        Assert::assertEquals($value, $paramValue);

        if ($assertionCallback !== null) {
            $assertionCallback($paramValue);
        }

        return $this;
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
