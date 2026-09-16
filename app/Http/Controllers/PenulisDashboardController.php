<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Models\UserProfile;
use App\Helpers\ActivityLogHelper;
use App\Helpers\UploadValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PenulisDashboardController extends Controller
{
    /**
     * Constructor - memastikan hanya penulis, admin, atau editor yang bisa akses
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            
            // Refresh user dari database untuk memastikan data terbaru (terutama role)
            // Ini penting ketika role user berubah saat mereka masih login
            $user->refresh();
            
            // Admin dan editor bisa akses semua
            if ($user->isAdmin() || $user->isEditor()) {
                return $next($request);
            }
            
            // Hanya penulis yang bisa akses
            if (!$user->isPenulis()) {
                // Jika user bukan penulis lagi, redirect ke user dashboard
                return redirect()->route('user.dashboard')
                    ->with('error', 'Akses ditolak. Anda tidak lagi memiliki akses sebagai penulis.');
            }
            
            return $next($request);
        });
    }

    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_articles' => $user->articles()->count(),
            'published_articles' => $user->articles()->where('status', 'published')->count(),
            'pending_articles' => $user->articles()->where('status', 'pending_review')->count(),
            'rejected_articles' => $user->articles()->where('status', 'rejected')->count(),
            'draft_articles' => $user->articles()->where('status', 'draft')->count(),
            'total_views' => $user->articles()->sum('views'),
            'total_comments' => $user->articles()->withCount('comments')->get()->sum('comments_count'),
            'avg_views' => $user->articles()->where('status', 'published')->avg('views') ?? 0,
        ];

        $popularArticles = $user->articles()
            ->with('category')
            ->where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        $recentArticles = $user->articles()
            ->with('category')
            ->latest()
            ->limit(5)
            ->get();

        return view('penulis.dashboard', compact('stats', 'popularArticles', 'recentArticles'));
    }

    public function articles(Request $request)
    {
        $user = Auth::user();

        $query = $user->articles()->with(['category', 'tags'])->withCount('comments');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $articles = \App\Helpers\AdminTableHelper::applySort($query, $request, [
            'title' => 'title',
            'status' => 'status',
            'views' => 'views',
            'comments_count' => 'comments_count',
            'created_at' => 'created_at',
        ], 'created_at', 'desc')->paginate(15)->withQueryString();

        $categories = \App\Models\Category::where('is_active', true)->get();

        return view('penulis.articles.index', compact('articles', 'categories'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        $tags = \App\Models\Tag::all();
        return view('penulis.articles.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'excerpt' => 'required|string|max:500',
            'content' => 'nullable|string',
            'content_html' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'featured_image' => UploadValidation::image(false, 2048),
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'save_as_draft' => 'nullable|boolean',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        // Use content_html if available (from Quill), otherwise use content
        $content = $request->content_html ?? $request->content;
        if (empty($content) || $content === '<p><br></p>' || trim(strip_tags($content)) === '') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['content' => 'Konten artikel wajib diisi.']);
        }

        $user = Auth::user();
        
        // Determine status
        if ($request->has('save_as_draft') && $request->save_as_draft) {
            $status = 'draft';
        } elseif ($request->scheduled_at) {
            $status = $user->isVerified() ? 'published' : 'pending_review';
        } else {
            $status = $user->isVerified() ? 'published' : 'pending_review';
        }

        $article = $user->articles()->create([
            'title' => $request->title,
            'slug' => $request->slug ?: \Str::slug($request->title),
            'excerpt' => $request->excerpt,
            'content' => $content,
            'category_id' => $request->category_id,
            'status' => $status,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'featured_image' => $request->hasFile('featured_image') 
                ? $request->file('featured_image')->store('articles', 'public') 
                : null,
            'published_at' => $request->scheduled_at ? null : ($status === 'published' ? now() : null),
            'scheduled_at' => $request->scheduled_at ? \Carbon\Carbon::parse($request->scheduled_at) : null,
        ]);

        // Handle tags
        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        } else {
            $article->tags()->detach();
        }

        $message = $status === 'draft' ? 'Draft artikel berhasil disimpan!' : 'Artikel berhasil dibuat!';
        return redirect()->route('penulis.articles.index')->with('success', $message);
    }

    public function edit(Article $article)
    {
        $this->authorize('update', $article);
        $categories = \App\Models\Category::all();
        $tags = \App\Models\Tag::all();
        return view('penulis.articles.edit', compact('article', 'categories', 'tags'));
    }

    public function update(Request $request, Article $article)
    {
        $this->authorize('update', $article);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug,' . $article->id,
            'excerpt' => 'required|string|max:500',
            'content' => 'nullable|string',
            'content_html' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'featured_image' => UploadValidation::image(false, 2048),
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'save_as_draft' => 'nullable|boolean',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        // Use content_html if available (from Quill), otherwise use content
        $content = $request->content_html ?? $request->content;
        if (empty($content) || $content === '<p><br></p>' || trim(strip_tags($content)) === '') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['content' => 'Konten artikel wajib diisi.']);
        }

        $user = Auth::user();
        
        // Determine status - don't change if already published, unless saving as draft
        if ($request->has('save_as_draft') && $request->save_as_draft) {
            $status = 'draft';
        } elseif ($request->scheduled_at) {
            $status = $user->isVerified() ? 'published' : 'pending_review';
        } elseif ($article->status === 'published') {
            // Keep published status if already published
            $status = 'published';
        } else {
            // For pending/rejected/draft, set based on verification
            $status = $user->isVerified() ? 'published' : 'pending_review';
        }

        $updateData = [
            'title' => $request->title,
            'slug' => $request->slug ?: \Str::slug($request->title),
            'excerpt' => $request->excerpt,
            'content' => $content,
            'category_id' => $request->category_id,
            'status' => $status,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'scheduled_at' => $request->scheduled_at ? \Carbon\Carbon::parse($request->scheduled_at) : null,
        ];

        // Set published_at if publishing for the first time
        if ($status === 'published' && !$article->published_at && !$request->scheduled_at) {
            $updateData['published_at'] = now();
        } elseif ($request->scheduled_at) {
            $updateData['published_at'] = null; // Will be set when scheduled time arrives
        }

        $article->update($updateData);

        // Handle featured image
        if ($request->hasFile('featured_image')) {
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $article->update([
                'featured_image' => $request->file('featured_image')->store('articles', 'public')
            ]);
        }

        // Handle tags
        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        } else {
            $article->tags()->detach();
        }

        $message = $status === 'draft' ? 'Draft artikel berhasil diperbarui!' : 'Artikel berhasil diperbarui!';
        return redirect()->route('penulis.articles.index')->with('success', $message);
    }

    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);
        
        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }
        
        $article->delete();
        return redirect()->route('penulis.articles.index')->with('success', 'Artikel berhasil dihapus!');
    }

    public function bulkArticles(Request $request)
    {
        $request->validate([
            'action' => 'required|in:draft,submit,delete',
            'articles' => 'required|array|min:1',
            'articles.*' => 'integer|exists:articles,id',
        ]);

        $user = Auth::user();
        $articles = Article::whereIn('id', $request->articles)
            ->where('author_id', $user->id)
            ->get();

        if ($articles->isEmpty()) {
            return back()->with('error', 'Tidak ada artikel yang dapat diproses.');
        }

        $count = 0;

        switch ($request->action) {
            case 'draft':
                foreach ($articles as $article) {
                    $this->authorize('update', $article);
                    $article->update(['status' => 'draft']);
                    $count++;
                }
                $message = "{$count} artikel diubah ke draft.";
                break;

            case 'submit':
                $status = $user->isVerified() ? 'published' : 'pending_review';
                foreach ($articles as $article) {
                    $this->authorize('update', $article);
                    $article->update(['status' => $status]);
                    $count++;
                }
                $message = $status === 'published'
                    ? "{$count} artikel diterbitkan."
                    : "{$count} artikel diajukan untuk review.";
                break;

            case 'delete':
                foreach ($articles as $article) {
                    $this->authorize('delete', $article);
                    if ($article->featured_image) {
                        try {
                            Storage::disk('public')->delete($article->featured_image);
                        } catch (\Exception $e) {
                            // continue deleting even if image cleanup fails
                        }
                    }
                    $article->tags()->detach();
                    $article->delete();
                    $count++;
                }
                $message = "{$count} artikel dihapus.";
                break;

            default:
                return back()->with('error', 'Aksi tidak valid.');
        }

        return redirect()->route('penulis.articles.index')->with('success', $message);
    }

    public function profile()
    {
        $user = Auth::user();
        $user->load('profile');
        return view('penulis.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9_-]+$/',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'bio' => 'nullable|string|max:1000',
            'avatar' => UploadValidation::image(false, 2048),
            'website' => 'nullable|url',
            'location' => 'nullable|string|max:255',
            'social_links' => 'nullable|array',
        ]);

        // Update basic user info
        $userData = $request->only(['name', 'email']);
        $userData['username'] = strtolower($request->username); // Ensure username is lowercase
        
        $user->fill($userData);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update or create profile
        $profile = $user->profile;
        $profileData = [
            'bio' => $request->bio,
            'website' => $request->website,
            'location' => $request->location,
            'social_links' => $request->social_links ?? [],
        ];

        if ($request->hasFile('avatar')) {
            if ($profile && $profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $profileData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($profile) {
            $profile->update($profileData);
        } else {
            $user->profile()->create($profileData);
        }

        return redirect()->route('penulis.profile')->with('success', 'Profil berhasil diperbarui!');
    }

    public function requestVerification()
    {
        $user = Auth::user();
        
        if (!$user->canRequestVerification()) {
            return redirect()->route('penulis.dashboard')
                ->with('error', 'Anda tidak dapat mengajukan verifikasi saat ini.');
        }

        return view('penulis.request-verification');
    }

    public function submitVerificationRequest(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->canRequestVerification()) {
            return redirect()->route('penulis.dashboard')
                ->with('error', 'Anda tidak dapat mengajukan verifikasi saat ini.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:1000',
            'verification_type' => 'required|in:perorangan,lembaga',
            'verification_document' => UploadValidation::verificationDocument(true, 5120),
        ]);

        $updateData = [
            'verification_requested_at' => now(),
            'verification_request_status' => 'pending',
            'verification_type' => $request->verification_type,
            'verification_rejection_reason' => null,
        ];

        // Handle file upload
        if ($request->hasFile('verification_document')) {
            // Delete old document if exists
            if ($user->verification_document && Storage::disk('public')->exists($user->verification_document)) {
                Storage::disk('public')->delete($user->verification_document);
            }
            
            $updateData['verification_document'] = $request->file('verification_document')->store('verification-documents', 'public');
        }

        $user->update($updateData);

        // Log activity
        ActivityLogHelper::logUser('verification.requested', $user, "Penulis {$user->name} mengajukan permintaan verifikasi");

        return redirect()->route('penulis.dashboard')
            ->with('success', 'Permintaan verifikasi berhasil dikirim! Admin akan meninjau permintaan Anda.');
    }

    public function show(Article $article)
    {
        $this->authorize('view', $article);
        
        $article->load(['category', 'tags', 'comments' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);
        
        return view('penulis.articles.show', compact('article'));
    }

    public function comments(Article $article)
    {
        $this->authorize('view', $article);
        
        $comments = $article->comments()->orderBy('created_at', 'desc')->paginate(20);
        
        return view('penulis.articles.comments', compact('article', 'comments'));
    }

    public function updateCommentStatus(Request $request, Article $article, Comment $comment)
    {
        $this->authorize('update', $article);
        
        // Verify comment belongs to article
        if ($comment->article_id !== $article->id) {
            abort(403);
        }
        
        $request->validate([
            'is_approved' => 'required|boolean',
        ]);
        
        $comment->update([
            'is_approved' => $request->is_approved
        ]);
        
        return redirect()->back()->with('success', 'Status komentar berhasil diperbarui!');
    }

    public function deleteComment(Article $article, Comment $comment)
    {
        $this->authorize('update', $article);
        
        // Verify comment belongs to article
        if ($comment->article_id !== $article->id) {
            abort(403);
        }
        
        $comment->delete();
        
        return redirect()->back()->with('success', 'Komentar berhasil dihapus!');
    }

    public function saveDraft(Request $request, Article $article = null)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'featured_image' => UploadValidation::image(false, 2048),
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'slug' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        
        if ($article) {
            $this->authorize('update', $article);
        }

        // Generate unique slug if creating new draft
        if (!$article) {
            if ($request->slug) {
                $baseSlug = \Str::slug($request->slug);
            } elseif ($request->title) {
                $baseSlug = \Str::slug($request->title);
            } else {
                $baseSlug = 'draft-' . time();
            }
            
            // Ensure slug is unique
            $uniqueSlug = $baseSlug;
            $counter = 1;
            while (\App\Models\Article::where('slug', $uniqueSlug)->exists()) {
                $uniqueSlug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $slug = $uniqueSlug;
        } else {
            // For existing article, validate slug uniqueness if changed
            if ($request->slug && $request->slug !== $article->slug) {
                $baseSlug = \Str::slug($request->slug);
                $uniqueSlug = $baseSlug;
                $counter = 1;
                while (\App\Models\Article::where('slug', $uniqueSlug)->where('id', '!=', $article->id)->exists()) {
                    $uniqueSlug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                $slug = $uniqueSlug;
            } else {
                $slug = $article->slug;
            }
        }

        $data = [
            'title' => $request->title ?? ($article ? $article->title : 'Draft tanpa judul'),
            'excerpt' => $request->excerpt ?? ($article ? $article->excerpt : null),
            'content' => $request->content ?? ($article ? $article->content : ''),
            'category_id' => $request->category_id ?? ($article ? $article->category_id : null),
            'status' => 'draft',
            'slug' => $slug,
            'meta_description' => $request->meta_description ?? ($article ? $article->meta_description : null),
            'meta_keywords' => $request->meta_keywords ?? ($article ? $article->meta_keywords : null),
        ];

        if ($request->hasFile('featured_image')) {
            if ($article && $article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        if ($article) {
            $article->update($data);
        } else {
            $article = $user->articles()->create($data);
        }

        // Handle tags
        if ($article->exists && $request->has('tags')) {
            $article->tags()->sync($request->tags);
        }

        return response()->json([
            'success' => true,
            'message' => 'Draft berhasil disimpan!',
            'article_id' => $article->id
        ]);
    }

    public function duplicate(Article $article)
    {
        $this->authorize('update', $article);
        
        $newArticle = $article->replicate();
        $newArticle->title = $article->title . ' (Copy)';
        $newArticle->slug = \Str::slug($newArticle->title) . '-' . time();
        $newArticle->author_id = Auth::id();
        $newArticle->status = 'draft';
        $newArticle->published_at = null;
        $newArticle->scheduled_at = null;
        $newArticle->views = 0;
        $newArticle->save();

        // Copy tags
        foreach ($article->tags as $tag) {
            $newArticle->tags()->attach($tag);
        }

        return redirect()->route('penulis.articles.edit', $newArticle)
            ->with('success', 'Artikel berhasil diduplikasi!');
    }

    public function export(Article $article)
    {
        $this->authorize('view', $article);
        
        $content = view('penulis.articles.export', compact('article'))->render();
        
        return response($content)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . \Str::slug($article->title) . '.html"');
    }

    /**
     * Analytics Dashboard
     */
    public function analytics(Request $request)
    {
        $user = Auth::user();
        $days = $request->get('days', 30);
        
        // Get user's articles
        $articles = $user->articles()->with(['category', 'tags'])->get();
        $publishedArticles = $articles->where('status', 'published');
        
        // Overall stats
        $stats = [
            'total_articles' => $articles->count(),
            'published_articles' => $publishedArticles->count(),
            'total_views' => $publishedArticles->sum('views'),
            'total_comments' => Comment::whereIn('article_id', $articles->pluck('id'))
                ->where('is_approved', true)
                ->count(),
            'avg_views_per_article' => $publishedArticles->count() > 0 
                ? round($publishedArticles->sum('views') / $publishedArticles->count(), 2) 
                : 0,
            'engagement_rate' => $this->calculateEngagementRate(
                $publishedArticles->sum('views'),
                Comment::whereIn('article_id', $articles->pluck('id'))->where('is_approved', true)->count()
            ),
        ];
        
        // Articles published over time (since we don't track daily views)
        $viewsOverTime = [];
        $startDate = now()->subDays($days);
        
        // Group articles by published date
        $articlesByDate = $publishedArticles
            ->filter(function($article) use ($startDate) {
                return $article->published_at && $article->published_at->gte($startDate);
            })
            ->groupBy(function($article) {
                return $article->published_at->format('Y-m-d');
            });
        
        // Create data points for each day in the range
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $dayArticles = $articlesByDate->get($dateKey, collect());
            
            $viewsOverTime[] = [
                'date' => $dateKey,
                'date_formatted' => $date->format('d/m'),
                'views' => $dayArticles->sum('views'),
                'articles' => $dayArticles->count(),
            ];
        }
        
        // Top performing articles (limit to 5 for better UI)
        $topArticles = $publishedArticles
            ->sortByDesc('views')
            ->take(5)
            ->map(function($article) {
                $commentsCount = $article->comments()->where('is_approved', true)->count();
                $daysSincePublished = $article->published_at ? now()->diffInDays($article->published_at) : 0;
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'views' => $article->views,
                    'comments' => $commentsCount,
                    'engagement_rate' => $this->calculateEngagementRate($article->views, $commentsCount),
                    'avg_views_per_day' => $daysSincePublished > 0 ? round($article->views / $daysSincePublished, 2) : $article->views,
                    'published_at' => $article->published_at,
                    'category' => $article->category->name ?? 'N/A',
                ];
            })
            ->values();
        
        // Performance by category
        $categoryPerformance = [];
        $categories = \App\Models\Category::whereIn('id', $publishedArticles->pluck('category_id')->unique())->get();
        foreach ($categories as $category) {
            $catArticles = $publishedArticles->where('category_id', $category->id);
            $totalViews = $catArticles->sum('views');
            $totalComments = Comment::whereIn('article_id', $catArticles->pluck('id'))
                ->where('is_approved', true)
                ->count();
            
            $categoryPerformance[] = [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'total_articles' => $catArticles->count(),
                'total_views' => $totalViews,
                'total_comments' => $totalComments,
                'avg_views_per_article' => $catArticles->count() > 0 ? round($totalViews / $catArticles->count(), 2) : 0,
                'engagement_rate' => $this->calculateEngagementRate($totalViews, $totalComments),
            ];
        }
        usort($categoryPerformance, fn($a, $b) => $b['total_views'] <=> $a['total_views']);
        
        return view('penulis.analytics.index', compact('stats', 'viewsOverTime', 'topArticles', 'categoryPerformance', 'days'));
    }

    /**
     * Media Library
     */
    public function mediaLibrary(Request $request)
    {
        $user = Auth::user();
        
        // Get all media files from articles
        $articles = $user->articles()->whereNotNull('featured_image')->get();
        $mediaFiles = [];
        
        foreach ($articles as $article) {
            if ($article->featured_image && Storage::disk('public')->exists($article->featured_image)) {
                $filePath = $article->featured_image;
                $fullPath = storage_path('app/public/' . $filePath);
                $fileInfo = pathinfo($filePath);
                
                $mediaFiles[] = [
                    'id' => $article->id,
                    'name' => $fileInfo['basename'],
                    'path' => $filePath,
                    'url' => Storage::disk('public')->url($filePath),
                    'size' => file_exists($fullPath) ? filesize($fullPath) : 0,
                    'type' => mime_content_type($fullPath) ?? 'image/jpeg',
                    'article_id' => $article->id,
                    'article_title' => $article->title,
                    'uploaded_at' => $article->created_at,
                ];
            }
        }
        
        // Sort by uploaded_at desc
        usort($mediaFiles, fn($a, $b) => $b['uploaded_at']->timestamp <=> $a['uploaded_at']->timestamp);
        
        // Pagination
        $perPage = 24;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedFiles = array_slice($mediaFiles, $offset, $perPage);
        $totalPages = ceil(count($mediaFiles) / $perPage);
        
        return view('penulis.media.index', compact('paginatedFiles', 'currentPage', 'totalPages', 'perPage'));
    }

    /**
     * Upload media
     */
    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => UploadValidation::image(true, 5120),
        ]);
        
        $file = $request->file('file');
        $path = $file->store('media/penulis/' . Auth::id(), 'public');
        
        return response()->json([
            'success' => true,
            'url' => Storage::disk('public')->url($path),
            'path' => $path,
            'name' => $file->getClientOriginalName(),
        ]);
    }

    /**
     * Delete media
     */
    public function deleteMedia(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);
        
        $path = str_replace('\\', '/', (string) $request->path);

        // Reject traversal / absolute / null-byte paths
        if (
            $path === ''
            || str_contains($path, '..')
            || str_contains($path, "\0")
            || str_starts_with($path, '/')
            || preg_match('#^[a-zA-Z]:#', $path)
        ) {
            return response()->json(['success' => false, 'message' => 'Path tidak valid'], 403);
        }
        
        $user = Auth::user();
        $userMediaPrefix = 'media/penulis/' . $user->id . '/';
        $article = $user->articles()->where('featured_image', $path)->first();
        
        if (!$article && !str_starts_with($path, $userMediaPrefix)) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan atau tidak memiliki akses'], 403);
        }
        
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            
            // If it's an article featured image, remove it
            if ($article) {
                $article->update(['featured_image' => null]);
            }
        }
        
        return response()->json(['success' => true, 'message' => 'File berhasil dihapus']);
    }

    /**
     * Advanced Comment Management
     */
    public function commentsAdvanced(Request $request)
    {
        $user = Auth::user();
        
        $query = Comment::whereIn('article_id', $user->articles()->pluck('id'))
            ->with(['article', 'user', 'parent'])
            ->withCount(['allReplies as replies_count', 'likes as likes_count']);
        
        // Filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_approved', $request->status === 'approved');
        }
        
        if ($request->has('article_id') && $request->article_id !== '') {
            $query->where('article_id', $request->article_id);
        }
        
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('comment', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $comments = $query->paginate(20)->withQueryString();
        $articles = $user->articles()->published()->get(['id', 'title']);
        
        // Stats
        $stats = [
            'total' => Comment::whereIn('article_id', $user->articles()->pluck('id'))->count(),
            'approved' => Comment::whereIn('article_id', $user->articles()->pluck('id'))->where('is_approved', true)->count(),
            'pending' => Comment::whereIn('article_id', $user->articles()->pluck('id'))->where('is_approved', false)->count(),
            'with_replies' => Comment::whereIn('article_id', $user->articles()->pluck('id'))->whereNotNull('parent_id')->count(),
        ];
        
        return view('penulis.comments.advanced', compact('comments', 'articles', 'stats'));
    }

    /**
     * Bulk comment actions
     */
    public function bulkCommentAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id',
        ]);
        
        $user = Auth::user();
        $userArticleIds = $user->articles()->pluck('id');
        
        $comments = Comment::whereIn('id', $request->comment_ids)
            ->whereIn('article_id', $userArticleIds)
            ->get();
        
        $count = 0;
        foreach ($comments as $comment) {
            switch ($request->action) {
                case 'approve':
                    $comment->update(['is_approved' => true]);
                    $count++;
                    break;
                case 'reject':
                    $comment->update(['is_approved' => false]);
                    $count++;
                    break;
                case 'delete':
                    $comment->delete();
                    $count++;
                    break;
            }
        }
        
        return redirect()->back()->with('success', "Berhasil {$request->action} {$count} komentar");
    }

    /**
     * Reply to comment
     */
    public function replyComment(Request $request, Article $article, Comment $comment)
    {
        $this->authorize('update', $article);
        
        if ($comment->article_id !== $article->id) {
            abort(403);
        }
        
        $request->validate([
            'comment' => 'required|string|max:1000',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);
        
        Comment::create([
            'article_id' => $article->id,
            'parent_id' => $comment->id,
            'name' => $request->name,
            'email' => $request->email,
            'comment' => $request->comment,
            'is_approved' => true, // Author replies are auto-approved
            'ip_address' => $request->ip(),
        ]);
        
        return redirect()->back()->with('success', 'Balasan berhasil ditambahkan');
    }

    /**
     * Export comments
     */
    public function exportComments(Article $article)
    {
        $this->authorize('update', $article);
        
        $comments = $article->comments()->with('user', 'parent')->get();
        
        $csv = "ID,Artikel,Penulis,Email,Komentar,Status,Parent ID,Tanggal\n";
        foreach ($comments as $comment) {
            $csv .= sprintf(
                "%d,\"%s\",\"%s\",\"%s\",\"%s\",%s,%s,%s\n",
                $comment->id,
                $article->title,
                $comment->name,
                $comment->email,
                str_replace('"', '""', $comment->comment),
                $comment->is_approved ? 'Approved' : 'Pending',
                $comment->parent_id ?? '',
                $comment->created_at->format('Y-m-d H:i:s')
            );
        }
        
        return response($csv)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="komentar-' . \Str::slug($article->title) . '.csv"');
    }

    /**
     * SEO Tools
     */
    public function seoTools(Request $request, Article $article = null)
    {
        $user = Auth::user();
        $articles = $user->articles()->published()->get(['id', 'title', 'slug']);
        
        // If article ID is provided in request but not as route parameter
        if (!$article && $request->has('article_id')) {
            $article = $user->articles()->find($request->article_id);
        }
        
        if ($article) {
            $this->authorize('view', $article);
            
            $seoAnalysis = $this->analyzeArticleSEO($article);
            
            return view('penulis.seo.analyze', compact('article', 'articles', 'seoAnalysis'));
        }
        
        return view('penulis.seo.index', compact('articles'));
    }

    /**
     * Analyze article SEO
     */
    protected function analyzeArticleSEO(Article $article): array
    {
        $analysis = [
            'score' => 0,
            'max_score' => 100,
            'checks' => [],
            'meta_title' => [
                'value' => $article->title,
                'length' => strlen($article->title),
                'optimal' => strlen($article->title) >= 30 && strlen($article->title) <= 60,
                'score' => 0,
            ],
            'meta_description' => [
                'value' => $article->meta_description ?? $article->excerpt,
                'length' => strlen($article->meta_description ?? $article->excerpt),
                'optimal' => strlen($article->meta_description ?? $article->excerpt) >= 120 && strlen($article->meta_description ?? $article->excerpt) <= 160,
                'score' => 0,
            ],
            'meta_keywords' => [
                'value' => $article->meta_keywords,
                'has_keywords' => !empty($article->meta_keywords),
                'score' => 0,
            ],
            'slug' => [
                'value' => $article->slug,
                'optimal' => strlen($article->slug) <= 100 && preg_match('/^[a-z0-9-]+$/', $article->slug),
                'score' => 0,
            ],
            'featured_image' => [
                'has_image' => !empty($article->featured_image),
                'score' => 0,
            ],
            'content' => [
                'word_count' => str_word_count(strip_tags($article->content)),
                'optimal' => str_word_count(strip_tags($article->content)) >= 300,
                'score' => 0,
            ],
            'headings' => [
                'h1_count' => substr_count($article->content, '<h1'),
                'h2_count' => substr_count($article->content, '<h2'),
                'has_h1' => substr_count($article->content, '<h1') > 0,
                'has_h2' => substr_count($article->content, '<h2') > 0,
                'score' => 0,
            ],
            'links' => [
                'internal_links' => substr_count($article->content, '<a href'),
                'has_links' => substr_count($article->content, '<a href') > 0,
                'score' => 0,
            ],
            'readability' => $this->calculateReadability($article->content),
        ];
        
        // Calculate scores
        $score = 0;
        
        // Meta title (15 points)
        if ($analysis['meta_title']['optimal']) {
            $analysis['meta_title']['score'] = 15;
            $score += 15;
        } elseif (strlen($analysis['meta_title']['value']) > 0) {
            $analysis['meta_title']['score'] = 8;
            $score += 8;
        }
        
        // Meta description (15 points)
        if ($analysis['meta_description']['optimal']) {
            $analysis['meta_description']['score'] = 15;
            $score += 15;
        } elseif (strlen($analysis['meta_description']['value']) > 0) {
            $analysis['meta_description']['score'] = 8;
            $score += 8;
        }
        
        // Meta keywords (10 points)
        if ($analysis['meta_keywords']['has_keywords']) {
            $analysis['meta_keywords']['score'] = 10;
            $score += 10;
        }
        
        // Slug (10 points)
        if ($analysis['slug']['optimal']) {
            $analysis['slug']['score'] = 10;
            $score += 10;
        } elseif (strlen($analysis['slug']['value']) > 0) {
            $analysis['slug']['score'] = 5;
            $score += 5;
        }
        
        // Featured image (10 points)
        if ($analysis['featured_image']['has_image']) {
            $analysis['featured_image']['score'] = 10;
            $score += 10;
        }
        
        // Content length (15 points)
        if ($analysis['content']['optimal']) {
            $analysis['content']['score'] = 15;
            $score += 15;
        } elseif ($analysis['content']['word_count'] >= 150) {
            $analysis['content']['score'] = 8;
            $score += 8;
        }
        
        // Headings (10 points)
        if ($analysis['headings']['has_h1'] && $analysis['headings']['has_h2']) {
            $analysis['headings']['score'] = 10;
            $score += 10;
        } elseif ($analysis['headings']['has_h2']) {
            $analysis['headings']['score'] = 5;
            $score += 5;
        }
        
        // Links (5 points)
        if ($analysis['links']['has_links']) {
            $analysis['links']['score'] = 5;
            $score += 5;
        }
        
        $analysis['score'] = $score;
        
        return $analysis;
    }

    /**
     * Calculate readability score (Flesch Reading Ease)
     */
    protected function calculateReadability(string $content): array
    {
        $text = strip_tags($content);
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
        
        $words = str_word_count($text);
        $sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $syllables = 0;
        
        $wordArray = explode(' ', $text);
        foreach ($wordArray as $word) {
            $syllables += max(1, preg_match_all('/[aeiou]+/i', $word));
        }
        
        if ($words == 0 || count($sentences) == 0) {
            return [
                'score' => 0,
                'level' => 'Tidak dapat dihitung',
                'description' => 'Konten terlalu pendek untuk dihitung',
            ];
        }
        
        $avgSentenceLength = $words / count($sentences);
        $avgSyllablesPerWord = $syllables / $words;
        
        $score = 206.835 - (1.015 * $avgSentenceLength) - (84.6 * $avgSyllablesPerWord);
        $score = max(0, min(100, $score));
        
        $level = 'Sangat Sulit';
        $description = 'Sangat sulit dibaca, memerlukan tingkat pendidikan tinggi';
        
        if ($score >= 90) {
            $level = 'Sangat Mudah';
            $description = 'Sangat mudah dibaca, cocok untuk anak-anak';
        } elseif ($score >= 80) {
            $level = 'Mudah';
            $description = 'Mudah dibaca, cocok untuk siswa sekolah dasar';
        } elseif ($score >= 70) {
            $level = 'Cukup Mudah';
            $description = 'Cukup mudah dibaca, cocok untuk siswa sekolah menengah';
        } elseif ($score >= 60) {
            $level = 'Standar';
            $description = 'Tingkat bacaan standar, cocok untuk siswa sekolah menengah atas';
        } elseif ($score >= 50) {
            $level = 'Cukup Sulit';
            $description = 'Cukup sulit dibaca, memerlukan tingkat pendidikan menengah';
        } elseif ($score >= 30) {
            $level = 'Sulit';
            $description = 'Sulit dibaca, memerlukan tingkat pendidikan tinggi';
        }
        
        return [
            'score' => round($score, 1),
            'level' => $level,
            'description' => $description,
        ];
    }

    /**
     * Calculate engagement rate
     */
    protected function calculateEngagementRate(int $views, int $comments): float
    {
        if ($views === 0) {
            return 0.0;
        }
        
        return round(($comments / $views) * 100, 2);
    }
}
