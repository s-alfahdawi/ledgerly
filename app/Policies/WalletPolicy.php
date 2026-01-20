<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wallet;
use App\Services\AccountContext;

class WalletPolicy
{
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    protected function hasPermission(User $user, string $permission): bool
    {
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            return false;
        }
        return $user->hasPermissionInAccount($accountId, $permission);
    }

    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'wallets.view');
    }

    public function view(User $user, Wallet $wallet): bool
    {
        if (!$this->hasPermission($user, 'wallets.view')) {
            return false;
        }
        return $wallet->account_id === $this->accountContext->id();
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'wallets.create');
    }

    public function update(User $user, Wallet $wallet): bool
    {
        if (!$this->hasPermission($user, 'wallets.update')) {
            return false;
        }
        return $wallet->account_id === $this->accountContext->id();
    }

    public function delete(User $user, Wallet $wallet): bool
    {
        if (!$this->hasPermission($user, 'wallets.delete')) {
            return false;
        }
        return $wallet->account_id === $this->accountContext->id();
    }
}
