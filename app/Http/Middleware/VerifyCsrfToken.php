<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * مسیرهایی که نیاز به CSRF ندارند
     */
    protected $except = [
        'payment/callback', // مسیر callback SEP
    ];
}
