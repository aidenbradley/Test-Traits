<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\InteractsWithLanguages;
use Throwable;

class InteractsWithLanguagesTest extends KernelTestBase
{
    use InteractsWithLanguages;

    protected static $modules = [
        'system',
    ];

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

    protected function configDirectory(): string
    {
        return __DIR__ . '/__fixtures__/config/sync/languages';
    }
}
