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
}
