<?php


return [


    /**
     * Card number : 4987654321098769
     * Cardholder Name : Test Account
     * Expiry Month : 05
     * Expiry year : 21
     * CVV : 123
     */
    /*


    |--------------------------------------------------------------------------
    | PayMob username and password
    |--------------------------------------------------------------------------
    |
    | This is your PayMob username and password to make auth request.
    |
    */

    'username' => env('PAYMOB_USERNAME'),
    'password' => env('PAYMOB_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | PayMob integration id
    |--------------------------------------------------------------------------
    |
    | This is your PayMob integration id.
    |
    */

    'integration_id' => env('PAYMOB_INTEGRATION_ID'),
];
