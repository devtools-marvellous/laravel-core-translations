<?php

namespace Database\Seeders;

use AttractCores\LaravelCoreTranslation\Database\Seeders\CorePackagesTranslationsSeeder;

/**
 * Class CoreAuthVerificationEmailMessagesSeeder
 *
 * @package Database\Seeders
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class CoreAuthVerificationEmailMessagesSeeder extends CorePackagesTranslationsSeeder
{

    /**
     * Return messages array.
     *
     * @return array
     */
    protected function getMessages() : array
    {
        return [
            'Email Address Verification Process',
            'Please click the button below to verify your email address.',
            'Verify Email Address',
            'If you did not create an account, no further action is required.',
            'If you have used the mobile app to create your account, then please enter this code:',
            // Uncomment below when used default setup without core kit.
            //'If you have used the mobile app to create your account, then please enter this code: <b>:code</b>.'
        ];
    }

}