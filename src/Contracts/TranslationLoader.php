<?php

namespace AttractCores\LaravelCoreTranslation\Contracts;

/**
 * Interface TranslationLoader
 *
 * @package AttractCores\LaravelCoreTranslation\Contracts
 * Date: 03.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
interface TranslationLoader
{

    /**
     * Returns all translations for the given locale and group.
     *
     * @param string $locale
     * @param string $group
     * @param string $namespace
     *
     * @return array
     */
    public function loadTranslations(string $locale, string $group, string $namespace): array;

    /**
     * Return valid namespaces, that work with given loader.
     *
     * @return array
     */
    public function getNamespaces() : array;
}