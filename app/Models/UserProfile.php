<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'avatar',
        'social_links',
        'website',
        'location',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Always expose social_links as an array.
     * Handles legacy double-encoded JSON from seeders that used json_encode().
     */
    protected function socialLinks(): Attribute
    {
        return Attribute::make(
            get: function (?string $value): array {
                if ($value === null || $value === '') {
                    return [];
                }

                $decoded = json_decode($value, true);

                // Double-encoded JSON: first decode yields another JSON string
                if (is_string($decoded)) {
                    $decoded = json_decode($decoded, true);
                }

                return is_array($decoded) ? $decoded : [];
            },
            set: function ($value): ?string {
                if ($value === null || $value === '') {
                    return null;
                }

                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (is_string($decoded)) {
                        $decoded = json_decode($decoded, true);
                    }
                    $value = is_array($decoded) ? $decoded : [];
                }

                if (! is_array($value)) {
                    return null;
                }

                return json_encode($value);
            },
        );
    }
}
