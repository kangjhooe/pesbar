<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleReport extends Model
{
    public const REASONS = [
        'hoax' => 'Hoaks / informasi palsu',
        'hate' => 'Ujaran kebencian',
        'plagiarism' => 'Plagiarisme',
        'inappropriate' => 'Konten tidak pantas',
        'privacy' => 'Melanggar privasi',
        'other' => 'Lainnya',
    ];

    protected $fillable = [
        'article_id',
        'user_id',
        'name',
        'email',
        'reason',
        'details',
        'status',
        'ip_address',
        'resolved_by',
        'resolved_at',
        'resolution_note',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function reasonLabel(): string
    {
        return self::REASONS[$this->reason] ?? $this->reason;
    }

    public function reporterLabel(): string
    {
        if ($this->user) {
            return $this->user->name . ' (login)';
        }

        return trim(($this->name ?? 'Tamu') . ($this->email ? ' · ' . $this->email : ''));
    }
}
