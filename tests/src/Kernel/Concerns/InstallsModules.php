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
            $modulesToEnable = [];

            $fileLocation = $this->getModulePath($module) . '/' . $module . '.info.yml';

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

    public function getModulePath(string $module): string
    {
        return \Drupal::service('app.root') . '/modules/custom/' . $module;
    }
}
