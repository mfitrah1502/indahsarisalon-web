<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

class MiddlewareServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Daftarkan route middleware
        Route::aliasMiddleware('role', RoleMiddleware::class);
    }
}