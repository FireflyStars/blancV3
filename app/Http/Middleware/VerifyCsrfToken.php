<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        'auth/login',
        'logout',
        'authenticate',
        '/stripe-test/connection_token',
        '/stripe-test/create_payment_intent',
        '/stripe-test/capture_payment_intent',
        '/stripe-test/update-terminal-order',
        '/stripe-test/create_setup_intent',
    ];
}
