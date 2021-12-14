<?php

namespace Drupal\test_traits_test\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveRequest implements ContainerInjectionInterface
{
    /** @var Request */
    private $request;

    public static function create(ContainerInterface $container)
    {
        return new self(
            $container->get('request_stack')->getCurrentRequest(),
        );
    }

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function __invoke(): Response
    {
        return Response::create('content');
    }

    public function requestInfo(): JsonResponse
    {
        return JsonResponse::create([
            'is_ajax' => $this->request->isXmlHttpRequest(),
        ]);
    }

    public function statusCode(string $code): JsonResponse
    {
        return JsonResponse::create('Content', (int) $code);
    }
}
