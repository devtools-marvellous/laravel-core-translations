<?php

namespace AttractCores\LaravelCoreTranslation\Http\Requests\Backend;

use AttractCores\LaravelCoreTranslation\Http\Requests\TranslatedFormRequest;
use Illuminate\Validation\Rule;

/**
 * Class TranslatedSimpleCrudRequest
 *
 * @package AttractCores\LaravelCoreTranslation\Http\Requests\Backend
 * Date: 09.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class TranslatedSimpleCrudRequest extends TranslatedFormRequest
{

    /**
     * Contain route key name for request model.
     *
     * @var string|null
     */
    protected ?string $routeModelKeyName;

    /**
     * Contain table name for inline db validations.
     *
     * @var string|null
     */
    protected ?string $tableName;

    /**
     * Authorize request actions.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->actions = $this->getRequestActions();

        return parent::authorize();
    }

    /**
     * Return post rules.
     *
     * @return array
     */
    public function postAction()
    {
        return $this->rulesArray();
    }

    /**
     * Return put rules.
     *
     * @return array
     */
    public function putAction()
    {
        return $this->rulesArray();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rulesArray()
    {
        $modelID = $this->routeValue($this->routeModelKeyName);
        $fallbackLocale = config('app.fallback_locale');

        return $this->addTranslationRules([
            'is_active' => [ 'required', 'boolean' ],
        ], [
            'name' => [
                [ 'required', 'string', 'max:255' ],
                $fallbackLocale => $modelID ?
                    [ 'required', 'string', 'max:255', Rule::unique($this->tableName, 'name')->ignore($modelID) ] :
                    [ 'required', 'string', 'max:255', Rule::unique($this->tableName, 'name') ],
            ],
        ]);
    }

    /**
     * Return possible request actions.
     *
     * @return array[]
     */
    protected function getRequestActions()
    {
        $backendRole = config('core-translations.roles.backend');

        return [
            'get'  => [
                'methods'    => [ 'GET' ],
                'permission' => $backendRole,
            ],
            'post' => [
                'methods'    => [ 'POST' ],
                'permission' => $backendRole,
            ],
            'put'  => [
                'methods'    => [ 'PATCH', 'PUT' ],
                'permission' => $backendRole,
            ],
        ];
    }

}
