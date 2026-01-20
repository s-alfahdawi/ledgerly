<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use App\Services\AccountContext;

class CategoryPolicy
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
        return $this->hasPermission($user, 'categories.view');
    }

    public function view(User $user, Category $category): bool
    {
        if (!$this->hasPermission($user, 'categories.view')) {
            return false;
        }
        return $category->account_id === $this->accountContext->id();
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'categories.create');
    }

    public function update(User $user, Category $category): bool
    {
        if (!$this->hasPermission($user, 'categories.update')) {
            return false;
        }
        return $category->account_id === $this->accountContext->id();
    }

    public function delete(User $user, Category $category): bool
    {
        if (!$this->hasPermission($user, 'categories.delete')) {
            return false;
        }
        return $category->account_id === $this->accountContext->id();
    }
}
