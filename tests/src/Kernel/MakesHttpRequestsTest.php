<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\Core\Url;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Concerns\MakesHttpRequests;

class MakesHttpRequestsTest extends KernelTestBase
{
    use MakesHttpRequests;

    protected static $modules = [
        'test_traits_test',
    ];

    /** @test */
    public function get_http_verb(): void
    {
        $this->get($this->route('route.get'))->assertOk();
    }

    /** @test */
    public function post_http_verb(): void
    {
        $this->post($this->route('route.post'))->assertOk();
    }

    /** @test */
    public function put_http_verb(): void
    {
        $this->put($this->route('route.put'))->assertOk();
    }

    /** @test */
    public function delete_http_verb(): void
    {
        $this->delete($this->route('route.delete'))->assertOk();
    }

    /** @test */
    public function ajax_xml_http(): void
    {
        $this->ajax()->get($this->route('route.request_info'));

        $this->assertTrue($this->request->isXmlHttpRequest());
    }

    private function route(string $routeName, array $parameters = [], array $options = []): string
    {
        return Url::fromRoute(...func_get_args())->toString(true)->getGeneratedUrl();
    }
}
