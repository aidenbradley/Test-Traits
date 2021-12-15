<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function assert_multi_status(): void
    {
        $this->createMockResponse(207)->assertMultiStatus();
    }

    /** @test */
    public function assert_already_reported(): void
    {
        $this->createMockResponse(208)->assertAlreadyReported();
    }

    /** @test */
    public function assert_im_used(): void
    {
        $this->createMockResponse(226)->assertImUsed();
    }

    /** @test */
    public function assert_multiple_choices(): void
    {
        $this->createMockResponse(300)->assertMultipleChoices();
    }

    /** @test */
    public function assert_moved_permanently(): void
    {
        $this->createMockResponse(301)->assertMovedPermanently();
    }

    /** @test */
    public function assert_found(): void
    {
        $this->createMockResponse(302)->assertFound();
    }

    /** @test */
    public function assert_see_other(): void
    {
        $this->createMockResponse(303)->assertSeeOther();
    }

    /** @test */
    public function assert_not_modified(): void
    {
        $this->createMockResponse(304)->assertNotModified();
    }

    /** @test */
    public function assert_use_proxy(): void
    {
        $this->createMockResponse(305)->assertUseProxy();
    }

    /** @test */
    public function assert_reserved(): void
    {
        $this->createMockResponse(306)->assertReserved();
    }

    /** @test */
    public function assert_temporary_redirect(): void
    {
        $this->createMockResponse(307)->assertTemporaryRedirect();
    }

    /** @test */
    public function assert_permanently_redirect(): void
    {
        $this->createMockResponse(308)->assertPermanentlyRedirect();
    }

    /** @test */
    public function assert_bad_request(): void
    {
        $this->createMockResponse(400)->assertBadRequest();
    }

    /** @test */
    public function assert_unauthorized(): void
    {
        $this->createMockResponse(401)->assertUnauthorized();
    }

    /** @test */
    public function assert_payment_required(): void
    {
        $this->createMockResponse(402)->assertPaymentRequired();
    }

    /** @test */
    public function assert_forbidden(): void
    {
        $this->createMockResponse(403)->assertForbidden();
    }

    /** @test */
    public function assert_not_found(): void
    {
        $this->createMockResponse(404)->assertNotFound();
    }

    /** @test */
    public function assert_method_not_allowed(): void
    {
        $this->createMockResponse(405)->assertMethodNotAllowed();
    }

    /** @test */
    public function assert_not_acceptable(): void
    {
        $this->createMockResponse(406)->assertNotAcceptable();
    }

    /** @test */
    public function assert_proxy_authentication_required(): void
    {
        $this->createMockResponse(407)->assertProxyAuthenticationRequired();
    }

    /** @test */
    public function assert_request_timeout(): void
    {
        $this->createMockResponse(408)->assertRequestTimeout();
    }

    /** @test */
    public function assert_conflict(): void
    {
        $this->createMockResponse(409)->assertConflict();
    }

    /** @test */
    public function assert_gone(): void
    {
        $this->createMockResponse(410)->assertGone();
    }

    /** @test */
    public function assert_length_required(): void
    {
        $this->createMockResponse(411)->assertLengthRequired();
    }

    /** @test */
    public function assert_precondition_failed(): void
    {
        $this->createMockResponse(412)->assertPreconditionFailed();
    }

    /** @test */
    public function assert_request_entity_too_large(): void
    {
        $this->createMockResponse(413)->assertRequestEntityTooLarge();
    }

    /** @test */
    public function assert_request_uri_too_long(): void
    {
        $this->createMockResponse(414)->assertRequestUriTooLong();
    }

    /** @test */
    public function assert_unsupported_media_type(): void
    {
        $this->createMockResponse(415)->assertUnsupportedMediaType();
    }

    /** @test */
    public function assert_request_range_not_satisfiable(): void
    {
        $this->createMockResponse(416)->assertRequestRangeNotSatisfiable();
    }

    /** @test */
    public function assert_expectation_failed(): void
    {
        $this->createMockResponse(417)->assertExpectationFailed();
    }

    /** @test */
    public function assert_i_am_a_teapot(): void
    {
        $this->createMockResponse(418)->assertImATeapot();
    }

    /** @test */
    public function assert_misdirected_request(): void
    {
        $this->createMockResponse(421)->assertMisdirectedRequest();
    }

    /** @test */
    public function assert_unprocessable_entity(): void
    {
        $this->createMockResponse(422)->assertUnprocessable();
    }

    /** @test */
    public function assert_locked(): void
    {
        $this->createMockResponse(423)->assertLocked();
    }

    /** @test */
    public function assert_failed_dependency(): void
    {
        $this->createMockResponse(424)->assertFailedDependency();
    }

    /** @test */
    public function assert_json_content(): void
    {
        $content = [
            'key' => 'value',
            'another_key' => 'another_value',
            'example' => 'example',
        ];

        $response = TestResponse::fromBaseResponse(
            JsonResponse::create($content)
        );

        $response->assertJsonContent($content);
    }

    /** @test */
    public function assert_json_content_contains(): void
    {
        $content = [
            'key' => 'value',
            'another_key' => 'another_value',
            'example' => 'example',
        ];

        $response = TestResponse::fromBaseResponse(
            JsonResponse::create($content)
        );

        $response->assertJsonContentContains(['key' => 'value']);
        $response->assertJsonContentContains(['another_key' => 'another_value']);
        $response->assertJsonContentContains(['example' => 'example']);
    }

    /** @param mixed $content */
    private function createMockResponse(int $statusCode, $content = ''): TestResponse
    {
        return TestResponse::fromBaseResponse(
            Response::create($content, $statusCode)
        );
    }
}
