<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

use Drupal\Core\Config\FileStorage;
use Drupal\Core\Site\Settings;
use Drupal\Tests\test_traits\Kernel\Testing\Exceptions\ConfigInstallFailed;

/** This trait may be used to test fields stored as field configs */
trait InstallsExportedConfig
{
    /** @var array */
    protected $installedConfig = [];

    /** @var bool */
    protected $installFieldModule;

    public function installField(string $fieldName, string $entityType, ?string $bundle = null): self
    {
        if (isset($this->installFieldModule) === false) {
            $this->enableModules(['field']);

            $this->installFieldModule = true;
        }

        return $this->installExportedConfig([
            'field.storage.' . $entityType . '.' . $fieldName,
            'field.field.' . $entityType . '.' . ($bundle ? $bundle . '.' : $entityType . '.') . $fieldName,
        ]);
    }

    public function installFields(array $fieldNames, string $entityType, ?string $bundle = null): self
    {
        foreach ($fieldNames as $fieldName) {
            $this->installField($fieldName, $entityType, $bundle);
        }

        return $this;
    }

    public function installImageStyle(string $imageStyle): self
    {
        return $this->installExportedConfig([
            'image.style.' . $imageStyle,
        ]);
    }

    public function installImageStyles(array $imageStyles): self
    {
        foreach ($imageStyles as $imageStyle) {
            $this->installImageStyle($imageStyle);
        }

        return $this;
    }

    public function installBundle(string $module, string $bundle): self
    {
        return $this->installExportedConfig([
            $module . '.type.' . $bundle,
        ]);
    }

    public function installBundles(string $entityType, array $bundles): self
    {
        foreach ($bundles as $bundle) {
            $this->installBundle($entityType, $bundle);
        }

        return $this;
    }

    /** @param string|array $bundles */
    public function installEntitySchemaWithBundles(string $entityType, $bundles): self
    {
        $this->installEntitySchema($entityType);

        return $this->installBundles($entityType, (array)$bundles);
    }

    public function installRole(string $role): self
    {
        return $this->installExportedConfig('user.role.' . $role);
    }

    public function installRoles(array $roles): self
    {
        foreach ($roles as $role) {
            $this->installRole($role);
        }

        return $this;
    }

    public function installVocabulary(string $vocabularyName): self
    {
        return $this->installExportedConfig([
            'taxonomy.vocabulary.' . $vocabularyName,
        ]);
    }

    public function installVocabularies(array $vocabularies): self
    {
        foreach ($vocabularies as $vocabulary) {
            $this->installVocabulary($vocabulary);
        }

        return $this;
    }

    public function installAllFieldsForEntity(string $entityType, ?string $bundle = null): self
    {
        $configStorage = new FileStorage($this->configDirectory());

        return $this->installFields(array_map(function ($storageFieldName) {
            return substr($storageFieldName, strripos($storageFieldName, '.') + 1);
        }, $configStorage->listAll('field.storage.' . $entityType)), $entityType, $bundle);
    }

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
        return Settings::get('config_sync_directory');
    }
}
