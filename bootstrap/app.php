<?php

use App\Http\Middleware\EnsureAdminLoggedIn;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => EnsureAdminLoggedIn::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'admin/api/*',
            'admin/pembayaran/*',
            'customer/register',
            'customer/login',
            'mitra/daftar',
            'mitra/register',
            'mitra/login',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
