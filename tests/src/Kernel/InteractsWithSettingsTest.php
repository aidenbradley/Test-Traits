<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\InteractsWithSettings;

class InteractsWithSettingsTest extends KernelTestBase
{
    use InteractsWithSettings;

    /**
     * @test
     *
     * "fixture.settings.php" contains a variable not defined in scope when it's loaded in.
     */
    public function supresses_errors_when_requiring_settings(): void
    {
        $this->container->set('app.root', __DIR__);
        $this->settingsLocation = '/__fixtures__/settings/fixture.settings.php';

        $this->assertEquals(
            $this->container->get('app.root') . '/test/config/directory',
            $this->getConfigurationDirectory()
        );
    }

    /**
     * @test
     *
     * "auto_discovered" is a setting set at /Kernel/__fixtures__/settings/auto_discover/settings.php
     */
    public function auto_discovers_settings(): void
    {
        $this->assertNull($this->getSettings()->get('auto_discovered'));

        // force InteractsWithSettings to find settings.php again
        // this time using finder to load in other settings
        $this->settings = null;
        $this->autoDiscoverSettings = true;

        $this->assertTrue($this->getSettings()->get('auto_discovered'));
    }
}
