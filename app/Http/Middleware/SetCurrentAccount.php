<?php

namespace App\Http\Middleware;

use App\Services\AccountContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentAccount
{
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $currentAccountId = $this->accountContext->id();

        // If no account is set in session, try to set the first available account
        if (!$currentAccountId) {
            $firstAccount = $user->accounts()->first();
            if ($firstAccount) {
                $this->accountContext->set($firstAccount->id);
            }
            // If user has no accounts at all, let it pass and the controller will handle the error
            return $next($request);
        }

        // Verify the account in session still exists and user is still a member
        $isMember = $user->accounts()->where('accounts.id', $currentAccountId)->exists();
        if (!$isMember) {
            // User is no longer a member of this account, switch to first available
            $firstAccount = $user->accounts()->first();
            if ($firstAccount) {
                $this->accountContext->set($firstAccount->id);
            } else {
                $this->accountContext->clear();
            }
        }

        return $next($request);
    }
}
