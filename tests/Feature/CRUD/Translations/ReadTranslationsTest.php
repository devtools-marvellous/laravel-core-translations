<?php

namespace Tests\Feature\CRUD\Translations;

use AttractCores\LaravelCoreTestBench\CRUDOperationTestCase;
use AttractCores\LaravelCoreTestBench\ServerResponseAssertions;
use AttractCores\LaravelCoreTranslation\Models\Translation;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\Feature\CRUD\CRUDTestCase;

/**
 * Class ReadTranslationsTest
 *
 * @package Tests\Unit
 * Date: 03.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class ReadTranslationsTest extends CRUDTestCase
{

    use CRUDOperationTestCase, ServerResponseAssertions;

    protected bool $withoutAuth = true;

    /**
     * Return name of the class for testing.
     *
     * @return string
     */
    protected function getCRUDClassUnderTest()
    {
        return "Read Translations";
    }

    /**
     * Return test data for 0 assertions
     *
     * @return array
     */
    public function get0TestData()
    {
        return [ // 0
            'name'        => 'Admin can read translations',
            'route'       => 'backend.v1.translations.index',
            'params'      => [],
            'request'     => [],
            'method'      => 'GET',
            'withoutAuth' => $this->withoutAuth,
        ];
    }

    /**
     * Determine action assertions based on route.
     *
     * @param TestResponse $response
     * @param array        $parameters
     */
    protected function do0TestAssertions(TestResponse $response, array $parameters)
    {
        $content = $this->assertSuccessResponse($response);
        $data = $content[ 'data' ];
        // Check cache key for index method.
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('group', $data[ 0 ]);
        $response->assertHeader(Str::ucfirst(Str::camel(config('core-translations.core_prefix') . 'Locale')), 'en');
        $response->assertHeader(Str::ucfirst(Str::camel(config('core-translations.core_prefix') .
                                                        'TranslationLastUpdated')), NULL);
    }

    /**
     * Return test data for 1 assertions
     *
     * @return array
     */
    public function get1TestData()
    {
        $this->createTranslation('api', 'test', [ 'en' => 'Test EN api text.' ]);

        return [ // 1
            'name'        => 'Anybody can read translations for api',
            'route'       => 'api.v1.translations.index',
            'params'      => [],
            'request'     => [],
            'method'      => 'GET',
            'withoutAuth' => $this->withoutAuth,
        ];
    }

    /**
     * Determine action assertions based on route.
     *
     * @param TestResponse $response
     * @param array        $parameters
     */
    protected function do1TestAssertions(TestResponse $response, array $parameters)
    {
        $content = $this->assertSuccessResponse($response);
        $data = $content[ 'data' ];
        // Check cache key for index method.
        $this->assertNotEmpty($data);
        $this->assertCount(1, $data);
        $this->assertEquals('api', $data[ 0 ][ 'group' ]);
        $this->assertEquals('test', $data[ 0 ][ 'key' ]);
        $this->assertEquals([ 'en' => 'Test EN api text.' ], $data[ 0 ][ 'text' ]);
        $response->assertHeader(Str::ucfirst(Str::camel(config('core-translations.core_prefix') . 'Locale')), 'en');
        $t = Translation::latest('updated_at')->first();
        $response->assertHeader(Str::ucfirst(Str::camel(config('core-translations.core_prefix') .
                                                        'TranslationLastUpdated')), $t->updated_at->toRfc822String());
    }

    /**
     * Return test data for 2 assertions
     *
     * @return array
     */
    public function get2TestData()
    {
        config([ 'core-translations.locales' => [ 'en', 'nl' ] ]);
        $this->createTranslation('api', '404.trash', [
            'en' => 'file not found. it might be in trash.', 'nl' => 'Bestand niet gevonden. Het bestand is waarschijnlijk verwijderd.',
        ]);
        $this->createTranslation('api', '404.simple', [ 'en' => 'file not found', 'nl' => 'Bestand niet gevonden' ]);

        return [ // 2
            'name'        => 'Admin can search translations',
            'route'       => 'backend.v1.translations.index',
            'params'      => [],
            'request'     => [
                'scopes' => json_encode([
                    [ 'name' => 'searchByTranslationsInAnyLocale', 'parameters' => ['waarschijnlijk'] ],
                ]),
            ],
            'method'      => 'GET',
            'withoutAuth' => $this->withoutAuth,
        ];
    }

    /**
     * Determine action assertions based on route.
     *
     * @param TestResponse $response
     * @param array        $parameters
     */
    protected function do2TestAssertions(TestResponse $response, array $parameters)
    {
        $content = $this->assertSuccessResponse($response);
        $data = $content[ 'data' ];
        // Check cache key for index method.
        $this->assertNotEmpty($data);
        $this->assertCount(1, $data);
        $this->assertEquals('404.trash', $data[0]['key']);
    }

    /**
     * Return test data for 3 assertions
     *
     * @return array
     */
    public function get3TestData()
    {
        config([ 'core-translations.locales' => [ 'en', 'nl' ] ]);
        $this->createTranslation('api', '404.trash', [
            'en' => 'file not found. it might be in trash.', 'nl' => 'Bestand niet gevonden. Het bestand is waarschijnlijk verwijderd.',
        ]);
        $this->createTranslation('api', '404.simple', [ 'en' => 'file not found', 'nl' => 'Bestand niet gevonden' ]);

        return [ // 3
            'name'        => 'Admin can search translations',
            'route'       => 'backend.v1.translations.index',
            'params'      => [],
            'request'     => [
                'scopes' => json_encode([
                    [ 'name' => 'searchByTranslationsInAnyLocale', 'parameters' => ['bestand'] ],
                ]),
                'sort' => '-id'
            ],
            'method'      => 'GET',
            'withoutAuth' => $this->withoutAuth,
        ];
    }

    /**
     * Determine action assertions based on route.
     *
     * @param TestResponse $response
     * @param array        $parameters
     */
    protected function do3TestAssertions(TestResponse $response, array $parameters)
    {
        $content = $this->assertSuccessResponse($response);
        $data = $content[ 'data' ];
        // Check cache key for index method.
        $this->assertNotEmpty($data);
        $this->assertCount(2, $data);
        $this->assertEquals('404.simple', $data[0]['key']);
    }
}

