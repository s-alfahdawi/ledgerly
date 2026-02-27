<?php

namespace App\Providers;

use App\Enums\Currency;
use App\Models\User;
use App\Policies\MemberPolicy;
use App\Services\AccountContext;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(User::class, MemberPolicy::class);
        Paginator::useBootstrapFive();

        // Share AccountContext data with all views — resolved once per request
        View::composer('*', function ($view) {
            static $resolved = false;
            static $account = null;
            static $accountId = null;
            static $currencyCode = null;

            if (!$resolved && auth()->check()) {
                $ctx = app(AccountContext::class);
                $account = $ctx->account();
                $accountId = $ctx->id();
                $currencyCode = $account?->currency_code ?? Currency::IQD->value;
                $resolved = true;
            }

            $view->with('__account', $account);
            $view->with('__accountId', $accountId);
            $view->with('__currencyCode', $currencyCode ?? Currency::IQD->value);
        });
    }
}
