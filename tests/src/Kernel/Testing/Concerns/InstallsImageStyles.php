<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

trait InstallsImageStyles
{
    use InstallsExportedConfig;

    /** @var bool */
    private $setupImageStyleDependencies = false;

    /** @param string|array $imageStyles */
    public function installImageStyles($imageStyles): self
    {
        $this->prepareImageStyleDependencies();

        foreach ((array) $imageStyles as $imageStyle) {
            $this->installExportedConfig([
                'image.style.' . $imageStyle,
            ]);
        }

        return $this;
    }

    private function prepareImageStyleDependencies(): self
    {
        if ($this->setupImageStyleDependencies === false) {
            $this->enableModules([
                'image',
            ]);

            $this->installEntitySchema('image_style');
        }

        $this->setupImageStyleDependencies = true;

        return $this;
    }
}
