<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

use Drupal\Tests\test_traits\Kernel\Testing\Mail\TestMail;

trait InteractsWithMail
{
    use HasClosureAssertions;

    public function getSentMail(): array
    {
        $mail = $this->container->get('state')->get('system.test_mail_collector');

        if ($mail === null) {
            return [];
        }

        return array_map(function(array $mailData) {
            return TestMail::createFromValues($mailData);
        }, $mail);
    }

    public function assertMailSent(?int $numberOfMailSent = null): self
    {
        $mail = $this->getSentMail();

        $this->assertNotEmpty($mail);

        if ($numberOfMailSent) {
            $this->assertEquals($numberOfMailSent, count($mail));
        }

        return $this;
    }

    public function assertNoMailSent(): self
    {
        $this->assertEmpty($this->getSentMail());

        return $this;
    }

    public function getMailSentTo(string $mailTo): ?TestMail
    {
        /** @var TestMail $mail */
        foreach ($this->getSentMail() as $mail) {
            if ($mail->getTo() !== $mailTo) {
                continue;
            }

            return $mail;
        }

        return null;
    }

    public function assertMailSentTo(string $to, ?\Closure $callback = null): self
    {
        $mail = $this->getMailSentTo($to);

        if ($mail === null) {
            $this->fail('No email was sent to ' . $to);
        }

        $this->assertEquals($to, $mail->getTo());

        if ($callback) {
            $this->addClosureAssertion($callback, $mail);
        }

        return $this;
    }

    public function getMailWithSubject(string $subject): array
    {
        $sentMail = [];

        /** @var TestMail $mail */
        foreach ($this->getSentMail() as $mail) {
            if ($mail->getSubject() !== $subject) {
                continue;
            }

            $sentMail[] = $mail;
        }

        return $sentMail;
    }

    public function assertMailWithSubject(string $subject, ?callable $callback = null): self
    {
        $mailItems = $this->getMailWithSubject($subject);

        if ($mailItems === []) {
            $this->fail('No email was sent with subject ' . $subject);
        }

        foreach ($mailItems as $mail) {
            $this->assertEquals($subject, $mail->getSubject());

            if ($callback === null) {
                continue;
            }

            $this->addClosureAssertion($callback, $mail);
        }

        return $this;
    }

    public function sentMailContainsSubject(string $subject): bool
    {
        /** @var TestMail $mail */
        foreach ($this->getSentMail() as $mail) {
            if ($mail->getSubject() === $subject) {
                return true;
            }
        }

        return false;
    }

    public function clearMail(): void
    {
        $this->container->get('state')->set('system.test_mail_collector', []);
    }
}
