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

    protected function setUp(): void
    {
        parent::setUp();

        $this->installEntitySchema('user');
        $this->installSchema('system', 'sequences');
    }

    /** @test */
    public function run_batch_thats_been_set(): void
    {
        $this->createEnabledUser('enabled_user_one')
            ->createEnabledUser('enabled_user_two')
            ->createEnabledUser('enabled_user_three');

        $this->get($this->route('disable_all_users.prepare_batch'));

        $this->runLatestBatch();

        $userStorage = $this->container->get('entity_type.manager')->getStorage('user');

        $disabledUserOne = $userStorage->load(1);
        $this->assertEquals(0, $disabledUserOne->status->value);

        $disabledUserTwo = $userStorage->load(2);
        $this->assertEquals(0, $disabledUserTwo->status->value);

        $disabledUserThree = $userStorage->load(3);
        $this->assertEquals(0, $disabledUserThree->status->value);
    }

    /** @test */
    public function run_batch_thats_been_processed(): void
    {
        $this->createEnabledUser('enabled_user_one')
            ->createEnabledUser('enabled_user_two')
            ->createEnabledUser('enabled_user_three');

        $userStorage = $this->container->get('entity_type.manager')->getStorage('user');

        $disabledUserOne = $userStorage->load(1);
        $this->assertEquals(1, $disabledUserOne->status->value);

        $disabledUserTwo = $userStorage->load(2);
        $this->assertEquals(1, $disabledUserTwo->status->value);

        $disabledUserThree = $userStorage->load(3);
        $this->assertEquals(1, $disabledUserThree->status->value);

        $this->get($this->route('disable_all_users.prepare_and_process_batch'));

        $this->runLatestBatch();

        $userStorage = $this->container->get('entity_type.manager')->getStorage('user');

        $disabledUserOne = $userStorage->load(1);
        $this->assertEquals(0, $disabledUserOne->status->value);

        $disabledUserTwo = $userStorage->load(2);
        $this->assertEquals(0, $disabledUserTwo->status->value);

        $disabledUserThree = $userStorage->load(3);
        $this->assertEquals(0, $disabledUserThree->status->value);
    }

    private function createEnabledUser(string $name): self
    {
        $this->container->get('entity_type.manager')->getStorage('user')->create([
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
