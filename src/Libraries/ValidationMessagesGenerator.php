<?php

namespace AttractCores\LaravelCoreTranslation\Libraries;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class ValidationMessagesGenerator
 *
 * @method string requiredRuleMessage( $replaces = [], $locale = NULL );
 * @method string stringRuleMessage( $replaces = [], $locale = NULL );
 * @method string integerRuleMessage( $replaces = [], $locale = NULL );
 * @method string numericRuleMessage( $replaces = [], $locale = NULL );
 * @method string booleanRuleMessage( $replaces = [], $locale = NULL );
 * @method string urlRuleMessage( $replaces = [], $locale = NULL );
 * @method string arrayRuleMessage( $replaces = [], $locale = NULL );
 * @method string activeUrlRuleMessage( $replaces = [], $locale = NULL );
 * @method string presentRuleMessage( $replaces = [], $locale = NULL );
 * @method string minArrayRuleMessage( $replaces = [], $locale = NULL );
 * @method string maxArrayRuleMessage( $replaces = [], $locale = NULL );
 * @method string minStringRuleMessage( $replaces = [], $locale = NULL );
 * @method string maxStringRuleMessage( $replaces = [], $locale = NULL );
 * @method string uniqueRuleMessage( $replaces = [], $locale = NULL );
 * @method string existsRuleMessage( $replaces = [], $locale = NULL );
 * @method string inRuleMessage( $replaces = [], $locale = NULL );
 *
 * @package App\Libraries
 * Date: 25.01.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class ValidationMessagesGenerator
{

    /**
     * @var array
     */
    protected array $possibleMessages = [];

    /**
     * @var array
     */
    protected array $possibleFieldAliases = [];

    /**
     * Cached rules messages.
     *
     * @var array
     */
    protected array $translatedRuleMessages = [];

    /**
     * Cached field aliases.
     *
     * @var array
     */
    protected array $translatedFieldAliases = [];

    /**
     * Rules on validation.
     *
     * @var array
     */
    protected array $rules;

    /**
     * Validated data array.
     *
     * @var array
     */
    protected array $validated;

    /**
     * Define multi type rules.
     *
     * @var array|string[]
     */
    protected array $multiTypeRules = [];

    /**
     * Determine that field can be null.
     *
     * @var bool
     */
    protected bool $nullableField = false;

    protected bool $sometimesField = false;

    protected ?string $fieldType = NULL;

    protected string $defaultValueForNotExists;

    /**
     * ValidationMessagesGenerator constructor.
     *
     * @param array $rules
     * @param array $validated
     */
    public function __construct(array $rules, array $validated)
    {
        $this->rules = $rules;
        $this->validated = $validated;
        $this->multiTypeRules = config('core-translations.multi_type_rules');
        $this->defaultValueForNotExists = md5('not_exists_in_body');
    }

    /**
     * Magic call method.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ( Str::contains($method, 'RuleMessage') ) {
            $rule = explode('RuleMessage', $method)[ 0 ];

            return $this->getRuleMessage(Str::snake($rule), $parameters);
        } elseif ( method_exists($this, $method) ) {
            return $this->$method(...$parameters);
        }

        $class = get_called_class();
        throw new \BadMethodCallException("Method [$method] does not exist in $class.");
    }

    /**
     * Generate default messages
     *
     * @param array $extended
     *
     * @return array
     */
    public function generateMessages($extended = []) : array
    {
        $messages = [];

        foreach ( $this->rules as $fieldName => $fieldRules ) {
            foreach ( $fieldRules as $rule ) {
                $rule = $this->tryRuleCastingIntoString($rule);

                if ( ! empty($rule) && is_string($rule) && ! in_array($rule, [ 'nullable', 'bail', 'sometimes' ]) ) {
                    $this->fieldType = $this->fieldType ?? $this->detectFieldType($rule);

                    if (
                        ! $this->nullableField || (
                            $this->nullableField &&
                            ! is_null($this->getFieldValue($fieldName,
                                $this->sometimesField ? NULL : $this->defaultValueForNotExists))
                        )
                    ) {
                        $rule = explode(':', $rule)[ 0 ];
                        $key = $fieldName . '.' . $rule;
                        $rulePolymorphicCallMethodName = $this->getPolymorphicMethodName($rule, $fieldName);

                        $messages[ $key ] = $extended[ $key ] ?? (
                            $rulePolymorphicCallMethodName ?
                                $this->$rulePolymorphicCallMethodName([
                                    'attribute' => $this->getFieldNameAlias($fieldName),
                                ]) : $key
                            );
                    }
                } else { // Calculate field cons
                    $this->nullableField = ! $this->nullableField ? $rule == 'nullable' : true;
                    $this->sometimesField = ! $this->sometimesField ? $rule == 'sometimes' : true;
                }
            }

            // Set to default before next field circle.
            $this->nullableField = false;
            $this->sometimesField = false;
            $this->fieldType = NULL;
        }

        if ( config('core-translations.enable_debug') ) {
            dump($messages);
        }

        return $messages;
    }

    /**
     * Return field type by rule.
     *
     * @param string $rule
     *
     * @return string|null
     */
    protected function detectFieldType(string $rule) : ?string
    {
        if ( $rule == 'numeric' || $rule == 'integer' ) {
            return 'numeric';
        } elseif ( $rule == 'string' ) {
            return 'string';
        } elseif ( $rule == 'array' ) {
            return 'array';
        }

        return NULL;
    }

    /**
     * Try rule casting into string.
     *
     * @param $rule
     *
     * @return mixed
     */
    protected function tryRuleCastingIntoString($rule)
    {
        try {
            return explode(':', $rule)[ 0 ];
        } catch ( \Throwable $e ) {
            return $rule;
        }
    }

    /**
     * Return polymorphic method name.
     *
     * @param string $rule
     * @param string $fieldName
     *
     * @return string
     */
    protected function getPolymorphicMethodName(string $rule, string $fieldName)
    {
        if ( in_array($rule, $this->multiTypeRules) ) {
            $value = $this->getFieldValue($fieldName);

            switch ( true ) {
                case ( is_numeric($value) || $this->fieldType == 'numeric' ):
                    return Str::camel($rule . 'NumericRuleMessage');
                case ( is_string($value) || $this->fieldType == 'string' ):
                    return Str::camel($rule . 'StringRuleMessage');
                case ( is_array($value) || $this->fieldType == 'array' ):
                    return Str::camel($rule . 'ArrayRuleMessage');
                default:
                    return NULL;
            }
        }

        return Str::camel($rule . 'RuleMessage');
    }

    /**
     * Return field value.
     *
     * @note This function use not raw field name.
     *
     * @param string $fieldName
     * @param null   $default
     *
     * @return array|\ArrayAccess|mixed
     */
    protected function getFieldValue(string $fieldName, $default = NULL)
    {
        return Arr::get($this->validated, $fieldName, $default);
    }

    /**
     * Return field value by raw name.
     *
     * @param string $fieldName
     * @param null   $default
     *
     * @return array|\ArrayAccess|mixed
     */
    protected function getFieldValueByRawName(string $fieldName, $default = NULL)
    {
        return Arr::get($this->validated, $this->getRawFieldName($fieldName), $default);
    }

    /**
     * Retrun message rule.
     *
     * @param $rule
     * @param $parameters
     *
     * @return string
     */
    protected function getRuleMessage($rule, $parameters) : string
    {
        $translations = $this->getRuleMessagesArray();

        $key = $this->getTranslationKey($rule, $parameters);

        // Optimize messages workflow.
        if ( isset($this->translatedRuleMessages[ $key ]) ) {
            return $this->translatedRuleMessages[ $key ];
        }

        // Get messages rule message.
        if ( isset($translations[ $rule ]) ) {
            return $this->translatedRuleMessages[ $key ] = $this->getKeyTranslation(
                $translations[ $rule ],
                $parameters[ 0 ] ?? [], // replaces array.
                false,
                2,
                $parameters[ 1 ] ?? [] // locale
            );
        }

        return __("validation.$rule",
            is_array($parameters) && isset($parameters[ 0 ]) ? $parameters[ 0 ] : [],
            is_array($parameters) && isset($parameters[ 1 ]) ? $parameters[ 1 ] : NULL,
        );
    }

    /**
     * Return translation key.
     *
     * @param       $rule
     * @param array $parameters
     *
     * @return string
     */
    protected function getTranslationKey($rule, array $parameters) : string
    {
        return md5(
            json_encode(
                array_merge($parameters, [
                    'rule' => $rule,
                ])
            )
        );
    }

    /**
     * Return array of
     *
     * @return Closure[]
     */
    protected function getRuleMessagesArray() : array
    {
        // Cache.
        if ( ! empty($this->possibleMessages) ) {
            return $this->possibleMessages;
        }

        $this->possibleMessages = config('core-translations.validator_messages');

        return $this->possibleMessages;
    }

    /**
     * Return translated field name alias.
     *
     * @param string $fieldName
     *
     * @return string|null
     */
    protected function getFieldNameAlias(string $fieldName) : ?string
    {
        if ( isset($this->translatedFieldAliases[ $fieldName ]) ) {
            return $this->translatedFieldAliases[ $fieldName ];
        }

        // Get raw field name.
        $rawFieldName = $this->getRawFieldName($fieldName);

        $fieldAliasTransKey = $this->getFieldNameAliases()[ $rawFieldName ] ?? false;

        if ( is_string($fieldAliasTransKey) && Str::contains($fieldAliasTransKey, '|') ) {
            // 0 - means get first plural
            // 1 - means get second plural. For example: image|images, if field is 'image_ids', then we should get 'images' plural.
            // If field is 'image_ids.*', then we should get first plural as 'image', cuz we validating single image.
            $value = $this->getKeyTranslation($fieldAliasTransKey, [], true, Str::endsWith($fieldName, '.*') ? 0 : 1);
        } elseif ( is_string($fieldAliasTransKey) ) {
            $value = $this->getKeyTranslation($fieldAliasTransKey);
        } else {
            $value = $this->getKeyTranslation(preg_replace('/\_/', ' ', $rawFieldName));
        }

        return $this->translatedFieldAliases[ $fieldName ] = $value;
    }

    /**
     * Return raw field name.
     *
     * @param string $fieldName
     *
     * @return string
     */
    protected function getRawFieldName(string $fieldName) : string
    {
        if(isset($this->getFieldNameAliases()[$fieldName])){
            return $fieldName;
        }

        $parts = explode('.*', $fieldName);

        return Str::contains($fieldName, '.*.') ? '_' . ltrim(last($parts), '.') : $parts[ 0 ];
    }

    /**
     * @return array
     */
    protected function getFieldNameAliases() : array
    {
        // Cache.
        if ( ! empty($this->possibleFieldAliases) ) {
            return $this->possibleFieldAliases;
        }

        $this->possibleFieldAliases = config('core-translations.field_aliases');

        return $this->possibleFieldAliases;
    }

    /**
     * Return translation callback.
     *
     * @param       $key
     * @param bool  $choice
     *
     * @return string
     */
    protected function getKeyTranslation($key, $replaces = [], $choice = false, $number = 2, $locale = NULL) : string
    {
        if ( ! $choice ) {
            return __($key, array_merge([
                'url_example' => config('app.url'),
                'app_name'    => config('app.name'),
            ], $replaces), $locale);
        }

        return trans_choice($key, $number, array_merge([
            'url_example' => config('app.url'),
            'app_name'    => config('app.name'),
        ], $replaces), $locale);
    }

}
