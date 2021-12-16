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
    public function http_get_json(): void
    {
        $this->get($this->route('route.json.get'))->assertNotFound();

        $this->getJson($this->route('route.json.get'))->assertOK();
    }

    /** @test */
    public function http_post(): void
    {
        $this->post($this->route('route.post'))->assertOk();
    }

    /** @test */
    public function http_post_json(): void
    {
        $this->post($this->route('route.json.post'))->assertNotFound();

        $this->postJson($this->route('route.json.post'))->assertOK();
    }

    /** @test */
    public function http_put(): void
    {
        $this->put($this->route('route.put'))->assertOk();
    }

    /** @test */
    public function http_put_json(): void
    {
        $this->put($this->route('route.json.put'))->assertNotFound();

        $this->putJson($this->route('route.json.put'))->assertOK();
    }

    /** @test */
    public function http_patch(): void
    {
        $this->patch($this->route('route.patch'))->assertOk();
    }

    /** @test */
    public function http_patch_json(): void
    {
        $this->patch($this->route('route.json.patch'))->assertNotFound();

        $this->patchJson($this->route('route.json.patch'))->assertOK();
    }

    /** @test */
    public function http_delete(): void
    {
        $this->delete($this->route('route.delete'))->assertOk();
    }

    /** @test */
    public function http_delete_json(): void
    {
        $this->delete($this->route('route.json.delete'))->assertNotFound();

        $this->deleteJson($this->route('route.json.delete'))->assertOK();
    }

    /** @test */
    public function http_options(): void
    {
        $this->options($this->route('route.options'))->assertOk();
    }

    /** @test */
    public function http_options_json(): void
    {
        $this->options($this->route('route.json.options'))->assertNotFound();

        $this->optionsJson($this->route('route.json.options'))->assertOK();
    }

    /** @test */
    public function ajax_xml_http(): void
    {
        // controller throws NotFoundHttpException if the request isn't XML HTTP
        $this->get($this->route('route.xml_http_only'))->assertNotFound();

        $this->ajax()->get($this->route('route.xml_http_only'))->assertOk();
    }

    /** @test */
    public function assert_location(): void
    {
        $route = $this->route('route.get');

        $this->get($route)->assertLocation($route);
    }

    /** @test */
    public function assert_redirect(): void
    {
        $response = $this->get($this->route('route.redirect', [
            'redirectRoute' => 'route.redirect_to',
        ]));

        $response->assertLocation($this->route('route.redirect', [
            'redirectRoute' => 'route.redirect_to',
        ]))->assertRedirect($this->route('route.redirect_to'));
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
