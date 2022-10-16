<?php

namespace Tests\Feature\CRUD\Translations;

use AttractCores\LaravelCoreTestBench\CRUDOperationTestCase;
use AttractCores\LaravelCoreTestBench\ServerResponseAssertions;
use AttractCores\LaravelCoreTranslation\Models\Translation;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\Feature\CRUD\CRUDTestCase;

/**
 * Class StoreTranslationsTest
 *
 * @package Tests\Unit
 * Date: 03.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class StoreTranslationsTest extends CRUDTestCase
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
        return "Store Translations On Backend";
    }

    /**
     * Return test data for 0 assertions
     *
     * @return array
     */
    public function get0TestData()
    {
        config([ 'core-translations.locales' => [ 'en' ] ]);
        return [ // 0
            'name'        => 'Admin can create translation',
            'route'       => 'backend.v1.translations.store',
            'params'      => [],
            'request'     => [
                'group' => 'api',
                'key'   => 'my.test',
                'text'  => [
                    'en' => 'en test text',
                    'nl' => 'nl test text',
                ],
            ],
            'method'      => 'POST',
            'status'      => 201,
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
        $this->assertEquals($parameters[ 'request' ][ 'group' ], $data[ 'group' ]);
        $this->assertEquals($parameters[ 'request' ][ 'key' ], $data[ 'key' ]);
        $this->assertNotEquals($parameters[ 'request' ][ 'text' ], $data[ 'text' ]);
        $this->assertEquals([ 'en' => 'en test text' ], $data[ 'text' ]);
        $response->assertHeader(Str::ucfirst(Str::camel(config('core-translations.core_prefix') . 'Locale')), 'en');
        $t = Translation::latest('updated_at')->first();
        $response->assertHeader(Str::ucfirst(Str::camel(config('core-translations.core_prefix') .
                                                        'TranslationLastUpdated')), $t->updated_at->toRfc822String());
    }

    /**
     * Return test data for 1 assertions
     *
     * @return array
     */
    public function get1TestData()
    {
        $this->createTranslation('api', 'test', ['en' => 'test text in en.']);

        return [ // 0
            'name'        => 'Admin can create translation',
            'route'       => 'backend.v1.translations.store',
            'params'      => [],
            'request'     => [
                'group' => 'api',
                'key'   => 'test',
                'text'  => [
                    'en' => 'test text in en.',
                    'nl' => 'nl test text',
                ],
            ],
            'method'      => 'POST',
            'status'      => 422,
            'withoutAuth' => $this->withoutAuth,
        ];
    }

}

