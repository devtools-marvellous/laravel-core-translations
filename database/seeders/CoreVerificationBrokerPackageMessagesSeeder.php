<?php

namespace Database\Seeders;

use AttractCores\LaravelCoreTranslation\Database\Seeders\CorePackagesTranslationsSeeder;

/**
 * Class CoreVerificationBrokerPackageMessagesSeeder
 *
 * @package Database\Seeders
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class CoreVerificationBrokerPackageMessagesSeeder extends CorePackagesTranslationsSeeder
{

    /**
     * Return messages array.
     *
     * @return array
     */
    protected function getMessages() : array
    {
        return [
            "We can't find a user with that e-mail address.",
            "Please, wait before retrying.",
            "We have e-mailed your verification link!",
            "We have e-mailed your password reset link!",
            "Your password has been reset!",
            "Account action verified!",
            "This verification token is invalid.",
        ];
    }

}