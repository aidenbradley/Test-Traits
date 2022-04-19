<?php

namespace Drupal\Tests\test_traits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\test_traits\Kernel\Testing\Concerns\InteractsWithLanguages;

class InteractsWithLanguagesTest extends KernelTestBase
{
    use InteractsWithLanguages;

    private $languageManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->languageManager = $this->container->get('language_manager');
    }

    /** @test */
    public function installs_language(): void
    {
        $this->setCurrentLanguage('en');

        $this->assertEquals('en', $this->container);
    }
}
