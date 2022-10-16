<?php

namespace AttractCores\LaravelCoreTranslation\Extensions;

use AttractCores\LaravelCoreTranslation\Libraries\ValidationMessagesGenerator;

/**
 * Trait FormInteractionsWithTranslations
 *
 * @package AttractCores\LaravelCoreTranslation\Extensions
 * Date: 09.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
trait FormInteractionsWithTranslations
{

    protected function addTranslationRules(array $rules, array $fieldsRules) : array
    {
        $appLocales = app('translation.locales');

        foreach ( $fieldsRules as $fieldName => $fieldRules ) {

            foreach ( $appLocales as $locale ) {
                $rules[$this->getLocalizedFieldName($fieldName, $locale)] =
                    // Check if we have localized rules, else if zero indexed element is array of rules
                    // else grab fieldRules whole array as array of rules for each locale.
                    isset($fieldRules[$locale]) ? $fieldRules[$locale] : (
                    is_array($fieldRules[0]) ?  $fieldRules[0] : $fieldRules
                    );
            }
        }

        return $rules;
    }

    /**
     * Return field name with locale name for localization purposes.
     *
     * @param string $fieldName
     * @param string $locale
     *
     * @return string
     */
    protected function getLocalizedFieldName(string $fieldName, string $locale)
    {
        return $fieldName . '.' . $locale;
    }

    /**
     * Return messages
     *
     * @return array
     */
    public function messages()
    {
        $messagesGenerator = new ValidationMessagesGenerator($this->rules(), $this->all());

        return $messagesGenerator->generateMessages(parent::messages());
    }
}