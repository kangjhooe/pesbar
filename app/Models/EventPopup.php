<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EventPopup extends Model
{
    protected $fillable = [
        'title',
        'message',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'boolean'
    ];

    /**
     * Scope untuk mendapatkan popup yang aktif dan sedang berjalan
     */
    public function scopeActive($query)
    {
        $today = Carbon::today();
        return $query->where('status', true)
                    ->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today);
    }

    /**
     * Cek apakah popup sedang aktif
     */
    public function isActive()
    {
        $today = Carbon::today();
        return $this->status && 
               $this->start_date <= $today && 
               $this->end_date >= $today;
    }
}
