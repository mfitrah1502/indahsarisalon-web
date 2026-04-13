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
        $middleware->validateCsrfTokens(except: [
            'booking/notification',
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\PreventBackHistory::class,
        ]);

        $middleware->alias([
            'session.timeout' => \App\Http\Middleware\SessionTimeout::class,
            'prevent-back' => \App\Http\Middleware\PreventBackHistory::class,
        ]);

        $middleware->redirectGuestsTo(function () {
            session()->flash('info', 'Silahkan login terlebih dahulu untuk melanjutkan.');
            return route('auth');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
