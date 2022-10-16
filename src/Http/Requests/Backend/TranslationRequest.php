<?php

namespace AttractCores\LaravelCoreTranslation\Http\Requests\Backend;

use AttractCores\LaravelCoreTranslation\Http\Requests\TranslatedFormRequest;
use Illuminate\Validation\Rule;

/**
 * Class TranslationRequest
 *
 * @package AttractCores\LaravelCoreTranslation\Http\Requests
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class TranslationRequest extends TranslatedFormRequest
{

    /**
     * Authorize current actions.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->actions = $this->getActions();

        return parent::authorize();
    }

    /**
     * Return possible request actions.
     *
     * @return array[]
     */
    protected function getActions() : array
    {
        $backendRole = config('core-translations.roles.backend');

        return [
            'get'     => [
                'methods'    => [ 'GET' ],
                'permission' => $backendRole,
            ],
            'post'    => [
                'methods'    => [ 'POST' ],
                'permission' => $backendRole,
            ],
            'put'     => [
                'methods'    => [ 'PUT', 'PATCH' ],
                'permission' => $backendRole,
            ],
            'destroy' => [
                'methods'    => [ 'DELETE' ],
                'permission' => $backendRole,
            ],
        ];
    }

    /**
     * Return PUT action rules.
     *
     * @return array
     */
    public function putAction()
    {
        return $this->rulesArray();
    }

    /**
     * Return POST action rules.
     *
     * @return array
     */
    public function postAction()
    {
        return $this->rulesArray();
    }

    /**
     * Return rules array.
     *
     * @return array
     */
    public function rulesArray()
    {
        $translation = $this->routeValue('translation');

        return $this->addTranslationRules([
            'group'             => [ 'required', 'string' ],
            'key'               => [
                'required', 'string', $translation ?
                    Rule::unique('translations', 'key')
                        ->where('group', $this->group)
                        ->ignore($translation, 'id') :
                    Rule::unique('translations', 'key')
                        ->where('group', $this->group)
            ],
            'translatable_id'   => [ 'nullable', 'sometimes', 'string' ],
            'translatable_type' => [ 'nullable', 'sometimes', 'string' ],
        ], [
            'text' => [ 'nullable', 'required', 'string' ],
        ]);
    }

}