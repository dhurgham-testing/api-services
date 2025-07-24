<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $fillable = [
        'name',
        'tokenable_id',
        'tokenable_type',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
    ];

    protected static function booted()
    {
        static::creating(function ($token) {
            if (empty($token->tokenable_type)) {
                $token->tokenable_type = \App\Models\User::class;
            }
        });
    }

    public function abilities(): BelongsToMany
    {
        return $this->belongsToMany(Ability::class, 'personal_access_token_abilities');
    }
} 