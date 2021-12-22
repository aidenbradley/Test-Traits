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

    protected function setUp()
    {
        parent::setUp();

        $this->mailManager = $this->container->get('plugin.manager.mail');
    }

    /** @test */
    public function get_sent_mail(): void
    {
        $this->assertEmpty($this->getSentMail());

        $this->sendMail('hello@example.com', [
            'message' => 'Hello, at example.com',
        ]);

        $this->assertNotEmpty($this->getSentMail());
    }

    /** @test */
    public function count_mail_sent(): void
    {
        $this->assertEquals(0, $this->countMailSent());

        $this->sendMail('hello@example.com', [
            'message' => 'Hello, at example.com',
        ]);

        $this->assertEquals(1, $this->countMailSent());
    }

    /** @test */
    public function get_mail_sent_to(): void
    {
        $this->assertEmpty($this->getMailSentTo('hello@example.com'));

        $this->sendMail('hello@example.com', [
            'message' => 'Hello, at example.com',
        ]);

        $this->assertNotEmpty($this->getMailSentTo('hello@example.com'));
    }

    /** @test */
    public function clear_mail(): void
    {
        $this->assertEmpty($this->getSentMail());

        $this->sendMail('hello@example.com', [
            'message' => 'Hello, at example.com',
        ]);

        $this->assertNotEmpty($this->getSentMail());

        $this->clearMail();

        $this->assertEmpty($this->getSentMail());
    }

    private function sendMail(string $to, array $params): void
    {
        $this->mailManager->mail('test_traits', 'send_email', $to, 'en', $params, null, static::SEND_MAIL);
    }
}
