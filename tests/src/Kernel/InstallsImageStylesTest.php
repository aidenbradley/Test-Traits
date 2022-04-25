<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\image\Entity\ImageStyle;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\InstallsImageStyles;

class InstallsImageStylesTest extends KernelTestBase
{
    use InstallsImageStyles;

    protected function setUp()
    {
        parent::setUp();

        $this->setConfigDirectory(__DIR__ . '/__fixtures__/config/sync/image_styles');
    }

    /** @test */
    public function installing_image_style_prepares_dependencies(): void
    {
        $this->installImageStyles('large');

        $imageStyleStorage = $this->container->get('entity_type.manager')->getStorage('image_style');

        $this->assertNotEmpty($imageStyleStorage->loadMultiple());
    }

    /** @test */
    public function install_single_image_style(): void
    {
        $this->enableModules([
            'image',
        ]);
        $this->installEntitySchema('image_style');

        $imageStyleStorage = $this->container->get('entity_type.manager')->getStorage('image_style');

        $this->assertEmpty($imageStyleStorage->loadMultiple());

        $this->installImageStyles('large');

        $imageStyles = $imageStyleStorage->loadMultiple();

        $this->assertNotEmpty($imageStyles);

        $largeImageStyle = reset($imageStyles);

        $this->assertEquals('large', $largeImageStyle->id());
    }

    /** @test */
    public function install_multiple_image_styles(): void
    {
        $this->enableModules([
            'image',
        ]);
        $this->installEntitySchema('image_style');

        $this->setConfigDirectory(__DIR__ . '/__fixtures__/config/sync/image_styles');

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
}
