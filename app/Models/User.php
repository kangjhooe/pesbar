<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'display_name',
        'organization_name',
        'username',
        'email',
        'password',
        'role',
        'verified',
        'is_internal',
        'provider',
        'provider_id',
        'verification_requested_at',
        'verification_request_status',
        'verification_type',
        'verification_document',
        'verification_documents',
        'verification_rejection_reason',
        'content_warning_count',
        'publish_restricted_until',
        'banned_until',
        'ban_appeal_status',
        'ban_appeal_message',
        'ban_appeal_at',
        'ban_appeal_rejection_reason',
    ];

    /**
     * Label dokumen upgrade per kunci file.
     */
    public const UPGRADE_DOCUMENT_LABELS = [
        'ktp' => 'KTP',
        'application_letter' => 'Surat permohonan menjadi penulis',
        'operational_permit' => 'Izin operasional / SK pendirian',
        'assignment_letter' => 'Surat tugas dari pimpinan lembaga',
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
            'is_internal' => 'boolean',
            'verification_documents' => 'array',
            'verification_requested_at' => 'datetime',
            'publish_restricted_until' => 'datetime',
            'banned_until' => 'datetime',
            'ban_appeal_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Username permanen setelah pernah di-set (URL profil publik).
        static::updating(function (User $user) {
            if ($user->isDirty('username')) {
                $original = $user->getOriginal('username');
                if (is_string($original) && $original !== '') {
                    $user->username = $original;
                }
            }
        });

        // Invariant: tidak boleh ada penulis belum terverifikasi.
        static::saving(function (User $user) {
            if ($user->role === 'penulis' && !$user->verified) {
                throw new \RuntimeException(
                    'Penulis harus terverifikasi. Gunakan upgrade yang disetujui admin atau jadikan Redaksi.'
                );
            }
        });
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
     * Riwayat sanksi konten.
     */
    public function contentSanctions(): HasMany
    {
        return $this->hasMany(ContentSanction::class)->latest();
    }

    /**
     * Get the comments made by the user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the comment likes/dislikes made by the user.
     */
    public function commentLikes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }

    /**
     * Get the poll votes made by the user.
     */
    public function pollVotes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    /**
     * Get the bookmarks made by the user.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Get the reading history of the user.
     */
    public function readingHistory(): HasMany
    {
        return $this->hasMany(ReadingHistory::class)->orderBy('read_at', 'desc');
    }

    /**
     * Get the users that this user follows.
     */
    public function follows(): HasMany
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    /**
     * Get the users that follow this user.
     */
    public function followers(): HasMany
    {
        return $this->hasMany(Follow::class, 'following_id');
    }

    /**
     * Get the authors that this user follows.
     */
    public function followingAuthors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
            ->withTimestamps();
    }

    /**
     * Check if user has bookmarked an article.
     */
    public function hasBookmarked(Article $article): bool
    {
        return $this->bookmarks()->where('article_id', $article->id)->exists();
    }

    /**
     * Check if user is following another user.
     */
    public function isFollowing(User $user): bool
    {
        return $this->follows()->where('following_id', $user->id)->exists();
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
     * Nama publik penulis (byline, profil publik).
     * Untuk lembaga: nama lembaga; selain itu nama akun orang.
     */
    public function publicName(): string
    {
        $display = is_string($this->display_name) ? trim($this->display_name) : '';

        return $display !== '' ? $display : (string) $this->name;
    }

    /**
     * Apakah penulis terverifikasi sebagai lembaga.
     */
    public function isLembaga(): bool
    {
        return $this->verification_type === 'lembaga';
    }

    /**
     * Dokumen upgrade yang tersedia (path relatif storage), berlabel.
     *
     * @return array<string, array{key:string,label:string,path:string}>
     */
    public function upgradeDocuments(): array
    {
        $docs = is_array($this->verification_documents) ? $this->verification_documents : [];
        $result = [];

        foreach (self::UPGRADE_DOCUMENT_LABELS as $key => $label) {
            $path = $docs[$key] ?? null;
            if (!is_string($path) || $path === '') {
                continue;
            }
            $result[$key] = [
                'key' => $key,
                'label' => $label,
                'path' => $path,
            ];
        }

        // Fallback data lama: satu file di verification_document
        if ($result === [] && is_string($this->verification_document) && $this->verification_document !== '') {
            $result['legacy'] = [
                'key' => 'legacy',
                'label' => 'Dokumen verifikasi',
                'path' => $this->verification_document,
            ];
        }

        return $result;
    }

    /**
     * Kunci dokumen wajib menurut tipe upgrade.
     *
     * @return list<string>
     */
    public static function requiredUpgradeDocumentKeys(string $type): array
    {
        return match ($type) {
            'perorangan' => ['ktp', 'application_letter'],
            'lembaga' => ['operational_permit', 'application_letter', 'assignment_letter'],
            default => [],
        };
    }

    /**
     * Penulis staf redaksi (dibuat/ditandai admin, bukan lewat upgrade publik).
     */
    public function isRedaksi(): bool
    {
        return $this->role === 'penulis' && (bool) $this->is_internal;
    }

    /**
     * Jadikan penulis redaksi: verified, tanpa pengajuan dokumen.
     */
    public function markAsRedaksi(): void
    {
        $this->update([
            'role' => 'penulis',
            'verified' => true,
            'is_internal' => true,
            'verification_request_status' => null,
            'verification_rejection_reason' => null,
            'publish_restricted_until' => null,
            'banned_until' => null,
            'ban_appeal_status' => null,
            'ban_appeal_message' => null,
            'ban_appeal_at' => null,
            'ban_appeal_rejection_reason' => null,
        ]);
    }

    /**
     * Cabut status redaksi; tetap penulis terverifikasi (kontributor).
     */
    public function unmarkRedaksi(): void
    {
        $this->update([
            'is_internal' => false,
        ]);
    }

    /**
     * Penulis sedang dibatasi publish langsung (harus lewat review).
     */
    public function isPublishRestricted(): bool
    {
        return $this->publish_restricted_until !== null
            && $this->publish_restricted_until->isFuture();
    }

    /**
     * Boleh publish langsung (penulis verified; kecuali publish dibatasi / banned).
     * Tidak ada antrean review — jika dibatasi, hanya boleh draft.
     */
    public function canPublishDirectly(): bool
    {
        return $this->isPenulis()
            && $this->isVerified()
            && !$this->isPublishRestricted()
            && !$this->isBanned();
    }

    /**
     * Sedang dalam masa banned (tidak bisa jadi/ajukan penulis).
     */
    public function isBanned(): bool
    {
        return $this->banned_until !== null && $this->banned_until->isFuture();
    }

    /**
     * Durasi ban menurut nomor sanksi: 1→7 hari, 2→30 hari, 3+→365 hari.
     */
    public static function sanctionDaysForLevel(int $level): int
    {
        return match (true) {
            $level <= 1 => 7,
            $level === 2 => 30,
            default => 365,
        };
    }

    /**
     * Turunkan ke user biasa (artikel lama tetap milik akun ini).
     */
    public function demoteToUser(): void
    {
        $this->update([
            'role' => 'user',
            'verified' => false,
            'is_internal' => false,
            'display_name' => null,
            'verification_request_status' => null,
            'verification_rejection_reason' => null,
            'publish_restricted_until' => null,
        ]);
    }

    /**
     * Beri sanksi konten: demote ke user + ban berjenjang; tercatat di content_sanctions.
     *
     * @return array{level:int,days:int,banned_until:\Carbon\CarbonInterface,sanction:ContentSanction}
     */
    public function issueSanction(?string $reason = null, ?User $issuer = null): array
    {
        if ($this->isRedaksi()) {
            throw new \RuntimeException('Cabut status Redaksi terlebih dahulu sebelum memberi sanksi.');
        }

        if ($this->role !== 'penulis') {
            throw new \RuntimeException('Hanya penulis yang dapat diberi sanksi konten.');
        }

        $level = (int) $this->content_warning_count + 1;
        $days = self::sanctionDaysForLevel($level);
        $bannedUntil = now()->addDays($days);

        $this->content_warning_count = $level;
        $this->banned_until = $bannedUntil;
        $this->ban_appeal_status = null;
        $this->ban_appeal_message = null;
        $this->ban_appeal_at = null;
        $this->ban_appeal_rejection_reason = null;
        $this->role = 'user';
        $this->verified = false;
        $this->is_internal = false;
        $this->display_name = null;
        $this->verification_request_status = null;
        $this->verification_rejection_reason = null;
        $this->publish_restricted_until = null;
        $this->save();

        $sanction = $this->contentSanctions()->create([
            'level' => $level,
            'days' => $days,
            'banned_until' => $bannedUntil,
            'reason' => $reason,
            'issued_by' => $issuer?->id,
        ]);

        return [
            'level' => $level,
            'days' => $days,
            'banned_until' => $bannedUntil,
            'sanction' => $sanction,
        ];
    }

    /**
     * Cabut verified = turunkan ke user (bukan ban, kecuali dipanggil lewat sanksi).
     */
    public function revokeVerifiedToUser(): void
    {
        if ($this->isRedaksi()) {
            throw new \RuntimeException('Tidak dapat mencabut verified penulis redaksi. Cabut status Redaksi terlebih dahulu.');
        }

        $this->demoteToUser();
    }

    /**
     * Buka ban lebih awal (admin). Role tetap user; harus ajukan upgrade lagi.
     */
    public function liftBan(?User $admin = null, ?string $note = null): void
    {
        $this->update([
            'banned_until' => null,
            'ban_appeal_status' => $this->ban_appeal_status === 'pending' ? 'approved' : $this->ban_appeal_status,
            'ban_appeal_rejection_reason' => null,
        ]);

        $active = $this->contentSanctions()
            ->whereNull('lifted_at')
            ->latest()
            ->first();

        if ($active) {
            $active->update([
                'lifted_at' => now(),
                'lifted_by' => $admin?->id,
                'lift_note' => $note,
            ]);
        }
    }

    /**
     * Ajukan banding atas ban aktif.
     */
    public function submitBanAppeal(string $message): void
    {
        if (!$this->isBanned()) {
            throw new \RuntimeException('Anda tidak sedang dalam masa banned.');
        }

        if ($this->ban_appeal_status === 'pending') {
            throw new \RuntimeException('Banding Anda sedang ditinjau.');
        }

        $this->update([
            'ban_appeal_status' => 'pending',
            'ban_appeal_message' => $message,
            'ban_appeal_at' => now(),
            'ban_appeal_rejection_reason' => null,
        ]);
    }

    /**
     * Tolak banding; ban tetap berjalan.
     */
    public function rejectBanAppeal(?string $reason = null): void
    {
        $this->update([
            'ban_appeal_status' => 'rejected',
            'ban_appeal_rejection_reason' => $reason,
        ]);
    }

    public function clearPublishRestriction(): void
    {
        $this->update(['publish_restricted_until' => null]);
    }

    /**
     * @deprecated Gunakan revokeVerifiedToUser()
     */
    public function revokeVerifiedKeepRole(): void
    {
        $this->revokeVerifiedToUser();
    }

    /**
     * @deprecated Gunakan issueSanction()
     */
    public function issueContentWarning(?int $restrictDays = null): array
    {
        $result = $this->issueSanction(null, null);

        return [
            'warning' => $result['level'],
            'restricted_days' => $result['days'],
            'verified_revoked' => true,
            'demoted' => true,
            'banned_until' => $result['banned_until'],
        ];
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
     * User biasa menunggu keputusan upgrade ke penulis.
     */
    public function hasPendingUpgradeRequest(): bool
    {
        return $this->role === 'user'
            && $this->verification_request_status === 'pending';
    }

    /**
     * @deprecated Penulis unverified tidak lagi diizinkan. Upgrade hanya dari role user.
     */
    public function canRequestVerification(): bool
    {
        return false;
    }

    /**
     * User biasa boleh (ulang) mengajukan upgrade ke penulis.
     */
    public function canRequestUpgrade(): bool
    {
        return $this->role === 'user'
            && !$this->isBanned()
            && $this->verification_request_status !== 'pending';
    }

    /**
     * Demote penulis unverified (data lama / invariant break) ke user.
     * Artikel pending_review → draft. Pengajuan pending dipertahankan sebagai upgrade.
     */
    public function demoteUnverifiedPenulisToUser(): bool
    {
        if ($this->role !== 'penulis' || $this->verified) {
            return false;
        }

        $this->articles()
            ->where('status', 'pending_review')
            ->update([
                'status' => 'draft',
                'rejection_reason' => null,
            ]);

        $keepUpgradePending = $this->verification_request_status === 'pending';

        // Bypass saving hook: role berubah ke user bersamaan dengan verified=false.
        $this->forceFill([
            'role' => 'user',
            'verified' => false,
            'is_internal' => false,
            'display_name' => null,
            'verification_request_status' => $keepUpgradePending ? 'pending' : null,
            'verification_rejection_reason' => $keepUpgradePending ? null : $this->verification_rejection_reason,
            'publish_restricted_until' => null,
        ])->saveQuietly();

        return true;
    }

    public function canSubmitBanAppeal(): bool
    {
        return $this->isBanned() && $this->ban_appeal_status !== 'pending';
    }
}
