<?php

namespace Drupal\Tests\test_traits\Kernel\Concerns;

use Drupal\Core\Serialization\Yaml;

trait InstallsModules
{
    public function installModuleWithDependencies($modules): void
    {
        foreach ((array) $modules as $module) {
            $fileLocation = drupal_get_path('module', $module) . '/' . $module . '.info.yml';

            $infoYaml = Yaml::decode(file_get_contents($fileLocation));

            if (isset($infoYaml['dependencies']) === false) {
                $this->enableModules((array) $module);

                return;
            }

            $cleanedDependencies = array_map(function ($dependency) {
                return str_replace('drupal:', '', $dependency);
            }, $infoYaml['dependencies']);

            $this->enableModules(array_merge((array) $module, $cleanedDependencies));
        }
    }

    public function installModulesWithDependencies(array $modules): void
    {
        foreach($modules as $module) {
            $this->installModuleWithDependencies($module);
        }
    }
}
