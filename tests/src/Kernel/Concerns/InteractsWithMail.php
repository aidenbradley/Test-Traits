<?php

namespace Drupal\Tests\test_traits\Kernel\Concerns;

use Drupal\helpers\Tests\TestMail;

trait InteractsWithMail
{
    public function getSentMail(): array
    {
        $mail = \Drupal::state()->get('system.test_mail_collector');

        return collect($mail)->mapInto(TestMail::class)->toArray();
    }

    public function countMailSent(): int
    {
        return count($this->getSentMail());
    }

    public function getMailSentTo(string $mailTo): array
    {
        $sentMail = [];

        /** @var TestMail $mail */
        foreach ($this->getSentMail() as $mail) {
            if ($mail->getTo() !== $mailTo) {
                continue;
            }

            $sentMail[] = $mail;
        }

        return $sentMail;
    }

    public function sentMailContainsTo(string $mailTo): bool
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
        \Drupal::state()->set('system.test_mail_collector', []);
    }
}
