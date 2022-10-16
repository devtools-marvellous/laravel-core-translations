<?php

namespace AttractCores\LaravelCoreTranslation\Database\Factories;

use AttractCores\LaravelCoreTranslation\Contracts\HasTranslations;
use AttractCores\LaravelCoreTranslation\Models\TranslatedSimpleCrudModel;
use AttractCores\LaravelCoreTranslation\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class TranslatedSimpleCrudFactory
 *
 * @package ${NAMESPACE}
 * Date: 10.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class TranslatedSimpleCrudFactory extends Factory
{

    /**
     * Real text fakers on each locale.
     *
     * @var array
     */
    public static array $fakers = [];

    /**
     * Faker locales map.
     *
     * @var array|string[]
     */
    public static array $localesMap = [
        'en' => 'en_US',
    ];

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TranslatedSimpleCrudModel::class;

    /**
     * Date fields names, that should not be nullable
     *
     * @var array
     */
    protected array $unNullableDates = [];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'       => $this->faker->realText(55),
            'is_active'  => true,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Use this function to run with ->raw().
     *
     * @return \AttractCores\LaravelCoreTranslation\Database\Factories\TranslatedSimpleCrudFactory
     */
    public function forRequest()
    {
        return $this->state($this->getRequestState());
    }

    /**
     * Return model state for request.
     *
     * @return array
     */
    protected function getRequestState()
    {
        $model = $this->newModel();

        $dates = [];
        foreach ( array_diff($model->getDates(), $this->unNullableDates) as $dateField ) {
            $dates[ $dateField ] = NULL;
        }

        $translatableFields = [];
        foreach ( $model->translatableFields() as $field ) {
            $translatableFields[ $field ] = $this->generateLocalizedArray();
        }

        return array_merge($translatableFields, $dates);
    }

    /**
     * Return model data for request from other model. Can be used in update requests.
     *
     * @param HasTranslations $model
     * @param array           $extendedFields
     *
     * @return array
     */
    public function forRequestFromModel(HasTranslations $model, array $extendedFields = [])
    {
        $default = $this->getDefaultFieldsFromModel($model);

        foreach ( $model->translatableFields() as $translatableField ) {
            $neededTranslation = $model->translations->filter(function (Translation $translation) use ($translatableField) {
                return Str::contains($translation->key, $translatableField);
            })->first();

            if ( $neededTranslation ) {
                $default[ $translatableField ] = $neededTranslation->text;
            } else {
                $default[ $translatableField ] = $this->generateLocalizedArray(false, 100);
            }
        }

        return array_replace_recursive($default, $extendedFields);
    }

    /**
     * Return default model fields for request.
     *
     * @param \AttractCores\LaravelCoreTranslation\Contracts\HasTranslations $model
     *
     * @return array
     */
    protected function getDefaultFieldsFromModel(HasTranslations $model)
    {
        return [
            'is_active' => $model->is_active,
        ];
    }

    /**
     * Create not active
     *
     * @return \AttractCores\LaravelCoreTranslation\Database\Factories\TranslatedSimpleCrudFactory
     */
    public function notActive()
    {
        return $this->state([
            'is_active' => false,
        ]);
    }

    /**
     * Configure hooks for factory.
     *
     * @return \AttractCores\LaravelCoreTranslation\Database\Factories\TranslatedSimpleCrudFactory
     */
    public function configure()
    {
        return $this->afterCreating(function (HasTranslations $model) {
            foreach ( $model->translatableFields() as $translatableField ) {
                $trans = new Translation([
                    'group'             => $model->getTranslationGroup(),
                    'key'               => $model->getFieldTranslationKey($translatableField),
                    'translatable_id'   => $model->getTranslatableMorphId(),
                    'translatable_type' => get_class($model),
                ]);

                $text = [];

                foreach ( app('translation.locales') as $locale ) {
                    $text[ $locale ] = is_null($model->$translatableField) || $locale == config('app.fallback_locale') ?
                        $model->$translatableField :
                        $this->getFakerGeneratorByLocale($locale)->realText(55);
                }

                $trans->text = $text;
                $trans->saveQuietly();
            }
        });
    }

    /**
     * Generate localized array.
     *
     * @param bool $withNullable
     * @param int  $length
     *
     * @return array
     */
    protected function generateLocalizedArray(bool $withNullable = false, int $length = 55)
    {
        $result = [];

        foreach ( app('translation.locales') as $locale ) {
            $result[ $locale ] = ( $withNullable && $this->faker->boolean() ) || ! $withNullable ?
                $this->getFakerGeneratorByLocale($locale)->realText($length) : NULL;
        }

        return $result;
    }

    /**
     * Return faked nullable locales.
     *
     * @return array
     */
    protected function getNullableLocales()
    {
        $result = [];

        foreach ( app('translation.locales') as $locale ) {
            if ( ! $this->faker->boolean() ) {
                $result[ $locale ] = NULL;
            }
        }

        return $result;
    }

    /**
     * Return faker generator by locale
     *
     * @param string $locale
     *
     * @return \Faker\Generator
     */
    protected function getFakerGeneratorByLocale(string $locale)
    {
        return static::$fakers[ $locale ] ??
               \Faker\Factory::create(static::$localesMap[ $locale ] ?? config('app.faker_locale'));
    }

    /**
     * Make locale fakes for localized translations.
     */
    public static function makeLocalesFakers()
    {
        foreach ( app('translation.locales') as $locale ) {
            static::$fakers[ $locale ] = \Faker\Factory::create(static::$localesMap[ $locale ] ??
                                                                config('app.faker_locale'));
        }
    }

}
