<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Category extends Model
{
    use SoftDeletes, Searchable;

    protected $fillable = [
        'account_id',
        'name',
        'type',
        'color',
        'is_archived',
        'deleted_by',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Scope queries to a specific account.
     * 
     * IMPORTANT: Always use this explicit scope instead of global scopes.
     * This allows flexibility for admin queries, background jobs, and tests.
     */
    public function scopeForAccount(Builder $query, int $accountId): Builder
    {
        return $query->where('account_id', $accountId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_archived', false);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type)->orWhere('type', 'both');
    }

    /**
     * Get the indexable data array for the model (Laravel Scout).
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
        ];
    }
}
