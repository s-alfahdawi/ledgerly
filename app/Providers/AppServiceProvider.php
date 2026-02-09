<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\MemberPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Members page authorizes against User::class; use MemberPolicy for member management.
        Gate::policy(User::class, MemberPolicy::class);

        // Use Bootstrap 5 pagination views (Laravel defaults to Tailwind).
        Paginator::useBootstrapFive();
    }
}
