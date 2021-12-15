<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TestResponseTest extends UnitTestCase
{
    /** @test */
    public function assert_continue(): void
    {
        $this->createMockResponse(Response::HTTP_CONTINUE)->assertContinue();
    }

    /** @test */
    public function assert_switching_protocols(): void
    {
        $this->createMockResponse(Response::HTTP_SWITCHING_PROTOCOLS)->assertSwitchingProtocols();
    }

    /** @test */
    public function assert_processing(): void
    {
        $this->createMockResponse(Response::HTTP_PROCESSING)->assertProcessing();
    }

    /** @test */
    public function assert_early_hints(): void
    {
        $this->createMockResponse(Response::HTTP_EARLY_HINTS)->assertEarlyHints();
    }

    /** @test */
    public function assert_ok(): void
    {
        $this->createMockResponse(Response::HTTP_OK)->assertOk();
    }

    /** @test */
    public function assert_created(): void
    {
        $this->createMockResponse(Response::HTTP_CREATED)->assertCreated();
    }

    /** @test */
    public function assert_accepted(): void
    {
        $this->createMockResponse(Response::HTTP_ACCEPTED)->assertAccepted();
    }

    /** @test */
    public function assert_non_authoritative_information(): void
    {
        $this->createMockResponse(Response::HTTP_NON_AUTHORITATIVE_INFORMATION)->assertNonAuthoritativeInformation();
    }

    /** @test */
    public function assert_no_content(): void
    {
        $this->createMockResponse(Response::HTTP_NO_CONTENT)->assertNoContent();
    }

    /** @test */
    public function assert_reset_content(): void
    {
        $this->createMockResponse(Response::HTTP_RESET_CONTENT)->assertResetContent();
    }

    /** @test */
    public function assert_partial_content(): void
    {
        $this->createMockResponse(Response::HTTP_PARTIAL_CONTENT)->assertPartialContent();
    }

    /** @test */
    public function assert_multi_status(): void
    {
        $this->createMockResponse(Response::HTTP_MULTI_STATUS)->assertMultiStatus();
    }

    /** @test */
    public function assert_already_reported(): void
    {
        $this->createMockResponse(Response::HTTP_ALREADY_REPORTED)->assertAlreadyReported();
    }

    /** @test */
    public function assert_im_used(): void
    {
        $this->createMockResponse(Response::HTTP_IM_USED)->assertImUsed();
    }

    /** @test */
    public function assert_multiple_choices(): void
    {
        $this->createMockResponse(Response::HTTP_MULTIPLE_CHOICES)->assertMultipleChoices();
    }

    /** @test */
    public function assert_moved_permanently(): void
    {
        $this->createMockResponse(Response::HTTP_MOVED_PERMANENTLY)->assertMovedPermanently();
    }

    /** @test */
    public function assert_found(): void
    {
        $this->createMockResponse(Response::HTTP_FOUND)->assertFound();
    }

    /** @test */
    public function assert_see_other(): void
    {
        $this->createMockResponse(Response::HTTP_SEE_OTHER)->assertSeeOther();
    }

    /** @test */
    public function assert_not_modified(): void
    {
        $this->createMockResponse(Response::HTTP_NOT_MODIFIED)->assertNotModified();
    }

    /** @test */
    public function assert_use_proxy(): void
    {
        $this->createMockResponse(Response::HTTP_USE_PROXY)->assertUseProxy();
    }

    /** @test */
    public function assert_reserved(): void
    {
        $this->createMockResponse(Response::HTTP_RESERVED)->assertReserved();
    }

    /** @test */
    public function assert_temporary_redirect(): void
    {
        $this->createMockResponse(Response::HTTP_TEMPORARY_REDIRECT)->assertTemporaryRedirect();
    }

    /** @test */
    public function assert_permanently_redirect(): void
    {
        $this->createMockResponse(Response::HTTP_PERMANENTLY_REDIRECT)->assertPermanentlyRedirect();
    }

    /** @test */
    public function assert_bad_request(): void
    {
        $this->createMockResponse(Response::HTTP_BAD_REQUEST)->assertBadRequest();
    }

    /** @test */
    public function assert_unauthorized(): void
    {
        $this->createMockResponse(Response::HTTP_UNAUTHORIZED)->assertUnauthorized();
    }

    /** @test */
    public function assert_payment_required(): void
    {
        $this->createMockResponse(Response::HTTP_PAYMENT_REQUIRED)->assertPaymentRequired();
    }

    /** @test */
    public function assert_forbidden(): void
    {
        $this->createMockResponse(Response::HTTP_FORBIDDEN)->assertForbidden();
    }

    /** @test */
    public function assert_not_found(): void
    {
        $this->createMockResponse(Response::HTTP_NOT_FOUND)->assertNotFound();
    }

    /** @test */
    public function assert_method_not_allowed(): void
    {
        $this->createMockResponse(Response::HTTP_METHOD_NOT_ALLOWED)->assertMethodNotAllowed();
    }

    /** @test */
    public function assert_not_acceptable(): void
    {
        $this->createMockResponse(Response::HTTP_NOT_ACCEPTABLE)->assertNotAcceptable();
    }

    /** @test */
    public function assert_proxy_authentication_required(): void
    {
        $this->createMockResponse(Response::HTTP_PROXY_AUTHENTICATION_REQUIRED)->assertProxyAuthenticationRequired();
    }

    /** @test */
    public function assert_request_timeout(): void
    {
        $this->createMockResponse(Response::HTTP_REQUEST_TIMEOUT)->assertRequestTimeout();
    }

    /** @test */
    public function assert_conflict(): void
    {
        $this->createMockResponse(Response::HTTP_CONFLICT)->assertConflict();
    }

    /** @test */
    public function assert_gone(): void
    {
        $this->createMockResponse(Response::HTTP_GONE)->assertGone();
    }

    /** @test */
    public function assert_length_required(): void
    {
        $this->createMockResponse(Response::HTTP_LENGTH_REQUIRED)->assertLengthRequired();
    }

    /** @test */
    public function assert_precondition_failed(): void
    {
        $this->createMockResponse(Response::HTTP_PRECONDITION_FAILED)->assertPreconditionFailed();
    }

    /** @test */
    public function assert_request_entity_too_large(): void
    {
        $this->createMockResponse(Response::HTTP_REQUEST_ENTITY_TOO_LARGE)->assertRequestEntityTooLarge();
    }

    /** @test */
    public function assert_request_uri_too_long(): void
    {
        $this->createMockResponse(Response::HTTP_REQUEST_URI_TOO_LONG)->assertRequestUriTooLong();
    }

    /** @test */
    public function assert_unsupported_media_type(): void
    {
        $this->createMockResponse(Response::HTTP_UNSUPPORTED_MEDIA_TYPE)->assertUnsupportedMediaType();
    }

    /** @test */
    public function assert_request_range_not_satisfiable(): void
    {
        $this->createMockResponse(Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE)->assertRequestRangeNotSatisfiable();
    }

    /** @test */
    public function assert_expectation_failed(): void
    {
        $this->createMockResponse(Response::HTTP_EXPECTATION_FAILED)->assertExpectationFailed();
    }

    /** @test */
    public function assert_i_am_a_teapot(): void
    {
        $this->createMockResponse(Response::HTTP_I_AM_A_TEAPOT)->assertImATeapot();
    }

    /** @test */
    public function assert_misdirected_request(): void
    {
        $this->createMockResponse(Response::HTTP_MISDIRECTED_REQUEST)->assertMisdirectedRequest();
    }

    /** @test */
    public function assert_unprocessable_entity(): void
    {
        $this->createMockResponse(Response::HTTP_UNPROCESSABLE_ENTITY)->assertUnprocessable();
    }

    /** @test */
    public function assert_locked(): void
    {
        $this->createMockResponse(Response::HTTP_LOCKED)->assertLocked();
    }

    /** @test */
    public function assert_failed_dependency(): void
    {
        $this->createMockResponse(Response::HTTP_FAILED_DEPENDENCY)->assertFailedDependency();
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
