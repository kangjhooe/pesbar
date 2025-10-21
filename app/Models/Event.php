<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'location',
        'organizer',
        'event_type',
        'priority',
        'is_public',
        'is_active',
        'image',
        'contact_info',
        'metadata'
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'array'
    ];

    /**
     * Scope untuk event yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk event yang public
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope untuk event yang akan datang
     */
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now()->toDateString());
    }

    /**
     * Scope untuk event berdasarkan tipe
     */
    public function scopeByType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope untuk event berdasarkan prioritas
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope untuk event hari ini
     */
    public function scopeToday($query)
    {
        return $query->where('event_date', now()->toDateString());
    }

    /**
     * Scope untuk event minggu ini
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('event_date', [
            now()->startOfWeek()->toDateString(),
            now()->endOfWeek()->toDateString()
        ]);
    }

    /**
     * Scope untuk event bulan ini
     */
    public function scopeThisMonth($query)
    {
        return $query->whereBetween('event_date', [
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        ]);
    }

    /**
     * Get formatted event date
     */
    public function getFormattedDateAttribute()
    {
        return $this->event_date->format('d-m-Y');
    }

    /**
     * Get formatted start time
     */
    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time ? $this->start_time->format('H:i') : null;
    }

    /**
     * Get formatted end time
     */
    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time ? $this->end_time->format('H:i') : null;
    }

    /**
     * Get event type label
     */
    public function getEventTypeLabelAttribute()
    {
        $types = [
            'pemerintah' => 'Pemerintah',
            'masyarakat' => 'Masyarakat',
            'budaya' => 'Budaya',
            'olahraga' => 'Olahraga',
            'pendidikan' => 'Pendidikan',
            'kesehatan' => 'Kesehatan',
            'lainnya' => 'Lainnya'
        ];

        return $types[$this->event_type] ?? 'Lainnya';
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute()
    {
        $priorities = [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi'
        ];

        return $priorities[$this->priority] ?? 'Sedang';
    }

    /**
     * Get priority color
     */
    public function getPriorityColorAttribute()
    {
        $colors = [
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'red'
        ];

        return $colors[$this->priority] ?? 'yellow';
    }

    /**
     * Get event status
     */
    public function getStatusAttribute()
    {
        $today = now()->toDateString();
        
        if ($this->event_date < $today) {
            return 'past';
        } elseif ($this->event_date == $today) {
            return 'today';
        } else {
            return 'upcoming';
        }
    }

    /**
     * Get days until event
     */
    public function getDaysUntilEventAttribute()
    {
        return now()->diffInDays($this->event_date, false);
    }

    /**
     * Get full image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        
        return asset('images/default-event.jpg');
    }
}