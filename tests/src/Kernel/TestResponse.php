<?php

namespace Drupal\Tests\test_traits\Kernel;

use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response;

class TestResponse
{
    /** @var Response */
    private $response;

    public static function fromBaseResponse(Response $response)
    {
        return new self($response);
    }

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function assertStatusCode(int $statusCode): void
    {
        Assert::assertEquals($statusCode, $this->response->getStatusCode());
    }

    public function assertContinue(): void
    {
        $this->assertStatusCode(Response::HTTP_CONTINUE);
    }

    public function assertSwitchingProtocols(): void
    {
        $this->assertStatusCode(Response::HTTP_SWITCHING_PROTOCOLS);
    }

    public function assertProcessing(): void
    {
        $this->assertStatusCode(Response::HTTP_PROCESSING);
    }

    public function assertEarlyHints(): void
    {
        $this->assertStatusCode(Response::HTTP_EARLY_HINTS);
    }

    public function assertOk(): void
    {
        $this->assertStatusCode(Response::HTTP_OK);
    }

    public function assertCreated(): void
    {
        $this->assertStatusCode(Response::HTTP_CREATED);
    }

    public function assertAccepted(): void
    {
        $this->assertStatusCode(Response::HTTP_ACCEPTED);
    }

    public function assertNonAuthoritativeInformation(): void
    {
        $this->assertStatusCode(Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
    }

    public function assertNoContent(): void
    {
        $this->assertStatusCode(Response::HTTP_NO_CONTENT);
    }

    public function assertResetContent(): void
    {
        $this->assertStatusCode(Response::HTTP_RESET_CONTENT);
    }

    public function assertPartialContent(): void
    {
        $this->assertStatusCode(Response::HTTP_PARTIAL_CONTENT);
    }

    public function assertMultiStatus(): void
    {
        $this->assertStatusCode(Response::HTTP_MULTI_STATUS);
    }

    public function assertAlreadyReported(): void
    {
        $this->assertStatusCode(Response::HTTP_ALREADY_REPORTED);
    }

    public function assertImUsed(): void
    {
        $this->assertStatusCode(Response::HTTP_IM_USED);
    }

    public function assertMultipleChoices(): void
    {
        $this->assertStatusCode(Response::HTTP_MULTIPLE_CHOICES);
    }

    public function assertMovedPermanently(): void
    {
        $this->assertStatusCode(Response::HTTP_MOVED_PERMANENTLY);
    }

    public function assertFound(): void
    {
        $this->assertStatusCode(Response::HTTP_FOUND);
    }

    public function assertSeeOther(): void
    {
        $this->assertStatusCode(Response::HTTP_SEE_OTHER);
    }

    public function assertNotModified(): void
    {
        $this->assertStatusCode(Response::HTTP_NOT_MODIFIED);
    }

    public function assertUseProxy(): void
    {
        $this->assertStatusCode(Response::HTTP_USE_PROXY);
    }

    public function assertReserved(): void
    {
        $this->assertStatusCode(Response::HTTP_RESERVED);
    }

    public function assertTemporaryRedirect(): void
    {
        $this->assertStatusCode(Response::HTTP_TEMPORARY_REDIRECT);
    }

    public function assertPermanentlyRedirect(): void
    {
        $this->assertStatusCode(Response::HTTP_PERMANENTLY_REDIRECT);
    }

    public function assertBadRequest(): void
    {
        $this->assertStatusCode(Response::HTTP_BAD_REQUEST);
    }

    public function assertUnauthorized(): void
    {
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);
    }

    public function assertPaymentRequired(): void
    {
        $this->assertStatusCode(Response::HTTP_PAYMENT_REQUIRED);
    }

    public function assertForbidden(): void
    {
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);
    }

    public function assertNotFound(): void
    {
        $this->assertStatusCode(Response::HTTP_NOT_FOUND);
    }

    public function assertNotAcceptable(): void
    {
        $this->assertStatusCode(Response::HTTP_NOT_ACCEPTABLE);
    }

    public function assertProxyAuthenticationRequired(): void
    {
        $this->assertStatusCode(Response::HTTP_PROXY_AUTHENTICATION_REQUIRED);
    }

    public function assertRequestTimeout(): void
    {
        $this->assertStatusCode(Response::HTTP_REQUEST_TIMEOUT);
    }

    public function assertConflict(): void
    {
        $this->assertStatusCode(Response::HTTP_CONFLICT);
    }

    public function assertGone(): void
    {
        $this->assertStatusCode(Response::HTTP_GONE);
    }

    public function assertLengthRequired(): void
    {
        $this->assertStatusCode(Response::HTTP_LENGTH_REQUIRED);
    }

    public function assertPreconditionFailed(): void
    {
        $this->assertStatusCode(Response::HTTP_PRECONDITION_FAILED);
    }

    public function assertRequestEntityTooLarge(): void
    {
        $this->assertStatusCode(Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
    }

    public function assertRequestUriTooLong(): void
    {
        $this->assertStatusCode(Response::HTTP_REQUEST_URI_TOO_LONG);
    }

    public function assertUnsupportedMediaType(): void
    {
        $this->assertStatusCode(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
    }

    public function assertRequestRangeNotSatisfiable(): void
    {
        $this->assertStatusCode(Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE);
    }

    public function assertExpectationFailed(): void
    {
        $this->assertStatusCode(Response::HTTP_EXPECTATION_FAILED);
    }

    public function assertImATeapot(): void
    {
        $this->assertStatusCode(Response::HTTP_I_AM_A_TEAPOT);
    }

    public function assertMisdirectedRequest(): void
    {
        $this->assertStatusCode(Response::HTTP_MISDIRECTED_REQUEST);
    }

    public function assertUnprocessable(): void
    {
        $this->assertStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function assertLocked(): void
    {
        $this->assertStatusCode(Response::HTTP_LOCKED);
    }

    public function assertFailedDependency(): void
    {
        $this->assertStatusCode(Response::HTTP_FAILED_DEPENDENCY);
    }

    public function assertMethodNotAllowed(): void
    {
        $this->assertStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function assertRedirectedTo(string $uri): void
    {
        Assert::assertEquals($uri, $this->response->headers->get('location'));
    }

    public function assertJsonContent(array $json): void
    {
        Assert::assertEquals($json, (array) json_decode($this->response->getContent()));
    }

    public function assertJsonContentContains(array $json): void
    {
        $decodedResponse = (array) json_decode($this->response->getContent());

        foreach ($json as $key => $value) {
            Assert::assertEquals($value, $decodedResponse[$key]);
        }
    }

    public function assertLocation(string $uri): void
    {
        dump($this->response->headers->all());
        Assert::assertEquals($uri, $this->response->headers->get('Location'));
    }

    public function __call($name, $arguments)
    {
        if (method_exists($name, $this->response)) {
            return $this->response->$name(...$arguments);
        }

        return $this;
    }

    public function __get($name)
    {
        if (property_exists($this->response, $name)) {
            return $this->response->$name;
        }

        return $this;
    }
}
