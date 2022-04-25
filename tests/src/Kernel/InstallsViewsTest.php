<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\InstallsViews;
use Drupal\views\Entity\View;

class InstallsViewsTest extends KernelTestBase
{
    use InstallsViews;

    protected $strictConfigSchema = false;

    protected function setUp()
    {
        parent::setUp();

        $this->setConfigDirectory(__DIR__ . '/__fixtures__/config/sync/views');
    }

    /** @test */
    public function installing_view_sets_up_dependencies(): void
    {
        $this->assertFalse($this->container->get('module_handler')->moduleExists('views'));

        $entityTypeDefinitions = $this->container->get('entity_type.manager')->getDefinitions();
        $this->assertArrayNotHasKey('view', $entityTypeDefinitions);

        $this->installViews('media');

        $entityTypeDefinitions = $this->container->get('entity_type.manager')->getDefinitions();
        $this->assertArrayHasKey('view', $entityTypeDefinitions);

        $this->assertTrue($this->container->get('module_handler')->moduleExists('views'));
    }

    /** @test */
    public function install_single_view(): void
    {
        $this->enableModules([
            'system',
            'user',
            'views',
        ]);
        $this->installEntitySchema('view');

        $viewStorage = $this->container->get('entity_type.manager')->getStorage('view');

        $this->assertEmpty($viewStorage->loadMultiple());

        $this->installViews('media');

        $views = $viewStorage->loadMultiple();

        $this->assertNotEmpty($views);

        $this->assertInstanceOf(View::class, $viewStorage->load('media'));
    }

    /** @test */
    public function install_multiple_views(): void
    {
        $this->enableModules([
            'system',
            'user',
            'views',
        ]);
        $this->installEntitySchema('view');

        $viewStorage = $this->container->get('entity_type.manager')->getStorage('view');

        $this->assertEmpty($viewStorage->loadMultiple());

        $this->installViews([
            'media',
            'redirect',
        ]);

        $this->assertInstanceOf(View::class, $viewStorage->load('media'));
        $this->assertInstanceOf(View::class, $viewStorage->load('redirect'));
    }
}
