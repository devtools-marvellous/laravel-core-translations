<?php

namespace AttractCores\LaravelCoreTranslation\Http\Requests\Api;


use AttractCores\LaravelCoreTranslation\Http\Requests\Backend\TranslationRequest as BackendTranslationRequest;

/**
 * Class TranslationRequest
 *
 * @package AttractCores\LaravelCoreTranslation\Http\Requests
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class TranslationRequest extends BackendTranslationRequest
{

    /**
     * Return possible request actions.
     *
     * @return array[]
     */
    protected function getActions() : array
    {
        $apiRole = config('core-translations.roles.api');

        return [
            'get'     => [
                'methods'    => [ 'GET' ],
                'permission' => $apiRole,
            ]
        ];
    }

}