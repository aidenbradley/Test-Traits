<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\system\Entity\Menu;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\InstallsMenus;

class InstallsMenusTest extends KernelTestBase
{
    use InstallsMenus;

    protected function setUp()
    {
        parent::setUp();

        $this->setConfigDirectory(__DIR__ . '/__fixtures__/config/sync/menus');
    }

    /** @test */
    public function installing_menu_sets_dependencies(): void
    {
        $this->assertFalse($this->container->get('module_handler')->moduleExists('system'));

        $entityTypeDefinitions = $this->container->get('entity_type.manager')->getDefinitions();
        $this->assertArrayNotHasKey('menu', $entityTypeDefinitions);

        $this->installMenus('footer');

        $this->assertTrue($this->container->get('module_handler')->moduleExists('system'));

        $entityTypeDefinitions = $this->container->get('entity_type.manager')->getDefinitions();
        $this->assertArrayHasKey('menu', $entityTypeDefinitions);
    }

    /** @test */
    public function install_single_menu(): void
    {
        $this->enableModules([
            'system',
        ]);
        $this->installEntitySchema('menu');

        $menuStorage = $this->container->get('entity_type.manager')->getStorage('menu');

        $this->assertEmpty($menuStorage->loadMultiple());

        $this->installMenus('footer');

        $menus = $menuStorage->loadMultiple();

        $this->assertNotEmpty($menus);

        $this->assertInstanceOf(Menu::class, $menuStorage->load('footer'));
    }

    /** @test */
    public function install_multiple_menus(): void
    {
        $this->enableModules([
            'system',
        ]);
        $this->installEntitySchema('menu');

        $menuStorage = $this->container->get('entity_type.manager')->getStorage('menu');

        $this->assertEmpty($menuStorage->loadMultiple());

        $this->installMenus([
            'footer',
            'main',
        ]);

        $this->assertInstanceOf(Menu::class, $menuStorage->load('footer'));
        $this->assertInstanceOf(Menu::class, $menuStorage->load('main'));
    }
}
