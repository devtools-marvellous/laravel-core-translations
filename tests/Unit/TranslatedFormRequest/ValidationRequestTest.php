<?php

namespace Tests\Unit\TranslatedFormRequest;

use AttractCores\LaravelCoreTranslation\Http\Requests\TranslatedFormRequest;
use AttractCores\LaravelCoreTranslation\TranslationLoaderManager;
use Tests\TestCase;

/**
 * Class ValidationRequestTest
 *
 * @package Tests\Unit
 * Date: 03.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class ValidationRequestTest extends TestCase
{
    protected ValidationRequest $requestOnTest;

    /**
     * Set up each test.
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->requestOnTest = ValidationRequest::capture();
    }

    /** @test */
    public function it_has_correct_validation_rules()
    {
        config([ 'core-translations.locales' => [ 'en', 'nl' ] ]);
        $rules = $this->requestOnTest->rules();
        $this->assertArrayHasKey('title', $rules);
        $this->assertArrayHasKey('name.en', $rules);
        $this->assertArrayHasKey('name.nl', $rules);
        $this->assertArrayHasKey('description.en', $rules);
        $this->assertArrayHasKey('description.nl', $rules);
        $this->assertEquals([ 'required', 'string' ], $rules[ 'name.en' ]);
        $this->assertEquals([ 'required', 'string' ], $rules[ 'description.en' ]);
        $this->assertEquals([ 'sometimes', 'nullable', 'string' ], $rules[ 'description.nl' ]);
    }

    /** @test */
    public function it_has_correct_validation_messages()
    {
        config([ 'core-translations.locales' => [ 'en', 'nl' ] ]);
        config([
            'core-translations.field_aliases' => [
                'name.en'        => 'name translation in english',
                'name.nl'        => 'name translation in nederland',
                'description.nl' => 'description translation in nederland|asd',
            ],
        ]);

        $this->requestOnTest->merge([
            'title' => 'asd',
            'name' => [
                'en' => 'en name asd',
                'nl' => 'nl name asd'
            ],
            'description' => [
                'en' => 'en description asd',
                'nl' => 'nl description asd'
            ],
        ]);

        $messages = $this->requestOnTest->messages();
        $this->assertCount(9, $messages);
        $this->assertArrayHasKey('title.required', $messages);
        $this->assertArrayHasKey('name.en.required', $messages);
        $this->assertArrayHasKey('description.nl.string', $messages);
        $this->assertEquals("Title is required.", $messages['title.required']);
        $this->assertEquals("Name translation in english is required.", $messages['name.en.required']);
        $this->assertEquals("Description translation in nederland must be a string.", $messages['description.nl.string']);
    }

}

class ValidationRequest extends TranslatedFormRequest
{
    public function rules()
    {
        return $this->addTranslationRules([
            'title' => [ 'required', 'string' ],
        ], [
            'name'        => [ 'required', 'string' ],
            'description' => [
                [ 'required', 'string' ],
                'nl' => [ 'sometimes', 'nullable', 'string' ],
            ],
        ]);
    }

}

