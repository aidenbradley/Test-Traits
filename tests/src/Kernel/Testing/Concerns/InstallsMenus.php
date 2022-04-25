<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

trait InstallsMenus
{
    use InstallsExportedConfig;

    /** @var bool */
    private $setupMenuDependencies = false;

    /** @param string|array $menus */
    public function installMenus($menus): self
    {
        $this->setupMenuDependencies();

        foreach ((array) $menus as $menu) {
            $this->installExportedConfig('system.menu.' . $menu);
        }

        return $this;
    }

    private function setupMenuDependencies(): self
    {
        if ($this->setupMenuDependencies === false) {
            $this->enableModules([
                'system',
            ]);

            $this->installEntitySchema('menu');
        }

        $this->setupMenuDependencies = true;

        return $this;
    }
}
