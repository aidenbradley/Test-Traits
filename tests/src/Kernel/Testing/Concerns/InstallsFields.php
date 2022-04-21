<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

use Drupal\Core\Config\FileStorage;

trait InstallsFields
{
    use InstallsExportedConfig;

    /** @var bool */
    private $setupDependencies = false;

    public function installField(string $fieldName, string $entityType, ?string $bundle = null): self
    {
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

    public function installAllFieldsForEntity(string $entityType, ?string $bundle = null): self
    {
        $configStorage = new FileStorage($this->configDirectory());

        return $this->installFields(array_map(function ($storageFieldName) {
            return substr($storageFieldName, strripos($storageFieldName, '.') + 1);
        }, $configStorage->listAll('field.storage.' . $entityType)), $entityType, $bundle);
    }

    private function setupDependencies(): void
    {
        if ($this->setupDependencies) {
            return;
        }

        $this->enableModules(['field']);

        $this->setupDependencies = true;
    }
}
