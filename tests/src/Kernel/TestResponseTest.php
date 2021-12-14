<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Symfony\Component\HttpFoundation\Response;

class TestResponseTest extends KernelTestBase
{
    protected static $modules = [
        'test_traits_test',
    ];

    /** @test */
    public function assert_ok(): void
    {
        $this->createMockResponse(200)->assertOk();
    }

    /** @test */
    public function assert_not_found(): void
    {
        $this->createMockResponse(404)->assertNotFound();
    }

    /** @param mixed $content */
    private function createMockResponse(int $statusCode, $content = ''): TestResponse
    {
        return TestResponse::fromBaseResponse(
            Response::create($content, $statusCode)
        );
    }
}
