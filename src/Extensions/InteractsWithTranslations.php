<?php

namespace AttractCores\LaravelCoreTranslation\Extensions;

use AttractCores\LaravelCoreTranslation\Exceptions\TranslationsArrayHasWrongLocales;
use AttractCores\LaravelCoreTranslation\Models\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * trait InteractsWithTranslations
 *
 * @property Collection translations - Collection of model translations
 *
 * @method  searchByTranslatableField( $query, string $fieldName, string $search, string $locale = NULL ) -  Return
 *          models searched by translatable field in give or current locale.
 * @method  searchByTranslatableFieldInAnyLocale( $query, string $fieldName, string $search ) - Return models searched
 *          by translatable field in any possible locale.
 *
 * @package ${NAMESPACE}
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
trait InteractsWithTranslations
{

    /**
     * Run trait.
     *
     * @throws \AttractCores\LaravelCoreTranslation\Exceptions\TranslationsArrayHasWrongLocales
     */
    public static function bootInteractsWithTranslations()
    {
        static::saving(function (self $model) {
            $model->performTranslatablemodelSaving();
        });

        static::saved(function (self $model) {
            $model->afterTranslatableModelSaved();
        });

        // Delete translation on model delete.
        static::deleted(function (self $model) {
            $model->performModelTranslationsDelete();
        });
    }

    /**
     * Translations relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOneOrMany
     */
    public function translations() : MorphOneOrMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * Return models searched by translatable field in give or current locale.
     *
     * @param             $query
     * @param string      $fieldName
     * @param string      $search
     * @param string|null $locale
     */
    public function scopeSearchByTranslatableField($query, string $fieldName, string $search, string $locale = NULL)
    {
        $locale = $locale ?? app()->getLocale();

        $query->whereHas('translations', function (Builder $query) use ($fieldName, $search, $locale) {
            $query->searchByKeyTranslation($fieldName, $search, $locale);
        });
    }

    /**
     * Return models searched by translatable field in any possible locale.
     *
     * @param             $query
     * @param string      $fieldName
     * @param string      $search
     */
    public function scopeSearchByTranslatableFieldInAnyLocale($query, string $fieldName, string $search)
    {
        $query->whereHas('translations', function (Builder $query) use ($fieldName, $search) {
            $query->searchByKeyTranslationInAnyLocale($fieldName, $search);
        });
    }

    /**
     * Return fully completed translation key for a field.
     *
     * @param string $fieldName
     *
     * @return string
     */
    public function getFieldTranslationFullKey(string $fieldName) : string
    {
        return sprintf('db-trans::%s.%s', $this->getTranslationGroup(), $this->getFieldTranslationKey($fieldName));
    }

    /**
     * Return translation by field.
     *
     * @param string $fieldName
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function getTranslationByField(string $fieldName)
    {
        if ( app()->getLocale() == config('app.fallback_locale') && Arr::has($this->original, $fieldName) ) {
            return $this->$fieldName;
        } else {
            $key = $this->getFieldTranslationFullKey($fieldName);
            $translation = __($key);

            return $translation == $key ? NULL : $translation;
        }
    }

    /**
     * Run needed translation changes after model saved.
     */
    public function afterTranslatableModelSaved()
    {
        foreach ( $this->getTranslationsFromRequest(request()) as $fieldName => $translations ) {
            $group = $this->getTranslationGroup();
            $key = $this->getFieldTranslationKey($fieldName);

            Translation::updateOrCreate(compact('group', 'key'), [
                'group'              => $group,
                'key'                => $key,
                'translatable_type'  => $this->getMorphClass(),
                'translatable_id'    => $this->getTranslatableMorphId(),
                'translatable_field' => $fieldName,
                'text'               => $translations,
            ]);
        }
    }

    /**
     * Perform translatable model saving.
     *
     * @throws \AttractCores\LaravelCoreTranslation\Exceptions\TranslationsArrayHasWrongLocales
     */
    public function performTranslatableModelSaving()
    {
        $appLocales = app('translation.locales');

        foreach ( $this->getTranslationsFromRequest(request()) as $fieldName => $translations ) {
            if ( is_array($translations) && empty(array_diff(array_keys($translations), $appLocales)) ) {
                // Set default field translation.
                $this->$fieldName = $translations[ config('app.fallback_locale') ];
            } else {
                throw TranslationsArrayHasWrongLocales::invalidLocalizedTranslations(
                    $fieldName, $appLocales, (array) $translations
                );
            }
        }
    }

    /**
     * Return translations from request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function getTranslationsFromRequest(Request $request)
    {
        return $request->only($this->translatableFields());
    }

    /**
     * Delete model translations on model deletion.
     */
    protected function performModelTranslationsDelete()
    {
        $this->translations()->delete();
    }

    /**
     * Return field translation key value.
     *
     * @param string $fieldName
     *
     * @return string
     */
    public function getFieldTranslationKey(string $fieldName) : string
    {
        return $this->getTranslatableMorphId() . '_' . $fieldName;
    }

    /**
     * Return translation group.
     *
     * @return string
     */
    public function getTranslationGroup() : string
    {
        return $this->getTable();
    }

    /**
     * Return model id for translations morph relation.
     *
     * @return string
     */
    public function getTranslatableMorphId() : string
    {
        return (string) $this->getKey();
    }

}