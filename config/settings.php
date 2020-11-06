<?php

return [
    'database' => [
        'table' => 'settings', // default settings table
    ],
    'cache' => [
        'key' => 'settings', // default settings cache key
        'ttl' => 10080, // cache ttl in minutes, default 7 days
    ],
    'encrypt' => [], // specifc keys that will have their values encrypted when stored, decrypted on get
];
