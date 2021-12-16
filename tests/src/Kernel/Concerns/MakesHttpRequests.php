<?php

namespace Drupal\Tests\test_traits\Kernel\Concerns;

use Drupal\Tests\test_traits\Kernel\TestResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait MakesHttpRequests
{
    /** @var Request */
    private $request;

    /** @var \Symfony\Contracts\HttpClient\ResponseInterface */
    private $response;

    /** @var bool */
    private $followRedirects;

    /** @var null|bool */
    private $requestIsAjax = null;

    public static function responseClass(): string
    {
        return TestResponse::class;
    }

    public function get(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): TestResponse
    {
        return $this->call('GET', ...func_get_args());
    }

    public function post(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): TestResponse
    {
        return $this->call('POST', ...func_get_args());
    }

    public function put(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): TestResponse
    {
        return $this->call('PUT', ...func_get_args());
    }

    public function patch(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): TestResponse
    {
        return $this->call('PATCH', ...func_get_args());
    }

    public function options(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): TestResponse
    {
        return $this->call('OPTIONS', ...func_get_args());
    }

    public function delete(string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): TestResponse
    {
        return $this->call('DELETE', ...func_get_args());
    }

    public function ajax(): self
    {
        $this->requestIsAjax = true;

        return $this;
    }

    /** @return mixed */
    public function call(string $method, string $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): TestResponse
    {
        $request = Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);

        $request->setSession($this->container->get('session'));

        if ($this->requestIsAjax) {
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');

            $this->requestIsAjax = null;
        }

        $httpKernel = $this->container->get('http_kernel');

        $response = $httpKernel->handle($request);

        $httpKernel->terminate($request, $response);

        if ($this->followRedirects) {
            $response = $this->followRedirects($response);
        }

        $kernel = $this->container->get('kernel');

        $kernel->invalidateContainer();
        $kernel->rebuildContainer();

        return static::responseClass()::fromBaseResponse($response);
    }

    public function followingRedirects()
    {
        $this->followRedirects = true;

        return $this;
    }

    protected function followRedirects(Response $response)
    {
        $this->followRedirects = false;

        while ($response->isRedirect()) {
            $response = $this->get($response->headers->get('Location'));
        }

        return $response;
    }
}
