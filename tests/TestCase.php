<?php

namespace Tests;

use Amondar\Sextant\SextantServiceProvider;
use AttractCores\LaravelCoreClasses\CoreControllerServiceProvider;
use AttractCores\LaravelCoreTestBench\PHPUnitConsole;
use AttractCores\LaravelCoreTranslation\CoreTranslation;
use AttractCores\LaravelCoreTranslation\CoreTranslationServiceProvider;
use AttractCores\LaravelCoreTranslation\Testing\TranslationTesting;
use CreateTranslationsTable;
use Database\Seeders\CoreAuthPackageMessagesSeeder;
use Database\Seeders\CoreAuthPwdResetEmailMessagesSeeder;
use Database\Seeders\CoreAuthVerificationEmailMessagesSeeder;
use Database\Seeders\CoreKitPackageMessagesSeeder;
use Database\Seeders\CoreMediaPackageMessagesSeeder;
use Database\Seeders\CoreVerificationBrokerPackageMessagesSeeder;
use Database\Seeders\ValidationGeneratorTranslationsSeeder;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Class TestCase
 *
 * @package Tests
 * Date: 03.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class TestCase extends Orchestra
{
    use TranslationTesting, PHPUnitConsole;

    public function setUp() : void
    {
        parent::setUp();

        $this->runConsoleOutput();

        Artisan::call('migrate');

        include_once __DIR__ . '/../database/migrations/create_translations_table.php';

        ( new CreateTranslationsTable() )->up();

        Artisan::call('db:seed',
            [ '--class' => ValidationGeneratorTranslationsSeeder::class ]);

        Artisan::call('db:seed',
            [ '--class' => CoreMediaPackageMessagesSeeder::class ]);

        Artisan::call('db:seed',
            [ '--class' => CoreVerificationBrokerPackageMessagesSeeder::class ]);

        Artisan::call('db:seed',
            [ '--class' => CoreAuthPackageMessagesSeeder::class ]);

        Artisan::call('db:seed',
            [ '--class' => CoreKitPackageMessagesSeeder::class ]);

        Artisan::call('db:seed',
            [ '--class' => CoreAuthPwdResetEmailMessagesSeeder::class ]);

        Artisan::call('db:seed',
            [ '--class' => CoreAuthVerificationEmailMessagesSeeder::class ]);

        config([
            'core-translations.roles' => [
                'api'     => 'default',
                'backend' => 'default',
            ],
        ]);

        CoreTranslation::enableRoutes([ 'api' ], [ 'api' ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SextantServiceProvider::class,
            CoreControllerServiceProvider::class,
            CoreTranslationServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app[ 'path.lang' ] = $this->getResourcesDirectory('lang');

        $app[ 'config' ]->set('database.default', 'sqlite');
        $app[ 'config' ]->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    public function getResourcesDirectory(string $path) : string
    {
        return __DIR__ . "/resources/{$path}";
    }

    /**
     * Return has failed indication as string.
     *
     * @return string
     */
    protected function hasFailedAsString()
    {
        return $this->hasFailed() ? 'true' : 'false';
    }

    /**
     * Return reverted has failed indication as string.
     *
     * @return string
     */
    protected function isSucceededAsString()
    {
        return ! $this->hasFailed() ? 'true' : 'false';
    }

    protected function freshRequest()
    {
        $this->flushHeaders();
        $this->flushSession();
    }

}