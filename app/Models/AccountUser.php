<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountUser extends Model
{
    protected $table = 'account_user';

    protected $fillable = [
        'account_id',
        'user_id',
        'role',
        'invited_by',
        'joined_at',
        'permissions',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'permissions' => 'array',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
