<?php

namespace Drupal\helpers\Concerns\Tests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** To be used in KernelTests */
trait MakesHttpRequests
{
    /** @var Request */
    protected $request;

    /** @var \Symfony\Contracts\HttpClient\ResponseInterface */
    protected $response;

    /** @phpstan-ignore-next-line */
    public function get(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): self
    {
        return $this->handleRequest(
            Request::create($uri, 'GET', $parameters, $cookies, $files, $server, $content)
        );
    }

    /** @phpstan-ignore-next-line */
    public function post(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): self
    {
        return $this->handleRequest(
            Request::create($uri, 'POST', $parameters, $cookies, $files, $server, $content)
        );
    }

    /** @phpstan-ignore-next-line */
    public function patch(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): self
    {
        return $this->handleRequest(
            Request::create($uri, 'PATCH', $parameters, $cookies, $files, $server, $content)
        );
    }

    /** @phpstan-ignore-next-line */
    public function delete(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): self
    {
        return $this->handleRequest(
            Request::create($uri, 'DELETE', $parameters, $cookies, $files, $server, $content)
        );
    }

    /** @phpstan-ignore-next-line */
    public function ajaxDelete(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): self
    {
        return $this->ajax($uri, 'DELETE', $parameters, $cookies, $files, $server, $content);
    }

    /** @phpstan-ignore-next-line */
    public function ajaxPatch(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): self
    {
        return $this->ajax($uri, 'PATCH', $parameters, $cookies, $files, $server, $content);
    }

    /** @phpstan-ignore-next-line */
    public function ajaxPost(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): self
    {
        return $this->ajax($uri, 'POST', $parameters, $cookies, $files, $server, $content);
    }

    /** @phpstan-ignore-next-line */
    public function ajaxGet(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): self
    {
        return $this->ajax($uri, 'GET', $parameters, $cookies, $files, $server, $content);
    }

    /** @phpstan-ignore-next-line */
    public function ajax(string $uri, string $method = 'POST', $parameters = [], $cookies = [], $files = [], $server = [], $content = null): self
    {
        $request = Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);

        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        return $this->handleRequest($request);
    }

    public function handleRequest(Request $request): self
    {
        $this->request = $request;

        $this->request->setSession($this->container->get('session'));

        $this->response = $this->container->get('http_kernel')->handle($this->request);

        return $this;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    /** @return mixed */
    public function getJson(string $uri)
    {
        return (array) json_decode(
            $this->ajaxGet($uri)->response->getContent()
        );
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
