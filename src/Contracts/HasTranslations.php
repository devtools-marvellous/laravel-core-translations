<?php

namespace AttractCores\LaravelCoreTranslation\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Support\Collection;

/**
 * Interface HasTranslations
 *
 * @property Collection translations - Collection of model translations
 *
 * @package AttractCores\LaravelCoreTranslation\Contracts
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
interface HasTranslations
{
    /**
     * Return fields that should have translations.
     *
     * @return array
     */
    public function translatableFields() : array;

    /**
     * Return field translation key value.
     *
     * @param string $fieldName
     *
     * @return string
     */
    public function getFieldTranslationKey(string $fieldName) : string;

    /**
     * Return translation group.
     *
     * @return string
     */
    public function getTranslationGroup() : string;

    /**
     * Return model id for translations morph relation.
     *
     * @return string
     */
    public function getTranslatableMorphId() : string;

    /**
     * Translations relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOneOrMany
     */
    public function translations() : MorphOneOrMany;

}