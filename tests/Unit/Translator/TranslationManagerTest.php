<?php

namespace Tests\Unit\Translator;

use AttractCores\LaravelCoreTranslation\Contracts\TranslationLoader;
use AttractCores\LaravelCoreTranslation\Loaders\DatabaseLoader;
use Tests\TestCase;

/**
 * Class TranslationManagerTest
 *
 * @package Tests\Unit
 * Date: 03.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class TranslationManagerTest extends TestCase
{

    /** @test */
    public function it_will_not_use_database_translations_if_the_provider_is_not_configured()
    {
        config([ "core-translations.loaders" => []]);

        $this->assertEquals('group.key', trans('group.key'));
    }

    /** @test */
    public function it_will_merge_translation_from_all_providers()
    {
        config([ "core-translations.loaders" => [
            DatabaseLoader::class,
            DummyLoader::class,
        ]]);

        $this->createTranslation('database', 'key', [ 'en' => 'db' ]);


        $this->assertEquals('db', trans('db-trans::database.key'));
        $this->assertEquals('db-trans::database.key2', trans('db-trans::database.key2'));
        $this->assertEquals('this is dummy', trans('my::dummy.dummy'));
        $this->assertEquals('asd', trans('my::dummy.dummy2'));
        $this->assertEquals('asd', trans('dummy.dummy2'));
    }

}

class DummyLoader implements TranslationLoader
{

    public function loadTranslations(string $locale, string $group, string $namespace = NULL) : array
    {
        if ( in_array($namespace, $this->getNamespaces()) ) {
            return [ 'dummy' => 'this is dummy', 'dummy2' => 'asd' ];
        }

        return [];
    }

    /**
     * Return valid namespaces.
     *
     * @return string[]
     */
    public function getNamespaces() : array
    {
        return [ 'my', '*' ];
    }

}