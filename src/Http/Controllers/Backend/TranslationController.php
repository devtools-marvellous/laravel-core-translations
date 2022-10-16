<?php

namespace AttractCores\LaravelCoreTranslation\Http\Controllers\Backend;

use Amondar\RestActions\Actions\DestroyAction;
use Amondar\RestActions\Actions\IndexAction;
use Amondar\RestActions\Actions\ShowAction;
use Amondar\RestActions\Actions\StoreAction;
use Amondar\RestActions\Actions\UpdateAction;
use AttractCores\LaravelCoreClasses\CoreController;
use AttractCores\LaravelCoreTranslation\Http\Requests\Backend\TranslationRequest;
use AttractCores\LaravelCoreTranslation\Http\Resources\TranslationResource;
use AttractCores\LaravelCoreTranslation\Repositories\TranslationRepository;

/**
 * Class TranslationController
 *
 * @package AttractCores\LaravelCoreTranslation\Http\Controllers\Backend
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class TranslationController extends CoreController
{

    use IndexAction, ShowAction, StoreAction, UpdateAction, DestroyAction;

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
        ],
        'show'    => [
            'onlyAjax'    => true,
            'request'     => TranslationRequest::class,
            'transformer' => TranslationResource::class,
        ],
        'store'   => [
            'request'     => TranslationRequest::class,
            'transformer' => TranslationResource::class,
            'repository'  => 'storeOrUpdate',
        ],
        'update'  => [
            'request'     => TranslationRequest::class,
            'transformer' => TranslationResource::class,
            'repository'  => 'storeOrUpdate',
        ],
        'destroy' => [
            'request' => TranslationRequest::class,
        ],
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
}