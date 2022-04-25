<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

trait InstallsBlocks
{
    use InstallsExportedConfig;

    /** @var bool */
    private $setupBlockDependencies = false;

    /** @param string|array $blocks */
    public function installBlocks($blocks): void
    {
        $this->setupBlockDependencies();

        foreach ((array) $blocks as $block) {
            $this->installExportedConfig('block.block.' . $block);
        }
    }

    private function setupBlockDependencies(): self
    {
        if ($this->setupBlockDependencies === false) {
            $this->enableModules([
                'system',
                'block',
            ]);

            $this->installEntitySchema('block');
        }

        $this->setupBlockDependencies = true;

        return $this;
    }
}
