<?php

namespace Database\Seeders;

use AttractCores\LaravelCoreTranslation\Database\Seeders\CorePackagesTranslationsSeeder;

/**
 * Class CoreAuthPackageMessagesSeeder
 *
 * @package Database\Seeders
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class CoreAuthPackageMessagesSeeder extends CorePackagesTranslationsSeeder
{

    /**
     * Return messages array.
     *
     * @return array
     */
    protected function getMessages() : array
    {
        return [
            "We have been send a new mail to you with verification instructions.",
            "Token field is required.",
            "Email is required.",
            "Please, enter correct email.",
            "User with given email does not exist :App_name.",
            "Please, choose side for reset link generation.",
            "Side parameter can only be equals to one of these items: frontend, backend.",
            "Refresh token is required.",
            "Password is required.",
            "Password  and confirmation field values should be equals to each other.",
            "Password should be :min characters in length.",
            "Oops, this email is not registered with :App_name.",
            "Email is already registered with :App_name.",
        ];
    }

}
