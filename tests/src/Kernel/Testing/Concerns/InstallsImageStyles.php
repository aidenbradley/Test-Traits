<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

trait InstallsImageStyles
{
    use InstallsExportedConfig;

    public function installImageStyles(string $imageStyles): self
    {
        foreach ((array) $imageStyles as $imageStyle) {
            $this->installExportedConfig([
                'image.style.' . $imageStyle,
            ]);
        }

        return $this;
    }
}
