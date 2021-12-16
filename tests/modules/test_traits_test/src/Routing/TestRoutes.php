<?php

namespace Drupal\test_traits_test\Routing;

use Drupal\test_traits_test\Controller\ResolveRequest;
use Symfony\Component\Routing\Route;

class TestRoutes
{
    public function routes(): array
    {
        return [
            'route.get' => $this->createRoute('route.get', 'GET'),
            'route.json.get' => $this->createJsonRoute('route.json.get', 'GET'),
            'route.post' => $this->createRoute('route.post', 'POST'),
            'route.json.post' => $this->createJsonRoute('route.json.post', 'POST'),
            'route.put' => $this->createRoute('route.put', 'PUT'),
            'route.json.put' => $this->createJsonRoute('route.json.put', 'PUT'),
            'route.patch' => $this->createRoute('route.patch', 'PATCH'),
            'route.json.patch' => $this->createJsonRoute('route.json.patch', 'PATCH'),
            'route.options' => $this->createRoute('route.options', 'OPTIONS'),
            'route.delete' => $this->createRoute('route.delete', 'DELETE'),
            'route.json.delete' => $this->createJsonRoute('route.json.delete', 'DELETE'),
            'route.xml_http_only' => $this->createRoute('route.xml_http_only', 'GET', 'xmlHttpOnly')
        ];
    }

    /** @param string|array $methods */
    private function createJsonRoute(string $routeName, $methods): Route
    {
        return $this->createRoute($routeName, $methods, 'json');
    }

    /** @param string|array $methods */
    private function createRoute(string $routeName, $methods, ?string $controllerMethod = null): Route
    {
        $controllerCallable = $controllerMethod ? ResolveRequest::class . '::' . $controllerMethod : ResolveRequest::class;

        return (new Route(str_replace('.', '-', $routeName)))
            ->setMethods((array) $methods)
            ->setDefault('_controller', $controllerCallable)
            ->setOption('no_cache', 'TRUE')
            ->setRequirement('_access', 'TRUE');
    }
}
