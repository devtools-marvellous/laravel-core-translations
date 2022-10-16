<?php

namespace AttractCores\LaravelCoreTranslation\Database\Seeders;

use AttractCores\LaravelCoreTranslation\Models\Translation;
use Illuminate\Database\Seeder;

/**
 * Class CorePackagesTranslationsSeeder
 *
 * @package AttractCores\LaravelCoreTranslation\Database\Seeders
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class CorePackagesTranslationsSeeder extends Seeder
{

    protected string $group = '*';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fallbackLocale = config('app.fallback_locale');

        // Seed validator aliases.
        foreach ( $this->getMessages() as $key => $translation ) {
            $group = $this->group;
            $key = is_string($key) ? $key : $translation;
            $text = [ $fallbackLocale => $translation ];

            Translation::query()->firstOrCreate(compact('group', 'key'), compact('group', 'key', 'text'));
        }
    }

    /**
     * Return messages array.
     *
     * @return array
     */
    protected function getMessages() : array
    {
        return [];
    }

}