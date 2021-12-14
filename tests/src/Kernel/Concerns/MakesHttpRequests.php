<?php

namespace Drupal\Tests\test_traits\Kernel\Concerns;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait MakesHttpRequests
{
    /** @var Request */
    protected $request;

    /** @var \Symfony\Contracts\HttpClient\ResponseInterface */
    protected $response;

    /** @var null|bool */
    protected $requestIsAjax = null;

    public function get(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): self
    {
        return $this->handleRequest(
            Request::create($uri, 'GET', $parameters, $cookies, $files, $server, $content)
        );
    }

    public function post(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): self
    {
        return $this->handleRequest(
            Request::create($uri, 'POST', $parameters, $cookies, $files, $server, $content)
        );
    }

    public function put(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): self
    {
        return $this->handleRequest(
            Request::create($uri, 'PUT', $parameters, $cookies, $files, $server, $content)
        );
    }

    public function delete(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): self
    {
        return $this->handleRequest(
            Request::create($uri, 'DELETE', $parameters, $cookies, $files, $server, $content)
        );
    }

    public function ajax(): self
    {
        $this->requestIsAjax = true;

        return $this;
    }

    public function handleRequest(Request $request): self
    {
        $this->container->get('kernel')->invalidateContainer();
        $this->container->get('kernel')->rebuildContainer();

        $this->request = $request;

        $this->request->setSession($this->container->get('session'));

        if ($this->requestIsAjax !== null) {
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');

            $this->requestIsAjax = null;
        }

        $this->response = $this->container->get('http_kernel')->handle($this->request);

        return $this;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function assertOk(): void
    {
        $this->assertEquals(Response::HTTP_OK, $this->response->getStatusCode());
    }

    public function assertNotFound(): void
    {
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->response->getStatusCode());
    }

    public function assertNoContent(): void
    {
        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->response->getStatusCode());
    }

    public function assertUnprocessable(): void
    {
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());
    }

    public function assertRedirectedTo(string $uri): void
    {
        $this->assertEquals($uri, $this->response->headers->get('location'));
    }

    public function assertMethodNotAllowed(): void
    {
        $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->response->getStatusCode());
    }

    public function assertJsonContent(array $json): void
    {
        $this->assertEquals($json, json_decode($this->response->getContent()));
    }

    public function assertJsonContentContains(array $json): void
    {
        $decodedResponse = json_decode($this->response->getContent());

        foreach ($json as $key => $value) {
            $this->assertEquals($value, $decodedResponse->{$key});
        }
    }

    public function followRedirect(): self
    {
        return $this->get(
            $this->response->headers->get('location')
        );
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }
}
