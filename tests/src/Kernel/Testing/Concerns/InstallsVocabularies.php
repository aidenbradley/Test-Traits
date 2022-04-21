<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

trait InstallsVocabularies
{
    use InstallsExportedConfig;

    /** @param string|array $vocabularies */
    public function installVocabularies($vocabularies): self
    {
        foreach ((array) $vocabularies as $vocabulary) {
            $this->installExportedConfig([
                'taxonomy.vocabulary.' . $vocabulary,
            ]);
        }

        return $this;
    }
}
