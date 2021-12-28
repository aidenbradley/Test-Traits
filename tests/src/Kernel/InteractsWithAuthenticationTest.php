<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\Core\Url;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Concerns\InteractsWithAuthentication;
use Drupal\Tests\test_traits\Kernel\Concerns\MakesHttpRequests;
use Drupal\Tests\user\Traits\UserCreationTrait;

class InteractsWithAuthenticationTest extends KernelTestBase
{
    use InteractsWithAuthentication,
        MakesHttpRequests;

    protected static $modules = [
        'system',
        'user',
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->installEntitySchema('user');
    }

    /** @test */
    public function acting_as(): void
    {
        $userStorage = $this->container->get('entity_type.manager')->getStorage('user');

        $userStorage->create([
            'uid' => 1,
            'name' => 'authenticated_user',
            'status' => 1,
        ])->save();

        $user = $userStorage->load(1);

        $this->actingAs($user)->get(
            $this->route('entity.user.canonical', ['user' => $user->id()])
        )->assertOk();

        $this->assertEquals($user->id(), $this->container->get('current_user')->getAccount()->id());

        $this->actingAsAnonymous()->get(
            $this->route('entity.user.canonical', ['user' => 0])
        )->assertForbidden();
    }

    private function route(string $route, array $parameters = [], array $options = []): string
    {
        return Url::fromRoute(...func_get_args())->toString(true)->getGeneratedUrl();
    }
}
