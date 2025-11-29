<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Definir gates para roles
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('cashier', function ($user) {
            return $user->role === 'cashier';
        });

        Gate::define('client', function ($user) {
            return $user->role === 'client';
        });
    }
}