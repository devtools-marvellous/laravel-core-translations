<?php

namespace Tests\Feature\Translations;

use AttractCores\LaravelCoreTranslation\Contracts\HasTranslations;
use AttractCores\LaravelCoreTranslation\Extensions\InteractsWithTranslations;
use AttractCores\LaravelCoreTranslation\Models\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

/**
 * Class AnyModelCanBeTranslatedTest
 *
 * @package Tests\Unit
 * Date: 03.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class AnyModelCanBeTranslatedTest extends TestCase
{

    /** @test */
    public function it_save_translatable_model_correctly()
    {
        Translation::query()->truncate();
        config([ 'core-translations.locales' => [ 'en', 'nl' ] ]);
        request()->merge([
            'name' => $requestTranslations = ['en' => 'EN name', 'nl' => 'NL name'],
        ]);
        $newModel = new TranslatableModel();

        $newModel->save();

        $this->assertEquals('EN name', $newModel->name);
        $this->assertCount(1, $translations = Translation::get());
        $this->assertEquals($requestTranslations, $translations->first()->text);

        request()->merge([
            'name' => $requestTranslations = ['en' => 'EN name changed', 'nl' => 'NL name'],
            'description' => 'asd'
        ]);

        $newModel->save();

        $this->assertEquals('EN name changed', $newModel->name);
        $this->assertCount(1, $translations = Translation::get());
        $this->assertEquals($requestTranslations, $translations->first()->text);
        $this->assertEquals('EN name changed', $newModel->getTranslationByField('name'));
        app()->setLocale('nl');
        $this->assertEquals('NL name', $newModel->getTranslationByField('name'));

        $newModel->delete();

        $this->assertEquals(0, $translations = Translation::count());
    }
}

class TranslatableModel extends Model implements HasTranslations
{
    use InteractsWithTranslations;

    protected $fillable = ['name'];

    public function translatableFields() : array
    {
       return ['name'];
    }

    protected function performInsert(Builder $query)
    {
        $this->setAttribute($this->getKeyName(), 1);

        $this->exists = true;

        $this->wasRecentlyCreated = true;

        return true;
    }

    protected function performUpdate(Builder $query)
    {
        $this->exists = true;

        $this->wasRecentlyCreated = false;

        return true;
    }

    protected function performDeleteOnModel()
    {
        $this->exists = false;
    }

}

