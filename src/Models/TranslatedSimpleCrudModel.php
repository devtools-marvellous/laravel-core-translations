<?php

namespace AttractCores\LaravelCoreTranslation\Models;

use Amondar\Sextant\Models\SextantModel;
use AttractCores\LaravelCoreTranslation\Contracts\HasTranslations;
use AttractCores\LaravelCoreTranslation\Extensions\InteractsWithTranslations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Class TranslatedSimpleCrudModel
 *
 * @property string      name       - Model name in fallback locale.
 * @property bool        is_active  - Determine that model is active.
 * @property Carbon|NULL created_at - Created at date.
 * @property Carbon|NULL updated_at - Updated at date.
 *
 * @package AttractCores\LaravelCoreTranslation\Models
 * Date: 10.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class TranslatedSimpleCrudModel extends SextantModel implements HasTranslations
{

    use InteractsWithTranslations;

    /**
     * Model fillables.
     *
     * @var array
     */
    protected $fillable = [ 'is_active' ];

    /**
     * Cast some data.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Return translatable fields.
     *
     * @return string[]
     */
    public function translatableFields() : array
    {
        return [ 'name' ];
    }

    /**
     * Accessible relations for api.
     *
     * @return array|string[]
     */
    public function extraFields()
    {
        $request = request();

        return $request->routeValue(Str::snake(class_basename($this))) ? [ 'translations' ] : [];
    }

    /**
     * Accessible scopes for api.
     *
     * @return array|string[]
     */
    public function extraScopes()
    {
        return [ 'active', 'searchByTranslatableField', 'searchByTranslatableFieldInAnyLocale' ];
    }

    /**
     * Return only active data.
     *
     * @param $query
     */
    public function scopeActive($query)
    {
        $query->where('is_active', true);
    }

}