<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AccountContext
{
    const SESSION_KEY = 'current_account_id';

    public function id(): ?int
    {
        $id = Session::get(self::SESSION_KEY);
        
        // If no account is set in session, try to resolve it automatically
        if (!$id) {
            $this->resolve();
            $id = Session::get(self::SESSION_KEY);
        }
        
        return $id;
    }

    public function account(): ?Account
    {
        $id = $this->id();
        return $id ? Account::find($id) : null;
    }

    public function set(int $accountId): void
    {
        Session::put(self::SESSION_KEY, $accountId);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Switch to a different account.
     */
    public function switch(int $accountId): void
    {
        $this->set($accountId);
    }

    /**
     * Automatically resolve and set the current account if not already set.
     */
    public function resolve(): ?Account
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        $currentId = Session::get(self::SESSION_KEY);
        
        // If already set, verify it's still valid
        if ($currentId) {
            $account = Account::find($currentId);
            if ($account && $user->accounts()->where('accounts.id', $currentId)->exists()) {
                return $account;
            }
        }

        // Try to get the first account
        $firstAccount = $user->accounts()->first();
        if ($firstAccount) {
            $this->set($firstAccount->id);
            return $firstAccount;
        }

        return null;
    }
}
