<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

trait InstallsImageStyles
{
    use InstallsExportedConfig;

    /** @param string|array $imageStyles */
    public function installImageStyles($imageStyles): self
    {
        foreach ((array) $imageStyles as $imageStyle) {
            $this->installExportedConfig([
                'image.style.' . $imageStyle,
            ]);
        }

        return $this;
    }
}
