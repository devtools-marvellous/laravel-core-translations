<?php

namespace AttractCores\LaravelCoreTranslation\Loaders;

use AttractCores\LaravelCoreTranslation\Contracts\TranslationLoader;
use AttractCores\LaravelCoreTranslation\Models\Translation;
use Spatie\TranslationLoader\Exceptions\InvalidConfiguration;
use Spatie\TranslationLoader\LanguageLine;

/**
 * Class DatabaseLoader
 *
 * @package AttractCores\LaravelCoreTranslation\Loaders
 * Date: 03.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class DatabaseLoader implements TranslationLoader
{


    /**
     * Return valid namespaces.
     *
     * @return string[]
     */
    public function getNamespaces() : array
    {
        return ['*', 'db-trans'];
    }

    /**
     * Load translations.
     *
     * @param string $locale
     * @param string $group
     * @param string $namespace
     *
     * @return array
     * @throws \Spatie\TranslationLoader\Exceptions\InvalidConfiguration
     */
    public function loadTranslations(string $locale, string $group, string $namespace): array
    {
        if(in_array($namespace, $this->getNamespaces())) {
            $model = $this->getConfiguredModelClass();


            return $model::getTranslationsForGroup($locale, $group);
        }

        return [];
    }

    /**
     * Return model class name
     *
     * @return string
     * @throws \Spatie\TranslationLoader\Exceptions\InvalidConfiguration
     */
    protected function getConfiguredModelClass(): string
    {
        $modelClass = config('core-translations.model');

        if (! is_a(new $modelClass, Translation::class)) {
            throw InvalidConfiguration::invalidModel($modelClass);
        }

        return $modelClass;
    }

}