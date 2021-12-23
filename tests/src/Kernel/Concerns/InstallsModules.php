<?php

namespace Drupal\Tests\test_traits\Kernel\Concerns;

use Drupal\Core\Serialization\Yaml;

/**
 * To be used in KernelTests
 * Use this to quickly install a module. Also useful as a test in itself to ensure your module has the correct
 * dependencies for installation
 */
trait InstallsModules
{
    /** @var array */
    protected $enabledModules;

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
