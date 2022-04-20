<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\InteractsWithLanguages;

class InteractsWithLanguagesTest extends KernelTestBase
{
    use InteractsWithLanguages;

    protected static $modules = [
        'system',
    ];

    /** @var string */
    private $customConfigDirectory;

    protected function setUp()
    {
        parent::setUp();

        $this->setConfigDirectory('languages');
    }

    /** @test */
    public function install_languages(): void
    {
        $this->assertArrayNotHasKey('de', $this->languageManager()->getLanguages());
        $this->installLanguage('de');
        $this->assertArrayHasKey('de', $this->languageManager()->getLanguages());

        $this->assertArrayNotHasKey('fr', $this->languageManager()->getLanguages());
        $this->installLanguage('fr');
        $this->assertArrayHasKey('fr', $this->languageManager()->getLanguages());
    }

    /** @test */
    public function set_current_language(): void
    {
        $this->setCurrentLanguage('en');
        $this->assertEquals('en', $this->languageManager()->getCurrentLanguage()->getId());

        $this->setCurrentLanguage('de');
        $this->assertEquals('de', $this->languageManager()->getCurrentLanguage()->getId());

        $this->setCurrentLanguage('fr');
        $this->assertEquals('fr', $this->languageManager()->getCurrentLanguage()->getId());
    }

    /** @test */
    public function set_current_language_with_prefix(): void
    {
        $this->enableModules([
            'node',
            'user',
        ]);
        $this->setConfigDirectory('node/bundles');

        $this->installEntitySchemaWithBundles('node', 'page');
        $this->installEntitySchema('user');

        $this->setConfigDirectory('languages');

        $noPrefixEnNode = $this->nodeStorage()->create([
            'nid' => '1000',
            'title' => 'EN Node',
            'type' => 'page',
            'langcode' => 'en',
        ]);
        $noPrefixEnNode->save();
        $this->assertEquals('/node/1000', $noPrefixEnNode->toUrl()->toString(true)->getGeneratedUrl());

        $this->setCurrentLanguage('fr', 'fr-fr');
        $frFrNode = $this->nodeStorage()->create([
            'nid' => '2000',
            'title' => 'FR Node',
            'type' => 'page',
            'langcode' => 'fr',
        ]);
        $frFrNode->save();
        $this->assertEquals('/fr-fr/node/2000', $frFrNode->toUrl()->toString(true)->getGeneratedUrl());

        $this->setCurrentLanguage('fr', 'fr-prefix');
        $frPrefixNode = $this->nodeStorage()->create([
            'nid' => '3000',
            'title' => 'FR Node',
            'type' => 'page',
            'langcode' => 'fr',
        ]);
        $frPrefixNode->save();
        $this->assertEquals('/fr-prefix/node/3000', $frPrefixNode->toUrl()->toString(true)->getGeneratedUrl());
    }

    /** @test */
    public function set_current_language_when_creating_entity(): void
    {
        $this->enableModules([
            'node',
            'user',
        ]);
        $this->setConfigDirectory('node/bundles');

        $this->installEntitySchemaWithBundles('node', 'page');
        $this->installEntitySchema('user');

        $this->setConfigDirectory('languages');

        $enNode = $this->nodeStorage()->create([
            'title' => 'EN Node',
            'type' => 'page',
        ]);
        $enNode->save();
        $this->assertEquals('en', $enNode->language()->getId());

        $this->setCurrentLanguage('fr');
        $frNode = $this->nodeStorage()->create([
            'title' => 'FR Node',
            'type' => 'page',
        ]);
        $frNode->save();
        $this->assertEquals('fr', $frNode->language()->getId());

        $this->setCurrentLanguage('de');
        $deNode = $this->nodeStorage()->create([
            'title' => 'DE Node',
            'type' => 'page',
        ]);
        $deNode->save();
        $this->assertEquals('de', $deNode->language()->getId());
    }

    protected function configDirectory(): string
    {
        $baseConfigPath = __DIR__ . '/__fixtures__/config/sync';

        if ($this->customConfigDirectory) {
            return $baseConfigPath . '/' . ltrim($this->customConfigDirectory, '/');
        }

        // providing our own directory with config we can test against
        return $baseConfigPath;
    }

    /** sets the config directory relative to the __fixtures__ directory */
    private function setConfigDirectory(string $directory): void
    {
        $this->customConfigDirectory = $directory;
    }

    private function nodeStorage(): EntityStorageInterface
    {
        return $this->container->get('entity_type.manager')->getStorage('node');
    }
}
