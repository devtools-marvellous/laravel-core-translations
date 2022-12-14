<?php

namespace Tests\Unit\Translator;

use Illuminate\Support\Arr;
use Tests\TestCase;

/**
 * Class TransTest
 *
 * @package Tests\Unit
 * Date: 03.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class TransTest extends TestCase
{
    protected $nested = [
        'bool' => [
            1 => 'Yes',
            0 => 'No',
        ],
    ];

    /** @test */
    public function it_can_get_translations_for_language_files()
    {
        $this->assertEquals('en value', trans('file.key'));
        $this->assertEquals('page not found', trans('file.404.title'));
        $this->assertEquals('This page does not exists', trans('file.404.message'));
    }

    /** @test */
    public function it_can_get_translations_for_language_files_for_the_current_locale()
    {
        app()->setLocale('nl');

        $this->assertEquals('nl value', trans('file.key'));
        $this->assertEquals('pagina niet gevonden', trans('file.404.title'));
        $this->assertEquals('Deze pagina bestaat niet', trans('file.404.message'));
    }

    /** @test */
    public function by_default_it_will_prefer_a_db_translation_over_a_file_translation()
    {
        $this->createTranslation('file', 'key', ['en' => 'en value from db']);
        $this->createTranslation('file', '404.title', ['en' => 'page not found from db']);

        $this->assertEquals('en value from db', trans('db-trans::file.key'));
        $this->assertEquals('page not found from db', trans('db-trans::file.404.title'));
        $this->assertEquals('This page does not exists', trans('file.404.message'));
    }

    /** @test */
    public function it_will_return_the_translation_string_if_max_nested_level_is_reached()
    {
        foreach (Arr::dot($this->nested) as $key => $text) {
            $this->createTranslation('nested', $key, ['en' => $text]);
        }

        $this->assertEquals($this->nested['bool'][1], trans('nested.bool.1'));
    }

    /** @test */
    public function it_will_return_the_dotted_translation_key_if_no_translation_found()
    {
        $notFoundKey = 'nested.bool.3';

        foreach (Arr::dot($this->nested) as $key => $text) {
            $this->createTranslation('nested', $key, ['en' => $text]);
        }

        $this->assertEquals($notFoundKey, trans($notFoundKey));
    }

    /** @test */
    public function it_will_default_to_fallback_if_locale_is_missing()
    {
        app()->setLocale('de');
        $this->createTranslation('missing_locale', 'key', ['en' => 'en value from db']);

        $this->assertEquals('en value from db', trans('db-trans::missing_locale.key'));
    }

}

