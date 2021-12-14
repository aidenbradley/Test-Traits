<?php

namespace Drupal\Tests\test_traits\Kernel\Concerns;

/**
 * To be used in KernelTests
 * Use this when asserting a responses content that includes a template defined in a theme
 */
trait InstallsTheme
{
    public function installTheme(string $theme): void
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
    }
}
