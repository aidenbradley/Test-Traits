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
    public function http_get(): void
    {
        $this->get($this->route('route.get'))->assertOk();
    }

    /** @test */
    public function http_post(): void
    {
        $this->post($this->route('route.post'))->assertOk();
    }

    /** @test */
    public function http_put(): void
    {
        $this->put($this->route('route.put'))->assertOk();
    }

    /** @test */
    public function http_delete(): void
    {
        $this->delete($this->route('route.delete'))->assertOk();
    }

    /** @test */
    public function ajax_xml_http(): void
    {
        $this->markTestSkipped('Need to figure out how to test this now we have TestResponse');
        $this->ajax()->get($this->route('route.get'));

        $this->assertTrue($this->request->isXmlHttpRequest());
    }

    /** @test */
    public function assert_location(): void
    {
        $route = $this->route('route.get');

        $this->get($route)->assertLocation($route);
    }

    /** @test */
    public function following_redirects(): void
    {
        $this->followingRedirects()->get($this->route('route.redirect', [
            'redirectRoute' => 'route.redirect_to',
        ]))->assertLocation($this->route('route.redirect_to'));
    }

    private function route(string $routeName, array $parameters = [], array $options = []): string
    {
        return Url::fromRoute(...func_get_args())->toString(true)->getGeneratedUrl();
    }
}
