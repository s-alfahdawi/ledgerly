<?php

namespace App\Policies;

use App\Models\User;
use App\Services\AccountContext;

class SettingsPolicy
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
        return $this->hasPermission($user, 'settings.view');
    }

    public function update(User $user): bool
    {
        return $this->hasPermission($user, 'settings.manage');
    }
}
