<?php

namespace AttractCores\LaravelCoreTranslation;

use AttractCores\LaravelCoreTranslation\Contracts\TranslationLoader;
use Illuminate\Translation\FileLoader;

/**
 * Class TranslationManager
 *
 * @package AttractCores\LaravelCoreTranslation
 * Date: 03.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class TranslationLoaderManager extends FileLoader
{

    /**
     * Namespaces cache.
     *
     * @var array
     */
    protected array $loadersNamespaces = [];

    /**
     * Load the messages for the given locale.
     *
     * @param string $locale
     * @param string $group
     * @param string $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = NULL) : array
    {
        $fileTranslations = parent::load($locale, $group, $namespace);

        if ( ! is_null($namespace) && ! in_array($namespace, $this->getValidNamespaces()) ) {
            return $fileTranslations;
        }

        $loaderTranslations = $this->getTranslationsForTranslationLoaders($locale, $group, $namespace);

        return array_replace_recursive($fileTranslations, $loaderTranslations);
    }

    /**
     * Return array of namespaces.
     *
     * @return array
     */
    protected function getValidNamespaces() : array
    {
        if ( ! empty($this->loadersNamespaces) ) {
            return $this->loadersNamespaces;
        }

        foreach ( config('core-translations.loaders') as $loaderClass ) {
            $this->loadersNamespaces = array_merge($this->loadersNamespaces, app($loaderClass)->getNamespaces());
        }

        return $this->loadersNamespaces = array_unique($this->loadersNamespaces);
    }

    /**
     * Flush loader namespaces, if needed.
     *
     * @return $this
     */
    public function flushLoadersNamespaces()
    {
        $this->loadersNamespaces = [];

        return $this;
    }


    /**
     * Return translations from loaders
     *
     * @param string      $locale
     * @param string      $group
     * @param string|null $namespace
     *
     * @return array
     */
    protected function getTranslationsForTranslationLoaders(
        string $locale,
        string $group,
        string $namespace = NULL
    ) : array
    {
        return collect(config('core-translations.loaders'))
            ->map(function (string $className) {
                return app($className);
            })
            ->mapWithKeys(function (TranslationLoader $translationLoader) use ($locale, $group, $namespace) {
                return $translationLoader->loadTranslations($locale, $group, $namespace);
            })
            ->toArray();
    }

}