<?php

namespace App\Policies;

use App\Models\User;
use App\Services\AccountContext;

class MemberPolicy
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
        return $this->hasPermission($user, 'members.view');
    }

    public function invite(User $user): bool
    {
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            return false;
        }

        $membership = $user->accounts()->where('accounts.id', $accountId)->first();
        if (!$membership) {
            return false;
        }

        $role = $membership->pivot->role;
        return in_array($role, ['owner', 'admin']);
    }

    public function update(User $user, User $member): bool
    {
        return $this->invite($user);
    }

    public function delete(User $user, User $member): bool
    {
        return $this->invite($user);
    }
}
