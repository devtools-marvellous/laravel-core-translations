<?php

namespace Tests\Feature\CRUD\Translations;

use AttractCores\LaravelCoreTestBench\CRUDOperationTestCase;
use AttractCores\LaravelCoreTestBench\ServerResponseAssertions;
use AttractCores\LaravelCoreTranslation\Models\Translation;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\Feature\CRUD\CRUDTestCase;

/**
 * Class DeleteTranslationsTest
 *
 * @package Tests\Unit
 * Date: 03.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class DeleteTranslationsTest extends CRUDTestCase
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
        return "Update Translations On Backend";
    }

    /**
     * Return test data for 0 assertions
     *
     * @return array
     */
    public function get0TestData()
    {
        $translation = $this->createTranslation('api', 'test', [ 'en test example' ]);

        return [ // 0
            'name'        => 'Admin can create translation',
            'route'       => 'backend.v1.translations.destroy',
            'params'      => [ 'translation' => $translation->getKey() ],
            'request'     => [],
            'method'      => 'DELETE',
            'status'      => 200,
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
        $this->assertEmpty($data);
        $this->assertNull(Translation::find($parameters['params']['translation']));
        $response->assertHeader(Str::ucfirst(Str::camel(config('core-translations.core_prefix') . 'Locale')), 'en');
        $response->assertHeader(Str::ucfirst(Str::camel(config('core-translations.core_prefix') .
                                                        'TranslationLastUpdated')), NULL);
    }

}

