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

    public function assertNotFound(): void
    {
        $this->assertStatusCode(Response::HTTP_NOT_FOUND);
    }

    public function assertUnprocessable(): void
    {
        $this->assertStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
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
        Assert::assertEquals($json, json_decode($this->response->getContent()));
    }

    public function assertJsonContentContains(array $json): void
    {
        $decodedResponse = json_decode($this->response->getContent());

        foreach ($json as $key => $value) {
            Assert::assertEquals($value, $decodedResponse->{$key});
        }
    }
}
