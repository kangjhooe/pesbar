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
        'is_featured',
        'is_breaking',
        'views',
        'published_at',
        'scheduled_at',
        'rejection_reason',
        'suspension_reason',
        'suspended_at',
        'suspended_by',
        'correction_notice',
        'corrected_at',
        'fact_checked_at',
        'fact_checked_by',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_breaking' => 'boolean',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'suspended_at' => 'datetime',
        'corrected_at' => 'datetime',
        'fact_checked_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });

        // Artikel published wajib punya published_at (tidak boleh "terbit" tanpa tanggal).
        static::saving(function (Article $article) {
            if ($article->status === 'published' && empty($article->published_at)) {
                $article->published_at = now();
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

    public function factChecker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fact_checked_by');
    }

    public function suspendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ArticleReport::class);
    }

    public function hasCorrection(): bool
    {
        return filled($this->correction_notice);
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

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function readingHistory(): HasMany
    {
        return $this->hasMany(ReadingHistory::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function getIsPublishedAttribute()
    {
        return $this->status === 'published' && $this->published_at !== null;
    }

    public function getIsArchivedAttribute()
    {
        return $this->status === 'archived';
    }

    public function getIsSuspendedAttribute()
    {
        return $this->status === 'suspended';
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

    /**
     * Parameter untuk route('articles.show', ...).
     */
    public function publicRouteParameters(): array
    {
        $category = $this->relationLoaded('category')
            ? $this->category
            : $this->category()->first();

        return [
            'category' => $category,
            'article' => $this,
        ];
    }

    /**
     * URL publik artikel: /{kategori}/{slug}.
     */
    public function publicUrl(bool $absolute = true): string
    {
        $params = $this->publicRouteParameters();

        if (empty($params['category'])) {
            return route('articles.index', [], $absolute);
        }

        return route('articles.show', $params, $absolute);
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

    /**
     * Estimasi waktu baca (menit), ~200 kata/menit.
     */
    public function readingTimeMinutes(): int
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($this->content ?? '')) ?? '');
        if ($text === '') {
            return 1;
        }

        $words = count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY));

        return max(1, (int) ceil($words / 200));
    }

    /**
     * Konten siap tampil: buang paragraf kosong Quill & width inline gambar.
     */
    public function formattedContent(): string
    {
        $html = $this->content ?? '';
        if ($html === '') {
            return '';
        }

        // Paragraf kosong khas Quill
        $html = preg_replace('/<p>(?:\s|&nbsp;|<br\s*\/?>)*<\/p>/iu', '', $html) ?? $html;

        // Lepas width/height attribute pada gambar agar CSS yang mengatur ukuran
        $html = preg_replace_callback(
            '/<img\b([^>]*)>/iu',
            static function (array $matches): string {
                $attrs = $matches[1];
                $attrs = preg_replace('/\s(?:width|height)\s*=\s*(["\']).*?\1/iu', '', $attrs) ?? $attrs;
                $attrs = preg_replace_callback(
                    '/\sstyle\s*=\s*(["\'])(.*?)\1/iu',
                    static function (array $styleMatch): string {
                        $quote = $styleMatch[1];
                        $style = $styleMatch[2];
                        $style = preg_replace('/(?:^|;)\s*(?:width|height|max-width|min-width)\s*:[^;]*/iu', '', $style) ?? $style;
                        $style = trim($style, " \t\n\r\0\x0B;");
                        if ($style === '') {
                            return '';
                        }

                        return ' style=' . $quote . $style . $quote;
                    },
                    $attrs
                ) ?? $attrs;

                return '<img' . $attrs . '>';
            },
            $html
        ) ?? $html;

        return trim($html);
    }
}
