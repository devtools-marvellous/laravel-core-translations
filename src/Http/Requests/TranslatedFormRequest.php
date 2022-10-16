<?php

namespace AttractCores\LaravelCoreTranslation\Http\Requests;

use AttractCores\LaravelCoreClasses\CoreFormRequest;
use AttractCores\LaravelCoreTranslation\Extensions\FormInteractionsWithTranslations;

/**
 * Class TranslatedFormRequest
 *
 * @package ${NAMESPACE}
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class TranslatedFormRequest extends CoreFormRequest
{

    use FormInteractionsWithTranslations;
}