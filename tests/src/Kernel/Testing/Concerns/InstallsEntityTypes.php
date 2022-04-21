<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

trait InstallsEntityTypes
{
    use InstallsExportedConfig;

    /** @param string|array $bundles */
    public function installBundles(string $module, $bundles): self
    {
        foreach ((array) $bundles as $bundle) {
            $this->installExportedConfig([
                $module . '.type.' . $bundle,
            ]);
        }

        return $this;
    }

    /** @param string|array $bundles */
    public function installEntitySchemaWithBundles(string $entityType, $bundles): self
    {
        $this->installEntitySchema($entityType);

        return $this->installBundles($entityType, (array)$bundles);
    }
}
