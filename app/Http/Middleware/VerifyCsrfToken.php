<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'admin/employees',
        'admin/employees/*',
        'admin/employee-schedules',
        'admin/employee-schedules/*',
        'admin/shifts',
        'admin/shifts/*',
    ];
}
