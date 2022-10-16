<?php

namespace AttractCores\LaravelCoreTranslation\Http\Middlewares;

use AttractCores\LaravelCoreTranslation\Models\Translation;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Class AddTranslationDataToResponse
 *
 * @version 1.0.0
 * @date    2021-03-05
 * @author  Yure Nery <yurenery@gmail.com>
 */
class AddTranslationDataToResponse
{

    /**
     * Closure for fallback locale resolver.
     *
     * @var \Closure|NULL
     */
    public static $fallbackLocaleResolver;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param null                     $guard
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $guard = NULL)
    {
        // Read locale cookie and set app locale.
        $localeHeaderName = $this->getPrefixedHeader('locale');

        $locale = $request->header($localeHeaderName, $this->resolveFallbackLocale());

        if ( in_array($locale, app('translation.locales')) ) {
            app()->setLocale($locale);
        }

        // Process request.
        /** @var \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse $response */
        $response = $next($request);

        $response->headers->set($localeHeaderName, app()->getLocale());

        if ( config('core-translations.enable_db_translations') && $dateTime = $this->getLastTranslationUpdatedAt() ) {
            $response->headers->set($this->getPrefixedHeader('TranslationLastUpdated'), $dateTime);
        }

        return $response;
    }

    /**
     * Resolve fallback locale.
     *
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    protected function resolveFallbackLocale()
    {
        return static::$fallbackLocaleResolver ? call_user_func(static::$fallbackLocaleResolver) :
            config('app.fallback_locale');
    }

    /**
     * Return cookies prefix.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPrefixedCookies(string $name)
    {
        return Str::snake(config('core-translations.core_prefix')) . '_' . Str::snake($name);
    }

    /**
     * Return cookies prefix.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPrefixedHeader(string $name)
    {
        return Str::ucfirst(Str::camel(config('core-translations.core_prefix') . '_' . $name));
    }

    /**
     * Return latest translation updated at date.
     *
     * @return mixed
     */
    protected function getLastTranslationUpdatedAt()
    {
        return Cache::get('core-translations.api-last-updated', function () {
            $latestUpdatedForApi = Translation::latest('updated_at')->where('group', 'api')->first();

            Cache::forever('core-translations.api-last-updated', $result = (
            $latestUpdatedForApi ? $latestUpdatedForApi->updated_at->toRfc822String() : NULL
            ));

            return $result;
        });
    }

}