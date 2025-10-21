<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'poll_option_id',
        'user_id',
        'ip_address',
        'user_agent'
    ];

    /**
     * Relationship dengan poll
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Relationship dengan poll option
     */
    public function pollOption()
    {
        return $this->belongsTo(PollOption::class);
    }

    /**
     * Relationship dengan user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get voter name (user name or anonymous)
     */
    public function getVoterNameAttribute()
    {
        if ($this->user) {
            return $this->user->name;
        }
        
        return 'Anonim';
    }

    /**
     * Get formatted vote time
     */
    public function getFormattedVoteTimeAttribute()
    {
        return $this->created_at->format('d-m-Y H:i');
    }
}