<?php

return [

    /*
    |--------------------------------------------------------------------------
    | laravel-dadata
    |--------------------------------------------------------------------------
    |
    */
    'token' => env('DADATA_TOKEN', ''),

    'secret' => env('DADATA_SECRET', ''),

    'code_error' => 2,
    'code_halfsuccess' => 1,
    'code_ambiguous' => 3,
];
