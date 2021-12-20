<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\image\Entity\ImageStyle;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Tests\test_traits\Kernel\Concerns\InstallsExportedConfig;
use Drupal\Tests\test_traits\Kernel\Exceptions\ConfigInstallFailed;
use Drupal\user\Entity\Role;

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
    public function install_roles(): void
    {
        $this->setConfigDirectory('roles');

        $userRoleStorage = $this->container->get('entity_type.manager')->getStorage('user_role');

        $this->assertEmpty($userRoleStorage->loadMultiple());

        $rolesToInstall = [
            'writer',
            'editor',
        ];
        $this->installRoles($rolesToInstall);

        $roles = $userRoleStorage->loadMultiple();

        $this->assertNotEmpty($roles);

        $roleIds = array_map(function(Role $role) {
            return $role->id();
        }, $userRoleStorage->loadMultiple());

        $this->assertEquals($rolesToInstall, array_values($roleIds));
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

    /** @test */
    public function install_vocabularies(): void
    {
        $this->enableModules([
            'taxonomy',
        ]);
        $this->installEntitySchema('taxonomy_vocabulary');

        $this->setConfigDirectory('taxonomy');

        $vocabularyStorage = $this->container->get('entity_type.manager')->getStorage('taxonomy_vocabulary');

        $this->assertEmpty($vocabularyStorage->loadMultiple());

        $vocabulariesToInstall = [
            'category',
            'tags',
        ];
        $this->installVocabularies($vocabulariesToInstall);

        $vocabularies = $vocabularyStorage->loadMultiple();

        $this->assertNotEmpty($vocabularies);

        $vocabularyIds = array_map(function(Vocabulary $vocabulary) {
            return $vocabulary->id();
        }, $vocabularyStorage->loadMultiple());

        $this->assertEquals($vocabulariesToInstall, array_values($vocabularyIds));
    }

    /** @test */
    public function install_field(): void
    {
        $this->installEntitySchema('user');
        $this->installEntitySchema('node');

        $this->enableModules([
            'text',
        ]);

        $this->setConfigDirectory('node/bundles');
        $this->installBundle('node', 'page');

        $this->setConfigDirectory('node/fields');

        $nodeStorage = $this->container->get('entity_type.manager')->getStorage('node');

        $node = $nodeStorage->create([
            'nid' => 1,
            'type' => 'page',
            'title' => 'Node',
        ]);
        $node->save();

        $this->assertFalse($node->hasField('body'));

        $this->installField('body', 'node', 'page');

        $node = $nodeStorage->load(1);

        $this->assertTrue($node->hasField('body'));
    }

    /** @test */
    public function install_fields(): void
    {
        $this->installEntitySchema('user');
        $this->installEntitySchema('node');

        $this->enableModules([
            'text',
        ]);

        $this->setConfigDirectory('node/bundles');
        $this->installBundle('node', 'page');

        $this->setConfigDirectory('node/fields');

        $nodeStorage = $this->container->get('entity_type.manager')->getStorage('node');

        $node = $nodeStorage->create([
            'nid' => 1,
            'type' => 'page',
            'title' => 'Node',
        ]);
        $node->save();

        $this->assertFalse($node->hasField('body'));
        $this->assertFalse($node->hasField('field_boolean'));

        $this->installFields([
            'body',
            'field_boolean_field',
        ], 'node', 'page');

        $node = $nodeStorage->load(1);

        $this->assertTrue($node->hasField('body'));
        $this->assertTrue($node->hasField('field_boolean_field'));
    }

    /** @test */
    public function install_entity_schema_with_bundles(): void
    {
        $this->setConfigDirectory('node/bundles');

        $entityTypeManager = $this->container->get('entity_type.manager');

        $nodeEntityTypeDefinition = $entityTypeManager->getDefinition('node');

        $this->assertFalse($this->container->get('database')->schema()->tableExists(
            $nodeEntityTypeDefinition->getDataTable()
        ));

        $this->assertEmpty($entityTypeManager->getStorage('node_type')->loadMultiple());

        $bundlesToInstall = [
            'page',
            'news',
        ];

        $this->installEntitySchemaWithBundles('node', $bundlesToInstall);

        $this->assertTrue($this->container->get('database')->schema()->tableExists(
            $nodeEntityTypeDefinition->getDataTable()
        ));

        $nodeTypeIds = array_map(function(NodeType $nodeType) {
            return $nodeType->id();
        }, $entityTypeManager->getStorage('node_type')->loadMultiple());

        $this->assertEquals($bundlesToInstall, array_values($nodeTypeIds));
    }

    /** @test */
    public function install_image_style(): void
    {
        $this->enableModules([
            'image',
        ]);
        $this->setConfigDirectory('image_styles');

        $imageStyleStorage = $this->container->get('entity_type.manager')->getStorage('image_style');

        $this->assertEmpty($imageStyleStorage->loadMultiple());

        $this->installImageStyle('large');

        $imageStyles = $imageStyleStorage->loadMultiple();

        $this->assertNotEmpty($imageStyles);

        $largeImageStyle = reset($imageStyles);

        $this->assertEquals('large', $largeImageStyle->id());
    }

    /** @test */
    public function install_image_styles(): void
    {
        $this->enableModules([
            'image',
        ]);
        $this->setConfigDirectory('image_styles');

        $imageStyleStorage = $this->container->get('entity_type.manager')->getStorage('image_style');

        $this->assertEmpty($imageStyleStorage->loadMultiple());

        $imageStylesToInstall = [
            'large',
            'medium',
        ];

        $this->installImageStyles($imageStylesToInstall);

        $imageStyles = $imageStyleStorage->loadMultiple();

        $this->assertNotEmpty($imageStyles);

        $imageStyleIds = array_map(function(ImageStyle $imageStyle) {
            return $imageStyle->id();
        }, $imageStyles);

        $this->assertEquals($imageStylesToInstall, array_values($imageStyleIds));
    }

    /** @test */
    public function install_all_fields_for_entity(): void
    {
        $this->installEntitySchema('user');
        $this->installEntitySchema('node');

        $this->enableModules([
            'text',
        ]);

        $this->setConfigDirectory('node/bundles');
        $this->installBundle('node', 'page');

        $this->setConfigDirectory('node/fields');

        $nodeStorage = $this->container->get('entity_type.manager')->getStorage('node');

        $node = $nodeStorage->create([
            'nid' => 1,
            'type' => 'page',
            'title' => 'Node',
        ]);
        $node->save();

        $this->assertFalse($node->hasField('body'));
        $this->assertFalse($node->hasField('field_boolean'));

        $this->installAllFieldsForEntity('node', 'page');

        $node = $nodeStorage->load(1);

        $this->assertTrue($node->hasField('body'));
        $this->assertTrue($node->hasField('field_boolean_field'));
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

        $baseConfigPath = __DIR__ . '/__fixtures__/config/sync';

        if ($this->customConfigDirectory) {
            return $baseConfigPath . '/' . ltrim($this->customConfigDirectory, '/');
        }

        // providing our own directory with config we can test against
        return $baseConfigPath;
    }
}
