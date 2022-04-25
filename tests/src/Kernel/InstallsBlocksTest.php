<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\block\Entity\Block;
use Drupal\Core\Config\Entity\ConfigEntityType;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\InstallsBlocks;

class InstallsBlocksTest extends KernelTestBase
{
    use InstallsBlocks;

    protected static $modules = [
        'system',
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->setConfigDirectory(__DIR__ . '/__fixtures__/config/sync/blocks');
    }

    /** @test */
    public function installing_block_prepares_dependencies(): void
    {
        $moduleHandler = $this->container->get('module_handler');

        $this->assertFalse($moduleHandler->moduleExists('block'));

        $entityTypeDefinitions = $this->container->get('entity_type.manager')->getDefinitions();
        $this->assertArrayNotHasKey('block', $entityTypeDefinitions);

        $this->installBlocks('seven_branding');

        $this->assertTrue($moduleHandler->moduleExists('block'));
        $this->assertInstanceOf(ConfigEntityType::class, $this->container->get('entity_type.manager')->getDefinition('block'));
    }

    /** @test */
    public function installs_single_block(): void
    {
        $this->enableModules([
            'block',
        ]);
        $this->installEntitySchema('block');

        $blockStorage = $this->container->get('entity_type.manager')->getStorage('block');

        $this->assertEmpty($blockStorage->loadMultiple());

        $this->installBlocks('seven_branding');

        $blocks = $blockStorage->loadMultiple();

        $this->assertNotEmpty($blocks);

        $sevenBrandingBlock = reset($blocks);

        $this->assertEquals('seven_branding', $sevenBrandingBlock->id());
    }

    /** @test */
    public function installs_multiple_blocks(): void
    {
        $this->enableModules([
            'block',
        ]);
        $this->installEntitySchema('block');

        $blockStorage = $this->container->get('entity_type.manager')->getStorage('block');

        $this->assertEmpty($blockStorage->loadMultiple());

        $this->installBlocks([
            'seven_branding',
            'stark_messages',
        ]);

        $this->assertInstanceOf(Block::class, $blockStorage->load('seven_branding'));
        $this->assertInstanceOf(Block::class, $blockStorage->load('stark_messages'));
    }
}
