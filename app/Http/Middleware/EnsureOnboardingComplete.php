<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Models\Wallet;
use App\Services\AccountContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingComplete
{
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $accountId = $this->accountContext->id();

        if (!$accountId) {
            return $next($request);
        }

        // Check if the account has at least one wallet and one category
        $hasWallets = Wallet::forAccount($accountId)->exists();
        $hasCategories = Category::forAccount($accountId)->exists();

        if (!$hasWallets || !$hasCategories) {
            return redirect()->route('onboarding.index');
        }

        return $next($request);
    }
}
