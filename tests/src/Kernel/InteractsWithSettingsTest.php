<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\InteractsWithSettings;
use Drupal\Tests\test_traits\Kernel\Testing\Utils\ConfigurationDiscovery;

class InteractsWithSettingsTest extends KernelTestBase
{
    use InteractsWithSettings;

    /** @test */
    public function supresses_errors_when_requiring_settings(): void
    {
        $this->container->set('app.root', __DIR__);
        $this->settingsLocation = '/__fixtures__/settings/fixture.settings.php';

        $this->assertEquals(
            $this->container->get('app.root') . '/test/config/directory',
            $this->getConfigurationDirectory()
        );
    }

    /** @test */
    public function auto_discover_config_directory(): void
    {
        $this->autoDiscoverConfigDirectory = true;
    }
}
