<?php

namespace AttractCores\LaravelCoreTranslation\Testing;

use AttractCores\LaravelCoreTranslation\Models\Translation;

/**
 * Trait TranslationTesting
 *
 * @package ${NAMESPACE}
 * Date: 03.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
trait TranslationTesting
{

    /**
     * Create new test translation
     *
     * @param string $group
     * @param string $key
     * @param array  $text
     *
     * @return \AttractCores\LaravelCoreTranslation\Models\Translation
     */
    protected function createTranslation(string $group, string $key, array $text) : Translation
    {
        return Translation::create(compact('group', 'key', 'text'));
    }

}