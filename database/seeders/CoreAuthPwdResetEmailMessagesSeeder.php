<?php

namespace Database\Seeders;

use AttractCores\LaravelCoreTranslation\Database\Seeders\CorePackagesTranslationsSeeder;

/**
 * Class CoreAuthPwdResetEmailMessagesSeeder
 *
 * @package Database\Seeders
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class CoreAuthPwdResetEmailMessagesSeeder extends CorePackagesTranslationsSeeder
{

    /**
     * Return messages array.
     *
     * @return array
     */
    protected function getMessages() : array
    {
        return [
            'Password Reset',
            'You are receiving this email because we received a password reset request for your account.',
            'Reset Password',
            'This password reset link will expire in :count minutes.',
            'If you did not request a password reset, no further action is required.',
            'If you have used the mobile app, then please enter this code:',
            // Uncomment below when used default setup without core kit.
            //'If you have used the mobile app, then please enter this code: <b>:code</b>.'
        ];
    }

}