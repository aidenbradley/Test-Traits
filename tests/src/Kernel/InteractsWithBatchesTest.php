<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\Core\Url;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\InteractsWithBatches;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\MakesHttpRequests;

class InteractsWithBatchesTest extends KernelTestBase
{
    use MakesHttpRequests,
        InteractsWithBatches;

    protected static $modules = [
        'system',
        'user',
        'test_traits_batch',
    ];

    /** @var \Drupal\user\UserStorage */
    private $userStorage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->installEntitySchema('user');
        $this->installSchema('system', 'sequences');

        $this->userStorage = $this->container->get('entity_type.manager')->getStorage('user');
    }

    /** @test */
    public function set_batch(): void
    {
        $this->createDisabledUser('disabled_user_one')
            ->createDisabledUser('disabled_user_two')
            ->createDisabledUser('disabled_user_three');

        $this->get($this->route('disable_all_users.prepare_batch'));

        $this->runBatch();

        $disabledUserOne = $this->userStorage->load(1);
        $this->assertEquals(0, $disabledUserOne->status->value);

        $disabledUserTwo = $this->userStorage->load(2);
        $this->assertEquals(0, $disabledUserTwo->status->value);

        $disabledUserThree = $this->userStorage->load(3);
        $this->assertEquals(0, $disabledUserThree->status->value);
    }

    /** @test */
    public function process_batch(): void
    {
        $this->createDisabledUser('disabled_user_one')
            ->createDisabledUser('disabled_user_two')
            ->createDisabledUser('disabled_user_three');

        $this->get($this->route('disable_all_users.prepare_and_process_batch'));

        $this->runBatch();

        $userStorage = $this->container->get('entity_type.manager')->getStorage('user');

        $disabledUserOne = $userStorage->load(1);
        $this->assertEquals(0, $disabledUserOne->status->value);

        $disabledUserTwo = $userStorage->load(2);
        $this->assertEquals(0, $disabledUserTwo->status->value);

        $disabledUserThree = $userStorage->load(3);
        $this->assertEquals(0, $disabledUserThree->status->value);
    }

    private function createDisabledUser(string $name): self
    {
        $this->userStorage->create([
            'status' => 1,
            'mail' => $name . '@example.com',
            'name' => $name,
        ])->save();

        return $this;
    }

    private function route(string $route, array $parameters = [], array $options = []): string
    {
        return Url::fromRoute(...func_get_args())->toString(true)->getGeneratedUrl();
    }
}
