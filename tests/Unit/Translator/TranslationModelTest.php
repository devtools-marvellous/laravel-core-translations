<?php

namespace Tests\Unit\Translator;

use AttractCores\LaravelCoreTranslation\Models\Translation;
use Illuminate\Http\Resources\MissingValue;
use Tests\TestCase;

/**
 * Class TranslationModelTest
 *
 * @package Tests\Unit
 * Date: 03.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class TranslationModelTest extends TestCase
{
    /** @test */
    public function it_can_get_a_translation()
    {
        $translationModel = $this->createTranslation('group', 'new', [ 'en' => 'english', 'nl' => 'nederlands' ]);

        $this->assertEquals('english', $translationModel->getTranslation('en'));
        $this->assertEquals('nederlands', $translationModel->getTranslation('nl'));
    }

    /** @test */
    public function it_can_set_a_translation()
    {
        $translationModel = $this->createTranslation('group', 'new', [ 'en' => 'english' ]);

        $translationModel->setTranslation('nl', 'nederlands');

        $this->assertEquals('english', $translationModel->getTranslation('en'));
        $this->assertEquals('nederlands', $translationModel->getTranslation('nl'));
    }

    /** @test */
    public function it_can_set_a_translation_on_a_fresh_model()
    {
        $translationModel = new Translation();

        $translationModel->setTranslation('nl', 'nederlands');

        $this->assertEquals('nederlands', $translationModel->getTranslation('nl'));
    }

    /** @test */
    public function it_doesnt_show_error_when_getting_nonexistent_translation()
    {
        $translationModel = $this->createTranslation('group', 'new', [ 'nl' => 'nederlands' ]);
        $this->assertSame(NULL, $translationModel->getTranslation('en'));
    }

    /** @test */
    public function get_fallback_locale_if_doesnt_exists()
    {
        $translationModel = $this->createTranslation('group', 'new', [ 'en' => 'English' ]);
        $this->assertEquals('English', $translationModel->getTranslation('es'));
    }

}

