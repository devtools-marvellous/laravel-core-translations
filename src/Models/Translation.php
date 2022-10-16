<?php

namespace AttractCores\LaravelCoreTranslation\Models;

use Amondar\Sextant\Models\SextantModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use phpDocumentor\Reflection\Types\Nullable;

/**
 * Class Translation
 *
 * @property integer     id                 - Model Id.
 * @property string      group              - Translation group.
 * @property string      key                - Translation key.
 * @property array       text               - Translations array, locale => text.
 * @property string|NULL translatable_field - Translatable model class field.
 * @property string|NULL translatable_type  - Translatable model class.
 * @property string|NULL translatable_id    - Translatable model ID.
 *
 * @package AttractCores\LaravelCoreTranslation\Models
 * Date: 03.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class Translation extends SextantModel
{

    /** @var array */
    public $guarded = [ 'id' ];

    /** @var array */
    protected $casts = [ 'text' => 'array' ];

    /**
     * Boot model
     */
    public static function boot()
    {
        parent::boot();

        $flushGroupCache = function (self $languageLine) {
            $languageLine->flushGroupCache();
        };

        static::saved($flushGroupCache);
        static::deleted($flushGroupCache);
    }

    /**
     * Return scopes accessible from api.
     *
     * @return array|string[]
     */
    public function extraScopes()
    {
        return [ 'searchByTranslationsInAnyLocale' ];
    }

    /**
     * Return models searched by translatable field in give or current locale.
     *
     * @param             $query
     * @param string      $key
     * @param string      $search
     * @param string|null $locale
     */
    public function scopeSearchByKeyTranslation($query, string $key, string $search, string $locale = NULL)
    {
        $locale = $locale ?? app()->getLocale();

        $query->where('key', 'like', "%$key%")
              ->whereRaw("LOWER(json_extract(`text`, '$.\"$locale\"')) LIKE LOWER(?)", [ '%' . $search . '%' ]);
    }

    /**
     * Return models searched by translatable field in any possible locale.
     *
     * @param             $query
     * @param string      $key
     * @param string      $search
     */
    public function scopeSearchByKeyTranslationInAnyLocale($query, string $key, string $search)
    {
        $query->where('key', 'like', "%$key%")
              ->searchByTranslationsInAnyLocale($search);
    }

    /**
     * Return models searched by translatable field in any possible locale.
     *
     * @param             $query
     * @param string      $search
     */
    public function scopeSearchByTranslationsInAnyLocale($query, string $search)
    {
        $query->where(function (Builder $query) use ($search) {
            foreach ( app('translation.locales') as $locale ) {
                $query->orWhereRaw("LOWER(json_extract(`text`, '$.\"$locale\"')) LIKE LOWER(?)",
                    [ '%' . $search . '%' ]);
            }
        });
    }

    /**
     * Related translatable model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function translatable()
    {
        return $this->morphTo('translatable');
    }

    /**
     * Return cache key for given group and locale
     *
     * @param string $group
     * @param string $locale
     *
     * @return string
     */
    public static function getCacheKey(string $group, string $locale) : string
    {
        return "attract.core-translations.{$group}.{$locale}";
    }

    /**
     * Return database translations.
     *
     * @param string $locale
     * @param string $group
     *
     * @return array
     */
    public static function getTranslationsForGroup(string $locale, string $group) : array
    {
        try {
            return Cache::rememberForever(static::getCacheKey($group, $locale), function () use ($group, $locale) {
                return static::query()
                             ->where('group', $group)
                             ->cursor()
                             ->reduce(function ($lines, self $languageLine) use ($group, $locale) {
                                 $translation = $languageLine->getTranslation($locale);

                                 if ( $translation !== NULL && $group === '*' ) {
                                     // Make a flat array when returning json translations
                                     $lines[ $languageLine->key ] = $translation;
                                 } elseif ( $translation !== NULL && $group !== '*' ) {
                                     // Make a nesetd array when returning normal translations
                                     Arr::set($lines, $languageLine->key, $translation);
                                 }

                                 return $lines;
                             }) ?? [];
            });
        }catch(\Throwable $e){
            report($e);

            return [];
        }
    }

    /**
     * Return translation for given locale.
     *
     * @param string $locale
     *
     * @return string
     */
    public function getTranslation(string $locale) : ?string
    {
        if ( ! Arr::has($this->text, $locale) ) {
            return Arr::get($this->text, config('app.fallback_locale'));
        }

        return Arr::get($this->text, $locale);
    }

    /**
     * Set translation for given locale
     *
     * @param string $locale
     * @param string $value
     *
     * @return $this
     */
    public function setTranslation(string $locale, string $value)
    {
        $this->text = array_merge($this->text ?? [], [ $locale => $value ]);

        return $this;
    }

    /**
     * Flush cache on current row group.
     */
    public function flushGroupCache()
    {
        foreach ( $this->getTranslatedLocales() as $locale ) {
            Cache::forget(static::getCacheKey($this->group, $locale));
        }
    }

    /**
     * Return translated locales for current row.
     *
     * @return array
     */
    protected function getTranslatedLocales() : array
    {
        return array_keys($this->text);
    }

}
