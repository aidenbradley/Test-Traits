<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

trait InstallsViews
{
    use InstallsExportedConfig;

    /** @var bool */
    private $setupViewsDependencies = false;

    /** @param string|array */
    public function installViews($views)
    {
        $this->setupViewsDependencies();

        foreach ((array) $views as $view) {
            $this->installExportedConfig('views.view.' . $view);
        }
    }

    private function setupViewsDependencies(): self
    {
        if ($this->setupViewsDependencies === false) {
            $this->enableModules([
                'system',
                'user',
                'views',
            ]);
            $this->installEntitySchema('view');
        }

        $this->setupViewsDependencies = true;

        return $this;
    }
}
