<?php

namespace Database\Seeders;

use AttractCores\LaravelCoreTranslation\Database\Seeders\CorePackagesTranslationsSeeder;

/**
 * Class CoreKitPackageMessagesSeeder
 *
 * @package Database\Seeders
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class CoreKitPackageMessagesSeeder extends CorePackagesTranslationsSeeder
{

    /**
     * Return messages array.
     *
     * @return array
     */
    protected function getMessages() : array
    {
        return [
            "Your email address is not verified.",
            "You should specify at least one role.",
            "This role does not exist, or you are trying to use deprecated role for your access.",
            "Slug field is required.",
            "Slug should be unique.",
            "Role name is required.",
            "You can't create a role without permissions.",
            "Given permission is not exists in our db.",
            "Name field is required.",
            "Name length should be less than 255 chars.",
            "Name should be unique.",
            "Is active flag is required.",
            "Flag value should be boolean.",
        ];
    }

}