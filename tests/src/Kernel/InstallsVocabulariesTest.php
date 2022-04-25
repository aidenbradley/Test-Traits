<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\InstallsVocabularies;

class InstallsVocabulariesTest extends KernelTestBase
{
    use InstallsVocabularies;

    protected function setUp()
    {
        parent::setUp();

        $this->setConfigDirectory(__DIR__ . '/__fixtures__/config/sync/taxonomy');
    }

    /** @test */
    public function installing_vocabulary_sets_up_dependencies(): void
    {
        $moduleHandler = $this->container->get('module_handler');

        $this->assertFalse($moduleHandler->moduleExists('taxonomy'));

        $entityTypeDefinitions = $this->container->get('entity_type.manager')->getDefinitions();
        $this->assertArrayNotHasKey('taxonomy_vocabulary', $entityTypeDefinitions);

        $this->installVocabularies('tags');

        $entityTypeDefinitions = $this->container->get('entity_type.manager')->getDefinitions();
        $this->assertArrayHasKey('taxonomy_vocabulary', $entityTypeDefinitions);

        $this->assertTrue($moduleHandler->moduleExists('taxonomy'));
        $this->assertInstanceOf(Vocabulary::class, $this->container->get('entity_type.manager')->getStorage('taxonomy_vocabulary')->load('tags'));
    }

    /** @test */
    public function install_single_vocabulary(): void
    {
        $this->enableModules([
            'taxonomy',
        ]);

        $this->installEntitySchema('taxonomy_vocabulary');

        $vocabularyStorage = $this->container->get('entity_type.manager')->getStorage('taxonomy_vocabulary');

        $this->assertEmpty($vocabularyStorage->loadMultiple());

        $this->installVocabularies('tags');

        $vocabularyStorage = $this->container->get('entity_type.manager')->getStorage('taxonomy_vocabulary');

        $this->assertNotEmpty($vocabularyStorage->loadMultiple());

        $this->assertInstanceOf(Vocabulary::class, $vocabularyStorage->load('tags'));
    }

    /** @test */
    public function install_multiple_vocabularies(): void
    {
        $this->enableModules([
            'taxonomy',
        ]);

        $this->installEntitySchema('taxonomy_vocabulary');

        $vocabularyStorage = $this->container->get('entity_type.manager')->getStorage('taxonomy_vocabulary');

        $this->assertEmpty($vocabularyStorage->loadMultiple());

        $this->installVocabularies([
            'tags',
            'category',
        ]);

        $vocabularyStorage = $this->container->get('entity_type.manager')->getStorage('taxonomy_vocabulary');

        $this->assertNotEmpty($vocabularyStorage->loadMultiple());

        $this->assertInstanceOf(Vocabulary::class, $vocabularyStorage->load('tags'));
        $this->assertInstanceOf(Vocabulary::class, $vocabularyStorage->load('category'));
    }
}
