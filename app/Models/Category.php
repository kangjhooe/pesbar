<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function publishedArticles(): HasMany
    {
        return $this->hasMany(Article::class)->where('status', 'published');
    }

    public function publishedBeritaArticles(): HasMany
    {
        return $this->hasMany(Article::class)->where('status', 'published')->where('type', 'berita');
    }

    public function publishedArtikelArticles(): HasMany
    {
        return $this->hasMany(Article::class)->where('status', 'published')->where('type', 'artikel');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
