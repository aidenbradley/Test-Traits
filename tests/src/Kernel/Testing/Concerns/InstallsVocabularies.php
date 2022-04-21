<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

trait InstallsVocabularies
{
    use InstallsExportedConfig;

    public function installVocabularies(array $vocabularies): self
    {
        foreach ($vocabularies as $vocabulary) {
            $this->installExportedConfig([
                'taxonomy.vocabulary.' . $vocabulary,
            ]);
        }

        return $this;
    }
}
