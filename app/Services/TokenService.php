<?php

namespace App\Services;

use App\Models\User;
use App\Models\PersonalAccessToken;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Service for managing personal access tokens
 * Handles creation and management of API tokens with abilities
 */
class TokenService
{
    /**
     * Create new personal access token
     *
     * @param array $data
     * @return array
     */
    public function createToken(array $data): array
    {
        // Get the user by tokenable_id
        $user = User::findOrFail($data['tokenable_id']);

        // Convert expires_at string to Carbon instance if provided
        $expiresAt = null;
        if (isset($data['expires_at']) && $data['expires_at']) {
            $expiresAt = Carbon::parse($data['expires_at']);
        }

        // Create token using the user's createToken method
        $token = $user->createToken(
            $data['name'],
            [], // abilities will be handled via relationship
            $expiresAt
        );

        // Cast to our PersonalAccessToken model
        $personalAccessToken = PersonalAccessToken::find($token->accessToken->id);

        // Attach abilities if provided
        if (!empty($data['abilities'])) {
            $personalAccessToken->abilities()->attach($data['abilities']);
        }

        return [
            'token' => $personalAccessToken,
            'plain_text_token' => $token->plainTextToken,
            'user' => $user
        ];
    }
}
