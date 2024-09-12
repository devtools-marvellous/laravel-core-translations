<?php

namespace AttractCores\LaravelCoreTranslation;

use AttractCores\LaravelCoreTranslation\Database\Factories\TranslatedSimpleCrudFactory;
use AttractCores\LaravelCoreTranslation\Http\Middlewares\AddTranslationDataToResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Translation\TranslationServiceProvider as IlluminateTranslationServiceProvider;

class CoreTranslationServiceProvider extends IlluminateTranslationServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/core-translations.php', 'core-translations');
        parent::register();
    }

    public function boot()
    {
        $this->app->make(Kernel::class)
            ->appendMiddlewareToGroup('web', AddTranslationDataToResponse::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/core-translations.php' => config_path('core-translations.php'),
            ], 'core-translations-config');

            if (!class_exists('CreateTranslationsTable')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_translations_table.php' => database_path('migrations/' . $timestamp . '_create_translations_table.php'),
                    __DIR__ . '/../database/seeders/' => database_path('seeders/'),
                ], 'core-translations-migrations');
            }

            $this->publishes([
                __DIR__ . '/../database/seeders/' => database_path('seeders/'),
            ], 'core-translations-packages-translations-seeders');

            $this->publishes([
                __DIR__ . '/../tests/Feature/CRUD/' => base_path('tests/Feature/CRUD/'),
            ], 'core-translations-crud-tests');

            $this->publishes([
                __DIR__ . '/../docs' => base_path('../docs/'),
            ], 'core-translations-docs');

            if ($this->app->runningUnitTests()) {
                TranslatedSimpleCrudFactory::makeLocalesFakers();
            }
        }

        RateLimiter::for('translations-api', function (Request $request) {
            return Limit::perMinute(2)->by(optional($request->user())->id ?: $request->bearerToken());
        });
    }

    protected function registerLoader()
    {
        if (config('core-translations.enable_db_translations')) {
            $this->app->singleton('translation.loader', function ($app) {
                $class = config('core-translations.manager');
                return new $class($app['files'], $app['path.lang']);
            });
        } else {
            parent::registerLoader();
        }

        $this->app->bind('translation.locales', function ($app) {
            return config('core-translations.locales');
        });
    }
}