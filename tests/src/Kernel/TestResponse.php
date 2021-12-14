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

    public function assertOk(): void
    {
        Assert::assertEquals(Response::HTTP_OK, $this->response->getStatusCode());
    }

    public function assertNotFound(): void
    {
        Assert::assertEquals(Response::HTTP_NOT_FOUND, $this->response->getStatusCode());
    }

    public function assertNoContent(): void
    {
        Assert::assertEquals(Response::HTTP_NO_CONTENT, $this->response->getStatusCode());
    }

    public function assertUnprocessable(): void
    {
        Assert::assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());
    }

    public function assertRedirectedTo(string $uri): void
    {
        Assert::assertEquals($uri, $this->response->headers->get('location'));
    }

    public function assertMethodNotAllowed(): void
    {
        Assert::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->response->getStatusCode());
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
