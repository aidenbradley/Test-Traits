<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

use Drupal\Tests\test_traits\Kernel\Testing\Mail\TestMail;

trait InteractsWithMail
{
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

    public function countMailSent(): int
    {
        return count($this->getSentMail());
    }

    public function assertMailSentCount(int $count): self
    {
        $this->assertEquals($count, $this->countMailSent());

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

    public function assertMailSentTo(string $to, ?callable $callback = null): self
    {
        $mail = $this->getMailSentTo($to)->getTo();

        if ($mail === null) {
            $this->fail('No email was sent to ' . $to);
        }

        $this->assertEquals($to, $mail->getTo());

        if ($callback) {
            $callback($mail);
        }

        return $this;
    }

    public function sentMailContainsToAddress(string $mailTo): bool
    {
        /** @var TestMail $mail */
        foreach ($this->getSentMail() as $mail) {
            if ($mail->getTo() === $mailTo) {
                return true;
            }
        }

        return false;
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
