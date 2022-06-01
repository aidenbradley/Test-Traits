<?php

namespace Drupal\Tests\test_traits\Unit\Util;

use Drupal\Tests\test_traits\Kernel\Testing\Exceptions\SettingsFailed;
use Drupal\Tests\test_traits\Kernel\Testing\Utils\Settings;
use Drupal\Tests\UnitTestCase;

class SettingsTest extends UnitTestCase
{
    /** @test */
    public function get_setting(): void
    {
        $settings = Settings::create([
            'settings_value' => 'example_value',
        ]);

        $this->assertEquals('example_value', $settings->get('settings_value'));
    }

    /** @test */
    public function throws_exception_for_unknown_settings_value(): void
    {
        $settings = Settings::create([]);

        try {
            $settings->get('undefined_settings_key');
        } catch(SettingsFailed $exception) {
            $this->assertEquals(SettingsFailed::SETTINGS_DOES_NOT_EXIST, $exception->getCode());
        }
    }
}
