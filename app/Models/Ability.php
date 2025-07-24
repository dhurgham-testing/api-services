<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ability extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function personalAccessTokens(): BelongsToMany
    {
        return $this->belongsToMany(PersonalAccessToken::class, 'personal_access_token_abilities');
    }
}
