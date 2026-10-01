<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactImportant extends Model
{
    protected $fillable = [
        'name',
        'type',
        'phone',
        'address',
        'description',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Scope untuk kontak yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk mengurutkan berdasarkan sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute()
    {
        if (!$this->phone) {
            return null;
        }

        // Format nomor telepon Indonesia (0823… / +62823… → 0823…)
        $phone = preg_replace('/[^0-9]/', '', $this->phone);

        if (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }

        return $phone !== '' ? $phone : $this->phone;
    }
}
