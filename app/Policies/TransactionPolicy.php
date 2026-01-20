<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use App\Services\AccountContext;

class TransactionPolicy
{
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    public function viewAny(User $user): bool
    {
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            return false;
        }

        return $user->accounts()->where('accounts.id', $accountId)->exists()
            && $user->hasPermissionInAccount($accountId, 'transactions.view');
    }

    public function view(User $user, Transaction $transaction): bool
    {
        $accountId = $this->accountContext->id();
        if (!$accountId || $transaction->account_id !== $accountId) {
            return false;
        }

        return $user->accounts()->where('accounts.id', $accountId)->exists()
            && $user->hasPermissionInAccount($accountId, 'transactions.view');
    }

    public function create(User $user): bool
    {
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            return false;
        }

        return $user->accounts()->where('accounts.id', $accountId)->exists()
            && $user->hasPermissionInAccount($accountId, 'transactions.create');
    }

    public function update(User $user, Transaction $transaction): bool
    {
        $accountId = $this->accountContext->id();
        if (!$accountId || $transaction->account_id !== $accountId) {
            return false;
        }

        // Check permission first
        if (!$user->accounts()->where('accounts.id', $accountId)->exists()
            || !$user->hasPermissionInAccount($accountId, 'transactions.update')) {
            return false;
        }

        // Check if transaction is too old to edit (immutability rule)
        $editDays = config('billing.transaction_edit_days');
        if ($editDays !== null) {
            $cutoffDate = now()->subDays($editDays);
            if ($transaction->occurred_at->lt($cutoffDate)) {
                return false;
            }
        }

        return true;
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        $accountId = $this->accountContext->id();
        if (!$accountId || $transaction->account_id !== $accountId) {
            return false;
        }

        return $user->accounts()->where('accounts.id', $accountId)->exists()
            && $user->hasPermissionInAccount($accountId, 'transactions.delete');
    }
}
