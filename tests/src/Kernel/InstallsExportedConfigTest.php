<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;
use Drupal\Tests\test_traits\Kernel\Concerns\InstallsExportedConfig;
use Drupal\Tests\test_traits\Kernel\Exceptions\ConfigInstallFailed;

class InstallsExportedConfigTest extends KernelTestBase
{
    use InstallsExportedConfig {
        configDirectory as InstallsExportedConfigDirectory;
    }

    protected static $modules = [
        'test_traits',
        'system',
        'node',
        'user',
    ];

    /** @var bool */
    private $useVfsConfigDirectory = false;

    /** @var string */
    private $customConfigDirectory;

    /** @test */
    public function throws_exception_for_bad_config(): void
    {
        $this->useVfsConfigDirectory = true;

        $this->installEntitySchema('node');

        $this->expectException(ConfigInstallFailed::class);
        $this->expectExceptionCode(ConfigInstallFailed::CONFIGURATION_DOES_NOT_EXIST);

        $this->installExportedConfig('node.type.page');
    }

    /** @test */
    public function installs_config(): void
    {
        $this->setConfigDirectory('node/bundles');

        $nodeTypeStorage = $this->container->get('entity_type.manager')->getStorage('node_type');

        $this->assertEmpty($nodeTypeStorage->loadMultiple());

        $this->installExportedConfig('node.type.page');

        $nodeTypes = $nodeTypeStorage->loadMultiple();

        $this->assertNotEmpty($nodeTypes);

        $pageNodeType = reset($nodeTypes);

        $this->assertEquals('page', $pageNodeType->id());
    }

    /** @test */
    public function install_bundle(): void
    {
        $this->setConfigDirectory('node/bundles');

        $nodeTypeStorage = $this->container->get('entity_type.manager')->getStorage('node_type');

        $this->assertEmpty($nodeTypeStorage->loadMultiple());

        $this->installBundle('node', 'page');

        $nodeTypes = $nodeTypeStorage->loadMultiple();

        $this->assertNotEmpty($nodeTypes);

        $pageNodeType = reset($nodeTypes);

        $this->assertEquals('page', $pageNodeType->id());
    }

    /** @test */
    public function install_bundles(): void
    {
        $this->setConfigDirectory('node/bundles');

        $nodeTypeStorage = $this->container->get('entity_type.manager')->getStorage('node_type');

        $this->assertEmpty($nodeTypeStorage->loadMultiple());

        $bundlesToInstall = [
            'page',
            'news',
        ];

        $this->installBundles('node', $bundlesToInstall);

        $nodeTypes = $nodeTypeStorage->loadMultiple();

        $this->assertNotEmpty($nodeTypes);

        $nodeTypeIds = array_map(function(NodeType $nodeType) {
            return $nodeType->id();
        }, $nodeTypes);

        $this->assertEquals($bundlesToInstall, array_values($nodeTypeIds));
    }

    /** @test */
    public function install_role(): void
    {
        $this->setConfigDirectory('roles');

        $userRoleStorage = $this->container->get('entity_type.manager')->getStorage('user_role');

        $this->assertEmpty($userRoleStorage->loadMultiple());

        $this->installRole('editor');

        $roles = $userRoleStorage->loadMultiple();

        $this->assertNotEmpty($roles);

        $editorRole = reset($roles);

        $this->assertEquals('editor', $editorRole->id());
    }

    /** @test */
    public function install_vocabulary(): void
    {
        $this->enableModules([
            'taxonomy',
        ]);
        $this->installEntitySchema('taxonomy_vocabulary');

        $this->setConfigDirectory('taxonomy');

        $vocabularyStorage = $this->container->get('entity_type.manager')->getStorage('taxonomy_vocabulary');

        $this->assertEmpty($vocabularyStorage->loadMultiple());

        $this->installVocabulary('tags');

        $vocabularies = $vocabularyStorage->loadMultiple();

        $this->assertNotEmpty($vocabularies);

        $tagsVocabulary = reset($vocabularies);

        $this->assertEquals('tags', $tagsVocabulary->id());
    }

    /** sets the config directory relative to the fixtures route */
    public function setConfigDirectory(string $directory): void
    {
        $this->customConfigDirectory = $directory;
    }

    public function configDirectory(): string
    {
        if ($this->useVfsConfigDirectory) {
            return $this->InstallsExportedConfigDirectory();
        }

        $baseConfigPath = __DIR__ . '/__fixtures__/config/sync/';

        if ($this->customConfigDirectory) {
            return $baseConfigPath . '/' . ltrim($this->customConfigDirectory, '/');
        }

        // providing our own directory with config we can test against
        return $baseConfigPath;
    }
}
