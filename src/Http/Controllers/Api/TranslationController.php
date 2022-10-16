<?php

namespace AttractCores\LaravelCoreTranslation\Http\Controllers\Api;

use Amondar\RestActions\Actions\IndexAction;
use AttractCores\LaravelCoreClasses\CoreController;
use AttractCores\LaravelCoreTranslation\Http\Requests\Api\TranslationRequest;
use AttractCores\LaravelCoreTranslation\Http\Resources\TranslationResource;
use AttractCores\LaravelCoreTranslation\Repositories\TranslationRepository;
use Illuminate\Http\Request;

/**
 * Class TranslationController
 *
 * @package AttractCores\LaravelCoreTranslation\Http\Controllers\Api
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class TranslationController extends CoreController
{

    use IndexAction;

    protected static $PROTECTED_LIMIT = 1000;

    /**
     * Possible actions.
     *
     * @var array
     */
    protected $actions = [
        'index'   => [
            'onlyAjax'    => true,
            'request'     => TranslationRequest::class,
            'transformer' => TranslationResource::class,
        ]
    ];

    /**
     * TranslationController constructor.
     *
     * @param \AttractCores\LaravelCoreTranslation\Repositories\TranslationRepository $repository
     */
    public function __construct(TranslationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Base query filter to return only api translations.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getBaseFilterQuery(Request $request)
    {
        return $this->restMakeModel()->newQuery()->where('group', 'api');
    }

}