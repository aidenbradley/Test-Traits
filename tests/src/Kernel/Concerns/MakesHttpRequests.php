<?php

namespace Drupal\Tests\test_traits\Kernel\Concerns;

use Drupal\Tests\test_traits\Kernel\TestResponse;
use Symfony\Component\HttpFoundation\Request;

trait MakesHttpRequests
{
    /** @var Request */
    private $request;

    /** @var \Symfony\Contracts\HttpClient\ResponseInterface */
    private $response;

    /** @var null|bool */
    private $requestIsAjax = null;

    public static function responseClass(): string
    {
        return TestResponse::class;
    }

    public function get(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): TestResponse
    {
        return $this->handleRequest(
            Request::create($uri, 'GET', $parameters, $cookies, $files, $server, $content)
        );
    }

    public function post(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): TestResponse
    {
        return $this->handleRequest(
            Request::create($uri, 'POST', $parameters, $cookies, $files, $server, $content)
        );
    }

    public function put(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): TestResponse
    {
        return $this->handleRequest(
            Request::create($uri, 'PUT', $parameters, $cookies, $files, $server, $content)
        );
    }

    public function delete(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): TestResponse
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

    /** @return mixed */
    public function handleRequest(Request $request)
    {
        $this->container->get('kernel')->invalidateContainer();
        $this->container->get('kernel')->rebuildContainer();

        $this->request = $request;

        $this->request->setSession($this->container->get('session'));

        if ($this->requestIsAjax !== null) {
            $this->request->headers->set('X-Requested-With', 'XMLHttpRequest');

            $this->requestIsAjax = null;
        }

        return static::responseClass()::fromBaseResponse(
            $this->container->get('http_kernel')->handle($this->request)
        );
    }

    public function followRedirect(): TestResponse
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
