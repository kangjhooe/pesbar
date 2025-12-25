<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'username',
        'email',
        'password',
        'role',
        'verified',
        'provider',
        'provider_id',
        'verification_requested_at',
        'verification_request_status',
        'verification_type',
        'verification_document',
    ];

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
            'verified' => 'boolean',
            'verification_requested_at' => 'datetime',
        ];
    }

    /**
     * Get the user's profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the articles authored by the user.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is editor.
     */
    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    /**
     * Check if user is penulis.
     */
    public function isPenulis(): bool
    {
        return $this->role === 'penulis';
    }

    /**
     * Check if user is verified.
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'username';
    }

    /**
     * Generate a unique username from name.
     */
    public static function generateUsername(string $name, ?int $excludeUserId = null): string
    {
        $baseUsername = \Str::slug($name);
        
        // If slug is empty, use a default
        if (empty($baseUsername)) {
            $baseUsername = 'user';
        }
        
        $username = $baseUsername;
        $counter = 1;
        
        // Ensure username is unique
        $query = static::where('username', $username);
        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }
        
        while ($query->exists()) {
            $username = $baseUsername . '-' . $counter;
            $query = static::where('username', $username);
            if ($excludeUserId) {
                $query->where('id', '!=', $excludeUserId);
            }
            $counter++;
        }
        
        return $username;
    }

    /**
     * Check if user has pending verification request.
     */
    public function hasPendingVerificationRequest(): bool
    {
        return $this->verification_request_status === 'pending';
    }

    /**
     * Check if user can request verification.
     */
    public function canRequestVerification(): bool
    {
        return $this->role === 'penulis' 
            && !$this->verified 
            && $this->verification_request_status !== 'pending';
    }
}
