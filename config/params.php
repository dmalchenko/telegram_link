<?php

use kartik\datecontrol\Module;

return [
    'adminEmail' => 'admin@example.com',
    'group_id' => '77253035',
    'vk_id' => '6477854',
    'vk_secret' => 'jX3DmKxhTE0aZRWqENE4',
    'vk_redirect_uri' => 'http://tlegram.ru/vk/set-code',
    // format settings for displaying each date attribute (ICU format example)
    'dateControlDisplay' => [
        Module::FORMAT_DATE => 'dd-MM-yyyy',
        Module::FORMAT_TIME => 'hh:mm:ss a',
        Module::FORMAT_DATETIME => 'dd-MM-yyyy hh:mm:ss a',
    ],

    // format settings for saving each date attribute (PHP format example)
    'dateControlSave' => [
        Module::FORMAT_DATE => 'php:U', // saves as unix timestamp
        Module::FORMAT_TIME => 'php:H:i:s',
        Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
    ]
];
