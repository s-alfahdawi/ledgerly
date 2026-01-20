<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Account Model
 * 
 * Represents a workspace/tenant in the multi-tenant system.
 * 
 * CURRENCY HANDLING:
 * All amounts stored in transactions, wallets, etc. are in the account's currency_code.
 * No implicit currency conversion occurs - all amounts are stored as-is in account currency.
 */
class Account extends Model
{
    protected $fillable = [
        'name',
        'currency_code',
        'timezone',
        'owner_user_id',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'account_user')
            ->withPivot('role', 'invited_by', 'joined_at', 'permissions')
            ->withTimestamps();
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
