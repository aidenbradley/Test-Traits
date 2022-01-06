<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

trait InstallsTheme
{
    public function installTheme(string $theme): self
    {
        $this->container
            ->get('theme_installer')
            ->install((array) $theme);

        $this->container
            ->get('config.factory')
            ->getEditable('system.theme')
            ->set('default', $theme)
            ->save();

        $this->container->set('theme.registry', null);

        return $this;
    }
}
