<?php

namespace Drupal\Tests\test_traits\Kernel\Utils;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Testing\Utils\ConfigurationDiscovery;

class ConfigurationDiscoveryTest extends KernelTestBase
{
    /** @test */
    public function create_instance(): void
    {
        $this->assertInstanceOf(
            ConfigurationDiscovery::class,
            ConfigurationDiscovery::createFromAppRoot($this->container->get('app.root'))
        );
    }

    /** @test */
    public function supresses_errors_when_requiring_settings(): void
    {
        $this->container->set('app.root', __DIR__);

        $configurationDiscovery = ConfigurationDiscovery::createFromAppRoot(
            $this->container->get('app.root')
        );

        (function() {
            $this->settingsLocation = '../__fixtures__/settings/fixture.settings.php';
        })->call($configurationDiscovery);

        $this->assertEquals(
            $this->container->get('app.root') . '/test/config/directory',
            $configurationDiscovery->getConfigurationDirectory()
        );
    }

    /** @test */
    public function test(): void
    {

    }
}
