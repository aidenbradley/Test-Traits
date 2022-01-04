<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\InteractsWithMail;
use Drupal\Tests\test_traits\Kernel\Testing\Mail\TestMail;
use PHPUnit\Framework\Assert;

class InteractsWithMailTest extends KernelTestBase
{
    use InteractsWithMail;

    /** @var \Drupal\Core\Mail\MailManager */
    private $mailManager;

    private const SEND_MAIL = true;

    private const NO_REPLY = null;

    protected static $modules = [
        'test_traits_mail',
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->mailManager = $this->container->get('plugin.manager.mail');
    }

    /** @test */
    public function get_sent_mail(): void
    {
        $this->assertNoMailSent();

        $this->sendMail('hello@example.com', 'Hello', 'Hello, at example.com');

        $this->assertMailSent(1);

        $this->sendMail('hello@example.com', 'Hello', 'Hello, at example.com');

        $this->assertMailSent(2);
    }

    /** @test */
    public function get_mail_sent_to(): void
    {
        $this->assertEmpty($this->getMailSentTo('hello@example.com'));

        $this->sendMail('hello@example.com', 'Hello', 'Hello, at example.com');

        $this->assertNotEmpty($this->getMailSentTo('hello@example.com'));

        $this->assertMailSentTo('hello@example.com', function(TestMail $mail) {
            $mail->assertSentTo('hello@example.com');
            $mail->assertSubject('Hello');
        });
    }

    /** @test */
    public function get_mail_with_subject(): void
    {
        $this->assertEmpty($this->getMailWithSubject('User Registration'));

        $this->sendMail('hello@example.com', 'User Registration', 'Thanks for registering!');

        $this->assertNotEmpty($this->getMailWithSubject('User Registration'));

        $this->assertMailWithSubject('User Registration', function(TestMail $mail) {
            $mail->assertSentTo('hello@example.com');
        });
    }

    /** @test */
    public function multiple_get_mail_with_subject(): void
    {
        $this->assertEmpty($this->getMailWithSubject('User Registration'));

        $this->sendMail('hello@example.com', 'User Registration', 'Thanks for registering!');
        $this->sendMail('hello_again@example.com', 'User Registration', 'Thanks for registering again!');

        $this->assertNotEmpty($this->getMailWithSubject('User Registration'));

        $this->assertMailWithSubject('User Registration', function(TestMail $mail) {
            if ($mail->getTo() === 'hello@example.com') {
                $mail->assertBody('Thanks for registering!');
            }

            if ($mail->getTo() === 'hello_again@example.com') {
                $mail->assertBody('Thanks for registering again!');
            }
        });
    }

    /** @test */
    public function sent_mail_contains_subject(): void
    {
        $this->assertFalse($this->sentMailContainsSubject('User Registration'));

        $this->sendMail('hello@example.com', 'User Registration', 'Thanks for registering!');

        $this->assertTrue($this->sentMailContainsSubject('User Registration'));
    }

    /** @test */
    public function clear_mail(): void
    {
        $this->assertNoMailSent();

        $this->sendMail('hello@example.com', 'Hello', 'Hello, at example.com');

        $this->assertMailSent();

        $this->clearMail();

        $this->assertNoMailSent();
    }

    private function sendMail(string $to, string $subject, string $body, array $params = []): void
    {
        $state = $this->container->get('state');

        $state->set('test_traits.mail_subject', $subject);
        $state->set('test_traits.mail_body', $body);

        $this->mailManager->mail(
            'test_traits_mail',
            'test_traits_mail',
            $to,
            'en',
            $params,
            static::NO_REPLY,
            static::SEND_MAIL
        );
    }
}
