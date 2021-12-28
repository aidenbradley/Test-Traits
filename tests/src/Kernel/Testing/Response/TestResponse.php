<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Response;

use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response;

class TestResponse extends Response
{
    public static function fromBaseResponse(Response $response)
    {
        return new static(
            $response->getContent(),
            $response->getStatusCode(),
            $response->headers->all()
        );
    }

    /** @return static */
    public function assertStatusCode(int $statusCode)
    {
        Assert::assertEquals($statusCode, $this->getStatusCode());

        return $this;
    }

    /** @return static */
    public function assertContinue()
    {
        $this->assertStatusCode(Response::HTTP_CONTINUE);

        return $this;
    }

    /** @return static */
    public function assertSwitchingProtocols()
    {
        $this->assertStatusCode(Response::HTTP_SWITCHING_PROTOCOLS);

        return $this;
    }

    /** @return static */
    public function assertProcessing()
    {
        $this->assertStatusCode(Response::HTTP_PROCESSING);

        return $this;
    }

    /** @return static */
    public function assertEarlyHints()
    {
        $this->assertStatusCode(Response::HTTP_EARLY_HINTS);

        return $this;
    }

    /** @return static */
    public function assertOk()
    {
        $this->assertStatusCode(Response::HTTP_OK);

        return $this;
    }

    /** @return static */
    public function assertCreated()
    {
        $this->assertStatusCode(Response::HTTP_CREATED);

        return $this;
    }

    /** @return static */
    public function assertAccepted()
    {
        $this->assertStatusCode(Response::HTTP_ACCEPTED);

        return $this;
    }

    /** @return static */
    public function assertNonAuthoritativeInformation()
    {
        $this->assertStatusCode(Response::HTTP_NON_AUTHORITATIVE_INFORMATION);

        return $this;
    }

    /** @return static */
    public function assertNoContent()
    {
        $this->assertStatusCode(Response::HTTP_NO_CONTENT);

        return $this;
    }

    /** @return static */
    public function assertResetContent()
    {
        $this->assertStatusCode(Response::HTTP_RESET_CONTENT);

        return $this;
    }

    /** @return static */
    public function assertPartialContent()
    {
        $this->assertStatusCode(Response::HTTP_PARTIAL_CONTENT);

        return $this;
    }

    /** @return static */
    public function assertMultiStatus()
    {
        $this->assertStatusCode(Response::HTTP_MULTI_STATUS);

        return $this;
    }

    /** @return static */
    public function assertAlreadyReported()
    {
        $this->assertStatusCode(Response::HTTP_ALREADY_REPORTED);

        return $this;
    }

    /** @return static */
    public function assertImUsed()
    {
        $this->assertStatusCode(Response::HTTP_IM_USED);

        return $this;
    }

    /** @return static */
    public function assertMultipleChoices()
    {
        $this->assertStatusCode(Response::HTTP_MULTIPLE_CHOICES);

        return $this;
    }

    /** @return static */
    public function assertMovedPermanently()
    {
        $this->assertStatusCode(Response::HTTP_MOVED_PERMANENTLY);

        return $this;
    }

    /** @return static */
    public function assertFound()
    {
        $this->assertStatusCode(Response::HTTP_FOUND);

        return $this;
    }

    /** @return static */
    public function assertSeeOther()
    {
        $this->assertStatusCode(Response::HTTP_SEE_OTHER);

        return $this;
    }

    /** @return static */
    public function assertNotModified()
    {
        $this->assertStatusCode(Response::HTTP_NOT_MODIFIED);

        return $this;
    }

    /** @return static */
    public function assertUseProxy()
    {
        $this->assertStatusCode(Response::HTTP_USE_PROXY);

        return $this;
    }

    /** @return static */
    public function assertReserved()
    {
        $this->assertStatusCode(Response::HTTP_RESERVED);

        return $this;
    }

    /** @return static */
    public function assertTemporaryRedirect()
    {
        $this->assertStatusCode(Response::HTTP_TEMPORARY_REDIRECT);

        return $this;
    }

    /** @return static */
    public function assertPermanentlyRedirect()
    {
        $this->assertStatusCode(Response::HTTP_PERMANENTLY_REDIRECT);

        return $this;
    }

    /** @return static */
    public function assertBadRequest()
    {
        $this->assertStatusCode(Response::HTTP_BAD_REQUEST);

        return $this;
    }

