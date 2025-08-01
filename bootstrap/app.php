<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'check_students'=>\App\Http\Middleware\Check_Student::class,
            'check_admin'=>\App\Http\Middleware\Check_Admin::class,
            'check_teacher'=>\App\Http\Middleware\Check_Teacher::class,
            'check_auth'=>\App\Http\Middleware\Check_Auth::class,
            'check_teacher_activate'=>\App\Http\Middleware\Check_Teacher_Activate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
