<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Concerns\InstallsTheme;

class InstallsThemeTest extends KernelTestBase
{
    use InstallsTheme;

    protected static $modules = [
        'test_traits',
    ];

    protected $strictConfigSchema = false;

    /** @test */
    public function installs_theme(): void
    {
        $this->assertEmpty($this->container->get('theme_handler')->listInfo());

        $this->installTheme('seven');

        $this->assertArrayHasKey('seven', $this->container->get('theme_handler')->listInfo());

        $this->installTheme('bartik');

        $this->assertArrayHasKey('bartik', $this->container->get('theme_handler')->listInfo());
    }
}
