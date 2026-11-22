<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe API Credentials
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Stripe public/publishable and secret keys,
    | as well as webhook signing secret and default transaction currency.
    |
    */

    'key' => env('STRIPE_KEY'),

    'secret' => env('STRIPE_SECRET'),

    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    'currency' => env('STRIPE_CURRENCY', 'eur'),
];
