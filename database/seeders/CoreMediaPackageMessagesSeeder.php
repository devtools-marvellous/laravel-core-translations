<?php

namespace Database\Seeders;

use AttractCores\LaravelCoreTranslation\Database\Seeders\CorePackagesTranslationsSeeder;

/**
 * Class CoreMediaPackageMessagesSeeder
 *
 * @package Database\Seeders
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class CoreMediaPackageMessagesSeeder extends CorePackagesTranslationsSeeder
{

    /**
     * Return messages array.
     *
     * @return array
     */
    protected function getMessages() : array
    {
        return [
            "Please, provide valid multipart request with uploaded file.",
            "File size can be :max Kb in length.",
            "You should provide file name, that was saved on AWS.",
            "Please, provide file path on AWS sources bucket.",
            "Please, provide file name to upload to AWS.",
            'File name should contains tmp path prefix ":prefix".',
            "Please, provide file content type, that you want to upload to AWS.",
            "You can request upload of file with this mime types only: :mimes.",
            "Please, provide file content length in bytes, that you want to upload to AWS.",
            "You can request upload of file with maximum :max Kb length.",
            "Given media does not exists in our db or does not attachable."
        ];
    }
}