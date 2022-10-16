<?php

namespace AttractCores\LaravelCoreTranslation;

use AttractCores\LaravelCoreAuth\Resolvers\CorePermissionContract;
use Illuminate\Support\Facades\Route;

/**
 * Class CoreTranslations
 *
 * @package AttractCores\LaravelCoreTranslation
 * Date: 06.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class CoreTranslation
{

    /**
     * Add core translations api routes
     *
     * @param string[] $middlewares
     */
    public static function addApiRoutes(
        array $middlewares = [ 'api', 'auth-api-client' ]
    )
    {
        if ( config('core-translations.roles.api') != 'default' ) {
            $middlewares[] = 'can:' . config('core-translations.roles.api');
        }

        Route::prefix(config('kit-routes.api.v1.prefix', 'api/v1'))
             ->as(config('kit-routes.api.v1.name', 'api.v1.'))
             ->middleware($middlewares)
             ->namespace('\AttractCores\LaravelCoreTranslation\Http\Controllers')
             ->group(function () {
                 Route::apiResource('translations', 'Api\TranslationController')
                      ->only([ 'index' ])
                      ->middleware(['throttle:translations-api']);
             });
    }

    /**
     * Add core translations backend api routes
     *
     * @param string[] $middlewares
     */
    public static function addBackendApiRoutes(
        array $middlewares = [ 'api', 'auth:api', 'check-scopes:backend' ]
    )
    {
        if ( config('core-translations.roles.backend') != 'default' ) {
            $middlewares[] = 'can:' . config('core-translations.roles.backend');
        }

        Route::prefix(config('kit-routes.backend.v1.prefix', 'backend/v1'))
             ->as(config('kit-routes.backend.v1.name', 'backend.v1.'))
             ->middleware($middlewares)
             ->namespace('\AttractCores\LaravelCoreTranslation\Http\Controllers')
             ->group(function () {
                 Route::apiResource('translations', 'Backend\TranslationController');
             });
    }

    /**
     * Enable core translations routes.
     *
     * @param string[] $apiMiddlewares
     * @param string[] $backendApiMiddlewares
     */
    public static function enableRoutes(array $apiMiddlewares = [], array $backendApiMiddlewares = [])
    {
        if ( ! empty($apiMiddlewares) ) {
            static::addApiRoutes($apiMiddlewares);
        } else {
            static::addApiRoutes();
        }

        if ( ! empty($backendApiMiddlewares) ) {
            static::addBackendApiRoutes($backendApiMiddlewares);
        } else {
            static::addBackendApiRoutes();
        }
    }

}