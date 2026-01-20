<?php

namespace App\Policies;

use App\Models\User;
use App\Services\AccountContext;

class ReportPolicy
{
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    public function view(User $user): bool
    {
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            return false;
        }

        return $user->accounts()->where('accounts.id', $accountId)->exists()
            && $user->hasPermissionInAccount($accountId, 'reports.view');
    }

    public function export(User $user): bool
    {
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            return false;
        }

        return $user->accounts()->where('accounts.id', $accountId)->exists()
            && $user->hasPermissionInAccount($accountId, 'exports.pdf');
    }
}
