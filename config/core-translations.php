<?php

return [

    /*
    * Basic requests roles checks.
    */
    'roles'                  => [
        'backend' => 'operator',
        'api'     => 'default',
    ],

    /*******************************Validation Messages Generator***************************************************/

    /*
    *--------------------------------------------------------------------------
    * Field aliases
    *--------------------------------------------------------------------------
    *
    * Possible validation fields aliases.
    * Default field alias is field name. For example: title_en -> Title en is required.
    *
    */
    'field_aliases'          => [
        'text.en' => 'translation in English',
    ],

    /*
    *--------------------------------------------------------------------------
    * Validation rules messages
    *--------------------------------------------------------------------------
    *
    * Messages for validation rules used in project.
    * max_array validation will be called on array max rule.
    * For example: ['max:3']
    * same for other multi types rules.
    * All rules will be callable by pattern: {camel rule name}RuleMessage($replaces = [], $locale = NULL)
    * For example: maxArrayRuleMessage()
    *
    */
    'validator_messages'     => [
        'email'           => 'Please, enter correct e-mail.',
        'required'        => ':Attribute is required.',
        'required_if'     => ':Attribute is required.',
        'string'          => ':Attribute must be a string.',
        'integer'         => ':Attribute should contain integer value.',
        'numeric'         => ':Attribute should contain numeric value.',
        'boolean'         => ':Attribute should contain boolean value.',
        'array'           => ':Attribute should contain array value.',
        'url'             => ':Attribute should contain valid URL. For example: :url_example.',
        'active_url'      => ':Attribute should contain valid and accessible URL. For example: :url_example.',
        'present'         => ':Attribute should be present in the form body.',
        'max_array'       => ':Attribute count can be maximum :max items.',
        'min_array'       => ':Attribute count should be minimum :max items.',
        'size_array'      => ':Attribute items count should be equals to :size.',
        'max_string'      => ':Attribute length can be maximum :max chars.',
        'min_string'      => ':Attribute length should be minimum :min chars.',
        'size_string'     => ':Attribute chars length should be equals to :size.',
        'max_numeric'     => ':Attribute value can be maximum :max.',
        'min_numeric'     => ':Attribute value should be minimum :min.',
        'size_numeric'    => ':Attribute value should be equals to :size.',
        'after_or_equal'  => ':Attribute value must be a date after or equal to :date.',
        'before_or_equal' => ':Attribute value must be a date before or equal to :date.',
    ],

    /*
    * In debug mode we dump validation messages.
    */
    'enable_debug'           => env('APP_KIT_TRANSLATIONS_DEBUG', false),

    /**
     * Locales array, that available for validation and models implementation.
     */
    'locales'                => explode(',', env('APP_KIT_TRANSLATIONS_LOCALES', 'en')),

    /*
    * Rules array that should be multi type.
    * Casting into string, array or numeric types
    */
    'multi_type_rules'       => [ 'min', 'max', 'size' ],

    /*******************************Extended Translations Loaded***************************************************/

    /*
     * Enable or disable db translations loader.
     */
    'enable_db_translations' => env('APP_KIT_TRANSLATIONS_ENABLE_DB_TRANSLATIONS', false),

    /*
     * Cookies and headers prefix
     */
    'core_prefix'            => env('APP_KIT_CORE_PREFIX', 'Attract Cores'),

    /*
     * Language lines will be fetched by these loaders. You can put any class here that implements
     * the AttractCores\LaravelCoreTranslation\Contracts\TranslationLoader interface.
     */
    'loaders'                => [
        \AttractCores\LaravelCoreTranslation\Loaders\DatabaseLoader::class,
    ],

    /*
     * This is the model used by the Db Translation loader. You can put any model here
     * that extends \AttractCores\LaravelCoreTranslation\Models\Translation.
     */
    'model'                  => \AttractCores\LaravelCoreTranslation\Models\Translation::class,

    /*
     * This is the translation manager which overrides the default Laravel `translation.loader`
     */
    'manager'                => \AttractCores\LaravelCoreTranslation\TranslationLoaderManager::class,
];
