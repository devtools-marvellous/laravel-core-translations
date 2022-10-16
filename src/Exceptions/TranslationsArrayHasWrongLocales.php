<?php

namespace AttractCores\LaravelCoreTranslation\Exceptions;

use Exception;

/**
 * Class TranslationsArrayHasWrongLocales
 *
 * @package ${NAMESPACE}
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class TranslationsArrayHasWrongLocales extends Exception
{

    /**
     * @param string $fieldName
     * @param array  $appLocales
     * @param array  $givenTranslations
     *
     * @return static
     */
    public static function invalidLocalizedTranslations(string $fieldName, array $appLocales, array $givenTranslations): self
    {
        return new static(sprintf('Your translations for `%s` field contain ambiguous locales. Application possible locales: ' .
        '`%s`, translations that given for saving: `%s`.', $fieldName, implode(', ', $appLocales,), json_encode($givenTranslations)));
    }
}