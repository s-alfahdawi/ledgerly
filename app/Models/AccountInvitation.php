<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AccountInvitation extends Model
{
    protected $fillable = [
        'account_id',
        'email',
        'token',
        'role',
        'invited_by',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public static function createInvitation(int $accountId, string $email, string $role, int $invitedBy): self
    {
        return self::create([
            'account_id' => $accountId,
            'email' => $email,
            'token' => self::generateToken(),
            'role' => $role,
            'invited_by' => $invitedBy,
            'expires_at' => Carbon::now()->addDays(7), // Valid for 7 days
        ]);
    }
}
