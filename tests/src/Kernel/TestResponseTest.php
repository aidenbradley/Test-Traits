<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Symfony\Component\HttpFoundation\Response;

class TestResponseTest extends KernelTestBase
{
    protected static $modules = [
        'test_traits_test',
    ];

    /** @test */
    public function assert_continue(): void
    {
        $this->createMockResponse(100)->assertContinue();
    }

    /** @test */
    public function assert_switching_protocols(): void
    {
        $this->createMockResponse(101)->assertSwitchingProtocols();
    }

    /** @test */
    public function assert_processing(): void
    {
        $this->createMockResponse(102)->assertProcessing();
    }

    /** @test */
    public function assert_early_hints(): void
    {
        $this->createMockResponse(103)->assertEarlyHints();
    }

    /** @test */
    public function assert_ok(): void
    {
        $this->createMockResponse(200)->assertOk();
    }

    /** @test */
    public function assert_created(): void
    {
        $this->createMockResponse(201)->assertCreated();
    }

    /** @test */
    public function assert_accepted(): void
    {
        $this->createMockResponse(202)->assertAccepted();
    }

    /** @test */
    public function assert_non_authoritative_information(): void
    {
        $this->createMockResponse(203)->assertNonAuthoritativeInformation();
    }

    /** @test */
    public function assert_no_content(): void
    {
        $this->createMockResponse(204)->assertNoContent();
    }

    /** @test */
    public function assert_reset_content(): void
    {
        $this->createMockResponse(205)->assertResetContent();
    }

    /** @test */
    public function assert_partial_content(): void
    {
        $this->createMockResponse(206)->assertPartialContent();
    }

    /** @test */
    public function assert_not_found(): void
    {
        $this->createMockResponse(404)->assertNotFound();
    }

    /** @param mixed $content */
    private function createMockResponse(int $statusCode, $content = ''): TestResponse
    {
        return TestResponse::fromBaseResponse(
            Response::create($content, $statusCode)
        );
    }
}
