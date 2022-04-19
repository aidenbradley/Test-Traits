<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

trait InteractsWithLanguages
{
    use InstallsExportedConfig;

    protected $installLanguageModule = false;

    private function setupLanguageDependencies(): void
    {
        if ($this->installLanguageModule) {
            return;
        }

        $this->enableModules(['language']);
        $this->installConfig('language');
        $this->installEntitySchema('configurable_language');
        $this->config('language.negotiation')->set('url.prefixes', [
            'en' => '',
            'fr' => 'fr-fr',
            'de' => 'de-de',
        ])->save();
        $this->installLanguageModule = true;
    }

    /** @param string|array $langcode */
    protected function installLanguage($langcodes): void
    {
        $this->setupLanguageDependencies();

        foreach ((array)$langcodes as $langcode) {
            $this->installExportedConfig('language.entity.' . $langcode);
        }
    }

    /** @param \Drupal\Language\Entity\ConfigurableLanguage|string */
    protected function setCurrentLanguage($language): void
    {
        $this->setupLanguageDependencies();

        if (is_string($language)) {
            if (in_array($language, $this->installedLanguages) === false) {
                $this->installLanguage($language);
            }

            $language = $this->storage('configurable_language')->load($language);
        }

        $systemConfig = $this->config('system.site');

        $systemConfig->set('langcode', $language->getId());
        $systemConfig->set('default_langcode', $language->getId());
        $systemConfig->save();

        $this->container->get('language.default')->set($language);

        $this->container->get('language_manager')->reset();

        $this->installedLanguages[$language->getId()] = $language;
    }
}
