<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

use Drupal\Core\Config\FileStorage;
use Drupal\Core\Site\Settings;
use Drupal\Tests\test_traits\Kernel\Testing\Exceptions\ConfigInstallFailed;

trait InstallsExportedConfig
{
    use InstallsFields,
        InstallsImageStyles,
        InstallsRoles,
        InstallsVocabularies,
        InstallsEntityTypes,
        InstallsViews,
        InstallsBlocks,
        InstallsMenus;

    /** @var string */
    private $useVfsConfigDirectory = false;

    /** @var string */
    private $customConfigDirectory;

    /** @var array */
    private $installedConfig = [];

    /** @param string|array $config */
    public function installExportedConfig($config): self
    {
        $configStorage = new FileStorage($this->configDirectory());

        foreach ((array)$config as $configName) {
            if (in_array($configName, $this->installedConfig)) {
                continue;
            }

            $this->installedConfig[] = $configName;

            $configRecord = $configStorage->read($configName);

            $entityType = $this->container->get('config.manager')->getEntityTypeIdByName($configName);

            /** @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $storage */
            $storage = $this->container->get('entity_type.manager')->getStorage($entityType);

            if (is_array($configRecord) === false) {
                throw ConfigInstallFailed::doesNotExist($configName);
            }

            $storage->createFromStorageRecord($configRecord)->save();
        }

        return $this;
    }

    protected function configDirectory(): string
    {
        if ($this->useVfsConfigDirectory) {
            return Settings::get('config_sync_directory');
        }

        if ($this->customConfigDirectory) {
            return '/' . ltrim($this->customConfigDirectory, '/');
        }

        $root = $this->container->get('app.root');

        return str_replace('web/', '', $root . '/config/sync');
    }

    /** sets the config directory relative to the __fixtures__ directory */
    protected function setConfigDirectory(string $directory): self
    {
        $this->customConfigDirectory = $directory;

        return $this;
    }

    protected function useVfsConfigDirectory(): self
    {
        $this->useVfsConfigDirectory = true;

        return $this;
    }
}
