<?php

namespace Drupal\Tests\test_traits\Unit;

use Drupal\Tests\test_traits\Kernel\Mail\TestMail;
use Drupal\Tests\UnitTestCase;

class TestMailTest extends UnitTestCase
{
    /** @test */
    public function get_to(): void
    {
        $mail = TestMail::createFromValues([
            'to' => 'hello@example.com',
        ]);

        $this->assertEquals('hello@example.com', $mail->getTo());
    }

    /** @test */
    public function get_subject(): void
    {
        $mail = TestMail::createFromValues([
            'subject' => 'email subject',
        ]);

        $this->assertEquals('email subject', $mail->getSubject());
    }

    /** @test */
    public function get_body(): void
    {
        $mail = TestMail::createFromValues([
            'body' => 'email body',
        ]);

        $this->assertEquals('email body', $mail->getBody());
    }

    /** @test */
    public function get_param(): void
    {
        $mail = TestMail::createFromValues([
            'params' => [
                'message' => 'mail message',
                'article_title' => 'arbitrary value',
            ],
        ]);

        $this->assertEquals('mail message', $mail->getParam('message'));
        $this->assertEquals('arbitrary value', $mail->getParam('article_title'));
    }
}
