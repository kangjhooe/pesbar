<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'poll_type',
        'is_active',
        'allow_anonymous',
        'show_results',
        'show_vote_count',
        'start_date',
        'end_date',
        'max_votes_per_user',
        'settings'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allow_anonymous' => 'boolean',
        'show_results' => 'boolean',
        'show_vote_count' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'settings' => 'array'
    ];

    /**
     * Relationship dengan poll options
     */
    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class)->orderBy('sort_order');
    }

    /**
     * Relationship dengan poll votes
     */
    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    /**
     * Scope untuk poll yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk poll yang sedang berjalan
     */
    public function scopeRunning($query)
    {
        $now = now();
        return $query->where('is_active', true)
                    ->where(function($q) use ($now) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', $now);
                    })
                    ->where(function($q) use ($now) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', $now);
                    });
    }

    /**
     * Scope untuk poll yang sudah selesai
     */
    public function scopeFinished($query)
    {
        return $query->where('is_active', true)
                    ->whereNotNull('end_date')
                    ->where('end_date', '<', now());
    }

    /**
     * Scope untuk poll yang akan dimulai
     */
    public function scopeUpcoming($query)
    {
        return $query->where('is_active', true)
                    ->whereNotNull('start_date')
                    ->where('start_date', '>', now());
    }

    /**
     * Get total votes count
     */
    public function getTotalVotesAttribute()
    {
        return $this->votes()->count();
    }

    /**
     * Get poll status
     */
    public function getStatusAttribute()
    {
        $now = now();
        
        if (!$this->is_active) {
            return 'inactive';
        }
        
        if ($this->start_date && $this->start_date > $now) {
            return 'upcoming';
        }
        
        if ($this->end_date && $this->end_date < $now) {
            return 'finished';
        }
        
        return 'running';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'inactive' => 'Tidak Aktif',
            'upcoming' => 'Akan Dimulai',
            'running' => 'Berlangsung',
            'finished' => 'Selesai'
        ];

        return $labels[$this->status] ?? 'Tidak Diketahui';
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'inactive' => 'gray',
            'upcoming' => 'blue',
            'running' => 'green',
            'finished' => 'red'
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Get poll type label
     */
    public function getPollTypeLabelAttribute()
    {
        $types = [
            'single' => 'Pilihan Tunggal',
            'multiple' => 'Pilihan Ganda'
        ];

        return $types[$this->poll_type] ?? 'Pilihan Tunggal';
    }

    /**
     * Get formatted start date
     */
    public function getFormattedStartDateAttribute()
    {
        return $this->start_date ? $this->start_date->format('d-m-Y H:i') : null;
    }

    /**
     * Get formatted end date
     */
    public function getFormattedEndDateAttribute()
    {
        return $this->end_date ? $this->end_date->format('d-m-Y H:i') : null;
    }

    /**
     * Get days remaining
     */
    public function getDaysRemainingAttribute()
    {
        if (!$this->end_date) {
            return null;
        }
        
        return now()->diffInDays($this->end_date, false);
    }

    /**
     * Check if user can vote
     */
    public function canUserVote($userId = null, $ipAddress = null): bool
    {
        return $this->getVoteBlockReason($userId, $ipAddress) === null;
    }

    /**
     * Why voting is blocked, or null if allowed.
     * Logged-in: one account = one vote.
     * Guest: only when allow_anonymous, and one IP = one vote.
     */
    public function getVoteBlockReason($userId = null, $ipAddress = null): ?string
    {
        if (!$this->is_active || $this->status !== 'running') {
            return 'Polling ini tidak tersedia untuk voting';
        }

        if (!$userId && !$this->allow_anonymous) {
            return 'Silakan login untuk memberikan suara';
        }

        if ($userId) {
            if ($this->votes()->where('user_id', $userId)->exists()) {
                return 'Anda sudah memberikan suara untuk polling ini';
            }

            return null;
        }

        if ($ipAddress && $this->votes()->where('ip_address', $ipAddress)->exists()) {
            return 'Anda sudah memberikan suara untuk polling ini';
        }

        return null;
    }

    /**
     * Get vote percentage for an option
     */
    public function getVotePercentage($optionId)
    {
        $totalVotes = $this->total_votes;
        if ($totalVotes == 0) {
            return 0;
        }

        $optionVotes = $this->votes()->where('poll_option_id', $optionId)->count();
        return round(($optionVotes / $totalVotes) * 100, 1);
    }
}