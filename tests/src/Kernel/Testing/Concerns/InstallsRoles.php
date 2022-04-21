<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

trait InstallsRoles
{
    use InstallsExportedConfig;

    /** @param string|array $roles */
    public function installRoles($roles): self
    {
        foreach ((array) $roles as $role) {
            $this->installExportedConfig('user.role.' . $role);
        }

        return $this;
    }
}
