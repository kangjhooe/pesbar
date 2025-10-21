<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'option_text',
        'description',
        'color',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Relationship dengan poll
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Relationship dengan votes
     */
    public function votes()
    {
        return $this->hasMany(PollVote::class);
    }

    /**
     * Scope untuk option yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get vote count for this option
     */
    public function getVoteCountAttribute()
    {
        return $this->votes()->count();
    }

    /**
     * Get vote percentage for this option
     */
    public function getVotePercentageAttribute()
    {
        $totalVotes = $this->poll->total_votes;
        if ($totalVotes == 0) {
            return 0;
        }

        return round(($this->vote_count / $totalVotes) * 100, 1);
    }
}