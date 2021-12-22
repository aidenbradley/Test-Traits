<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Concerns\InteractsWithMail;

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
        $this->assertEmpty($this->getSentMail());

        $this->sendMail('hello@example.com', 'Hello', 'Hello, at example.com');

        $this->assertNotEmpty($this->getSentMail());
    }

    /** @test */
    public function count_mail_sent(): void
    {
        $this->assertEquals(0, $this->countMailSent());

        $this->sendMail('hello@example.com', 'Hello', 'Hello, at example.com');

        $this->assertEquals(1, $this->countMailSent());
    }

    /** @test */
    public function get_mail_sent_to(): void
    {
        $this->assertEmpty($this->getMailSentTo('hello@example.com'));

        $this->sendMail('hello@example.com', 'Hello', 'Hello, at example.com');

        $this->assertNotEmpty($this->getMailSentTo('hello@example.com'));
    }

    /** @test */
    public function sent_mail_contains_to_address(): void
    {
        $this->assertFalse(
            $this->sentMailContainsToAddress('hello@example.com')
        );

        $this->sendMail('hello@example.com', 'Hello', 'Hello, at example.com');

        $this->assertTrue(
            $this->sentMailContainsToAddress('hello@example.com')
        );
    }

    /** @test */
    public function get_mail_with_subject(): void
    {
        $this->assertEmpty($this->getMailWithSubject('User Registration'));

        $this->sendMail('hello@example.com', 'User Registration', 'Thanks for registering!');

        $this->assertNotEmpty($this->getMailWithSubject('User Registration'));
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
        $this->assertEmpty($this->getSentMail());

        $this->sendMail('hello@example.com', 'Hello', 'Hello, at example.com');

        $this->assertNotEmpty($this->getSentMail());

        $this->clearMail();

        $this->assertEmpty($this->getSentMail());
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
