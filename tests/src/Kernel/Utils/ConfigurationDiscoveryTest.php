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
    public function loads_in_settings_without_php_errors(): void
    {
        $this->markTestIncomplete('Need to figure out how this test is going to work');

        $configurationDiscovery = ConfigurationDiscovery::createFromAppRoot(
            $this->container->get('app.root')
        );

//        (function() {
//            $this->settingsLocation = '../__fixtures__/settings/fixture.settings.php';
//        })->call($configurationDiscovery);
    }

    /** @test */
    public function test(): void
    {

    }
}
