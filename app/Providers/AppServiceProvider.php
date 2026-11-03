<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Allow when the user's role has the permission; otherwise fall
        // through to normal rules (default: denied).
        Gate::before(function (User $user, string $ability) {
            return $user->hasPermission($ability) ? true : null;
        });
    }
}
