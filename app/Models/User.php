<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function accounts()
    {
        return $this->belongsToMany(Account::class, 'account_user')
            ->withPivot('role', 'invited_by', 'joined_at', 'permissions')
            ->withTimestamps();
    }

    /**
     * Check if user has permission in the given account context.
     */
    public function hasPermissionInAccount(int $accountId, string $permission): bool
    {
        $membership = $this->accounts()->where('accounts.id', $accountId)->first();
        if (!$membership) {
            return false;
        }

        $role = $membership->pivot->role;
        $rolePermissions = config('permissions.roles.' . $role, []);

        return in_array($permission, $rolePermissions);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
