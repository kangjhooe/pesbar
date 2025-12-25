<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\Cache;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'meta_description',
        'meta_keywords',
        'content',
        'featured_image',
        'category_id',
        'author_id',
        'status',
        'type',
        'is_featured',
        'is_breaking',
        'views',
        'published_at',
        'scheduled_at',
        'rejection_reason',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_breaking' => 'boolean',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tags');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('is_approved', true)->with('user');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function getIsPublishedAttribute()
    {
        return $this->status === 'published';
    }

    public function getIsRejectedAttribute()
    {
        return $this->status === 'rejected';
    }

    public function getIsArchivedAttribute()
    {
        return $this->status === 'archived';
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeBreaking($query)
    {
        return $query->where('is_breaking', true);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views', 'desc');
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    public function scopeBerita($query)
    {
        return $query->where('type', 'berita');
    }

    public function scopeArtikel($query)
    {
        return $query->where('type', 'artikel');
    }

    public function incrementViewCount()
    {
        $this->increment('views');
        
        // Clear cache for popular articles since views affect the ordering
        // Clear all popular_articles cache keys (different limits)
        Cache::forget('popular_articles_5');
        Cache::forget('popular_articles_10');
        Cache::forget('popular_articles_15');
        Cache::forget('popular_articles_20');
        
        // Also clear dashboard stats cache since it includes total views
        CacheHelper::clearDashboardCache();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getFormattedDateAttribute()
    {
        return $this->published_at ? $this->published_at->format('d-m-Y') : '';
    }

    public function getFormattedDateTimeAttribute()
    {
        return $this->published_at ? $this->published_at->format('d-m-Y H:i') : '';
    }

    public function getFormattedTimeAttribute()
    {
        return $this->published_at ? $this->published_at->format('H:i') : '';
    }
}
