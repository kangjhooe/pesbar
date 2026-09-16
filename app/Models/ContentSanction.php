<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentSanction extends Model
{
    protected $fillable = [
        'user_id',
        'level',
        'days',
        'banned_until',
        'reason',
        'issued_by',
        'lifted_at',
        'lifted_by',
        'lift_note',
    ];

    protected function casts(): array
    {
        return [
            'banned_until' => 'datetime',
            'lifted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function lifter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lifted_by');
    }

    public function isActive(): bool
    {
        return $this->lifted_at === null && $this->banned_until->isFuture();
    }
}