    /** @return static */
    public function assertUnauthorized()
    {
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        return $this;
    }

    /** @return static */
    public function assertPaymentRequired()
    {
        $this->assertStatusCode(Response::HTTP_PAYMENT_REQUIRED);

        return $this;
    }

    /** @return static */
    public function assertForbidden()
    {
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        return $this;
    }

    /** @return static */
    public function assertNotFound()
    {
        $this->assertStatusCode(Response::HTTP_NOT_FOUND);

        return $this;
    }

    /** @return static */
    public function assertNotAcceptable()
    {
        $this->assertStatusCode(Response::HTTP_NOT_ACCEPTABLE);

        return $this;
    }

    /** @return static */
    public function assertProxyAuthenticationRequired()
    {
        $this->assertStatusCode(Response::HTTP_PROXY_AUTHENTICATION_REQUIRED);

        return $this;
    }

    /** @return static */
    public function assertRequestTimeout()
    {
        $this->assertStatusCode(Response::HTTP_REQUEST_TIMEOUT);

        return $this;
    }

    /** @return static */
    public function assertConflict()
    {
        $this->assertStatusCode(Response::HTTP_CONFLICT);

        return $this;
    }

    /** @return static */
    public function assertGone()
    {
        $this->assertStatusCode(Response::HTTP_GONE);

        return $this;
    }

    /** @return static */
    public function assertLengthRequired()
    {
        $this->assertStatusCode(Response::HTTP_LENGTH_REQUIRED);

        return $this;
    }

    /** @return static */
    public function assertPreconditionFailed()
    {
        $this->assertStatusCode(Response::HTTP_PRECONDITION_FAILED);

        return $this;
    }

    /** @return static */
    public function assertRequestEntityTooLarge()
    {
        $this->assertStatusCode(Response::HTTP_REQUEST_ENTITY_TOO_LARGE);

        return $this;
    }

    /** @return static */
    public function assertRequestUriTooLong()
    {
        $this->assertStatusCode(Response::HTTP_REQUEST_URI_TOO_LONG);

        return $this;
    }

    /** @return static */
    public function assertUnsupportedMediaType()
    {
        $this->assertStatusCode(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);

        return $this;
    }

    /** @return static */
    public function assertRequestRangeNotSatisfiable()
    {
        $this->assertStatusCode(Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE);

        return $this;
    }

    /** @return static */
    public function assertExpectationFailed()
    {
        $this->assertStatusCode(Response::HTTP_EXPECTATION_FAILED);

        return $this;
    }

    /** @return static */
    public function assertImATeapot()
    {
        $this->assertStatusCode(Response::HTTP_I_AM_A_TEAPOT);

        return $this;
    }

    /** @return static */
    public function assertMisdirectedRequest()
    {
        $this->assertStatusCode(Response::HTTP_MISDIRECTED_REQUEST);

        return $this;
    }

    /** @return static */
    public function assertUnprocessable()
    {
        $this->assertStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);

        return $this;
    }

    /** @return static */
    public function assertLocked()
    {
        $this->assertStatusCode(Response::HTTP_LOCKED);

        return $this;
    }

    /** @return static */
    public function assertFailedDependency()
    {
        $this->assertStatusCode(Response::HTTP_FAILED_DEPENDENCY);

        return $this;
    }

    /** @return static */
    public function assertMethodNotAllowed()
    {
        $this->assertStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);

        return $this;
    }

    /** @return static */
    public function assertJsonContent(array $json)
    {
        Assert::assertEquals($json, (array) json_decode($this->getContent()));

        return $this;
    }

    /** @return static */
    public function assertJsonContentContains(array $json)
    {
        $decodedResponse = (array) json_decode($this->getContent());

        foreach ($json as $key => $value) {
            Assert::assertEquals($value, $decodedResponse[$key]);
        }

        return $this;
    }

    /** @return static */
    public function assertLocation(string $uri)
    {
        Assert::assertEquals($uri, \Drupal::service('path.current')->getPath());

        return $this;
    }

    /** @return static */
    public function assertRedirect(?string $uri = null)
    {
        Assert::assertTrue($this->isRedirect());

        if ($uri !== null) {
            Assert::assertEquals($uri, $this->headers->get('Location'));
        }

        return $this;
    }
}
