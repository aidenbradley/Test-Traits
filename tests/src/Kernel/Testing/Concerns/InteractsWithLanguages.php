<?php

namespace Drupal\Tests\test_traits\Kernel\Testing\Concerns;

use Drupal\Core\Language\LanguageManagerInterface;

trait InteractsWithLanguages
{
    use InstallsExportedConfig;

    /** @var array */
    protected $installedLanguages = [
        'en' // EN is installed by default
    ];

    /** @var bool */
    protected $installLanguageModule = false;

    protected function languageManager(): LanguageManagerInterface
    {
        return $this->container->get('language_manager');
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
    protected function setCurrentLanguage($language, ?string $prefix = null): void
    {
        $this->setupLanguageDependencies();

        if (is_string($language)) {
            if (in_array($language, $this->installedLanguages) === false) {
                $this->installLanguage($language);
            }

            $language = $this->container->get('entity_type.manager')->getStorage(
                'configurable_language'
            )->load($language);
        }

        $this->config('system.site')
            ->set('langcode', $language->getId())
            ->set('default_langcode', $language->getId())
            ->save();

        if ($prefix !== null) {
            $languageNegotiation = $this->config('language.negotiation');

            $prefixes = $languageNegotiation->get('url.prefixes');

            $prefixes[$language->id()] = $prefix;

            $languageNegotiation->set('url.prefixes', $prefixes)->save();
        }

        $this->container->get('language.default')->set($language);

        $this->container->get('kernel')->rebuildContainer();

        $this->languageManager()->reset();

        $this->installedLanguages[$language->getId()] = $language;
    }

    private function setupLanguageDependencies(): void
    {
        if ($this->installLanguageModule) {
            return;
        }

        $this->enableModules(['language']);
        $this->installConfig('language');
        $this->installEntitySchema('configurable_language');

        $this->installLanguageModule = true;
    }
}
