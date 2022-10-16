<?php

namespace AttractCores\LaravelCoreTranslation\Repositories;

use AttractCores\LaravelCoreClasses\CoreRepository;
use AttractCores\LaravelCoreTranslation\Models\Translation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Class TranslationRepository
 *
 * @property Translation model
 *
 * @package AttractCores\LaravelCoreTranslation\Repositories
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class TranslationRepository extends CoreRepository
{


    /**
     * Return model class for repository.
     *
     * @return string
     */
    public function model()
    {
        return Translation::class;
    }

    /**
     * Store or update repository model.
     *
     * @param FormRequest|NULL $request
     * @param array|null       $validated
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function storeOrUpdate(?FormRequest $request, array $validated = NULL)
    {
        $request = $request ?? request();
        $validated = $validated ?? $request->validated();

        // Fill model.
        $this->model->fill($validated);

        // Call saving hook;
        $this->savingHook($request, $validated);

        // Save model.
        $this->model->save();

        $this->setModelRelations($request, $validated);

        // Call saved hook.
        $this->savedHook($request, $validated);

        return $this->model;
    }


    /**
     * Run saved hook. Run this hook after full model saving.
     *
     * @param Request $request
     * @param array   $validated
     */
    protected function savedHook(Request $request, array &$validated)
    {
        Cache::forget('core-translations.api-last-updated');
    }

    /**
     * Remove model and drop cache.
     *
     * @throws \Exception
     */
    public function destroy()
    {
        $this->model->delete();
        Cache::forget('core-translations.api-last-updated');
    }

}