<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Bookmark;
use App\Models\ReadingHistory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\CacheHelper;
use App\Helpers\ActivityLogHelper;
use App\Helpers\UploadValidation;

class AdminArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of articles for admin
     */
    public function index(Request $request)
    {
        $query = Article::with(['category', 'author', 'tags']);

        // Filter by tab: my_articles (only admin's articles) or all_articles (all articles)
        $tab = $request->get('tab', 'all');
        if ($tab === 'my') {
            // Only show articles created by current admin
            $query->where('author_id', Auth::id());
        }
        // If tab is 'all' or not set, show all articles (no additional filter)

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->where('category_id', $request->category);
        }

        // Filter by featured
        if ($request->has('featured') && $request->featured !== '') {
            $query->where('is_featured', $request->featured == '1');
        }

        // Search by title
        if ($request->has('search') && $request->search !== '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $articles = \App\Helpers\AdminTableHelper::applySort($query, $request, [
            'title' => 'title',
            'status' => 'status',
            'created_at' => 'created_at',
            'published_at' => 'published_at',
            'is_featured' => 'is_featured',
            'is_breaking' => 'is_breaking',
            'views' => 'views',
        ], 'created_at', 'desc')->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->get();

        return view('admin.articles.index', compact('articles', 'categories', 'tab'));
    }

    /**
     * Show the form for creating a new article
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $tags = Tag::orderBy('name')->get();
        
        return view('admin.articles.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created article
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'featured_image' => UploadValidation::image(false, 2048),
            'status' => 'required|in:draft,published,archived,suspended',
            'is_featured' => 'boolean',
            'is_breaking' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'new_tags' => 'nullable|string|max:500',
            'published_at' => 'nullable|date',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        unset($data['type'], $data['views'], $data['tags'], $data['new_tags']);
        $data['author_id'] = Auth::id();
        
        // Handle slug - use custom slug if provided, otherwise generate from title
        if ($request->slug) {
            $baseSlug = Str::slug($request->slug);
        } else {
            $baseSlug = Str::slug($request->title);
        }
        
        // Ensure slug is unique
        $uniqueSlug = $baseSlug;
        $counter = 1;
        while (Article::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $data['slug'] = $uniqueSlug;
        
        // Set default values for boolean fields
        $data['is_featured'] = $request->has('is_featured') ? true : false;
        $data['is_breaking'] = $request->has('is_breaking') ? true : false;

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        // Set published_at if status is published
        if ($request->status === 'published' && !$request->published_at) {
            $data['published_at'] = now();
        }

        $article = Article::create($data);
        $article->tags()->sync($this->resolveTagIds($request));

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dibuat!');
    }

    /**
     * Display the specified article
     */
    public function show(Article $article)
    {
        $article->load(['category', 'author', 'tags', 'comments']);
        
        return view('admin.articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified article
     */
    public function edit(Article $article)
    {
        $this->authorize('update', $article);
        
        $categories = Category::where('is_active', true)->get();
        $tags = Tag::orderBy('name')->get();
        $article->load('tags');
        
        return view('admin.articles.edit', compact('article', 'categories', 'tags'));
    }

    /**
     * Update the specified article
     */
    public function update(Request $request, Article $article)
    {
        $this->authorize('update', $article);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'featured_image' => UploadValidation::image(false, 2048),
            'status' => 'required|in:draft,published,archived,suspended',
            'is_featured' => 'boolean',
            'is_breaking' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'new_tags' => 'nullable|string|max:500',
            'published_at' => 'nullable|date',
            'slug' => 'nullable|string|max:255|unique:articles,slug,' . $article->id,
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'correction_notice' => 'nullable|string|max:2000',
        ]);

        $data = $request->all();
        unset($data['type'], $data['author_id'], $data['views'], $data['tags'], $data['new_tags'], $data['fact_checked_at'], $data['fact_checked_by']);
        
        // Handle slug - use custom slug if provided and changed, otherwise keep existing or generate from title
        if ($request->slug && $request->slug !== $article->slug) {
            $baseSlug = Str::slug($request->slug);
            // Ensure slug is unique
            $uniqueSlug = $baseSlug;
            $counter = 1;
            while (Article::where('slug', $uniqueSlug)->where('id', '!=', $article->id)->exists()) {
                $uniqueSlug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $data['slug'] = $uniqueSlug;
        } elseif (!$request->slug && $request->title !== $article->title) {
            // If title changed but no custom slug, regenerate from title
            $baseSlug = Str::slug($request->title);
            $uniqueSlug = $baseSlug;
            $counter = 1;
            while (Article::where('slug', $uniqueSlug)->where('id', '!=', $article->id)->exists()) {
                $uniqueSlug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $data['slug'] = $uniqueSlug;
        } else {
            // Keep existing slug
            $data['slug'] = $article->slug;
        }
        
        // Set default values for boolean fields
        $data['is_featured'] = $request->has('is_featured') ? true : false;
        $data['is_breaking'] = $request->has('is_breaking') ? true : false;

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        // Set published_at if status is published and not already set
        if ($request->status === 'published' && !$article->published_at) {
            $data['published_at'] = now();
        }

        // Clear suspension metadata when leaving suspended
        if ($request->status !== 'suspended') {
            $data['suspension_reason'] = null;
            $data['suspended_at'] = null;
            $data['suspended_by'] = null;
        }

        // Kolom rejection_reason tidak dipakai lagi
        $data['rejection_reason'] = null;

        $notice = trim((string) $request->input('correction_notice', ''));
        if ($notice !== '') {
            $data['correction_notice'] = $notice;
            if ($article->correction_notice !== $notice) {
                $data['corrected_at'] = now();
            }
        } else {
            $data['correction_notice'] = null;
            $data['corrected_at'] = null;
        }

        $oldStatus = $article->status;
        $isOwnArticle = (int) $article->author_id === (int) Auth::id();

        $article->update($data);
        $article->tags()->sync($this->resolveTagIds($request));

        ActivityLogHelper::logArticle(
            'article.updated',
            $article,
            $isOwnArticle
                ? "Admin memperbarui artikel sendiri '{$article->title}' (status: {$oldStatus} → {$article->status})"
                : "Admin memperbarui artikel penulis #{$article->author_id} '{$article->title}' (status: {$oldStatus} → {$article->status})"
        );

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui!');
    }

    /**
     * Remove the specified article
     */
    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);

        DB::beginTransaction();
        try {
            $articleTitle = $article->title;
            $articleId = $article->id;
            
            // Get all comment IDs for this article
            $commentIds = Comment::where('article_id', $articleId)->pluck('id');
            
            // Delete comment likes first (related to comments)
            if ($commentIds->isNotEmpty()) {
                CommentLike::whereIn('comment_id', $commentIds)->delete();
            }
            
            // Delete comments (including nested comments via cascade)
            Comment::where('article_id', $articleId)->delete();
            
            // Delete bookmarks
            Bookmark::where('article_id', $articleId)->delete();
            
            // Delete reading history
            ReadingHistory::where('article_id', $articleId)->delete();
            
            // Detach tags
            $article->tags()->detach();
            
            // Delete featured image
            if ($article->featured_image) {
                try {
                    Storage::disk('public')->delete($article->featured_image);
                } catch (\Exception $e) {
                    \Log::warning('Failed to delete featured image: ' . $e->getMessage());
                }
            }
            
            // Delete the article
            $article->delete();
            
            DB::commit();

            // Clear cache
            CacheHelper::clearArticleCache();
            CacheHelper::clearDashboardCache();
            
            // Log activity
            ActivityLogHelper::log('article.deleted', 'Artikel dihapus: ' . $articleTitle, [
                'article_id' => $articleId,
                'article_title' => $articleTitle
            ]);

            // Preserve query parameters for redirect, but remove page if it exists
            // This prevents 404 when deleting the last item on a page
            $queryParams = request()->only(['tab', 'status', 'category', 'featured', 'search']);
            // Don't preserve page parameter - let it redirect to page 1 or appropriate page
            
            return redirect()->route('admin.articles.index', $queryParams)
                ->with('success', 'Artikel "' . Str::limit($articleTitle, 50) . '" berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Article Deletion Error: ' . $e->getMessage(), [
                'article_id' => $article->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Preserve query parameters for redirect
            $queryParams = request()->only(['tab', 'status', 'category', 'featured', 'search', 'page']);
            
            return redirect()->route('admin.articles.index', $queryParams)
                ->with('error', 'Terjadi kesalahan saat menghapus artikel: ' . $e->getMessage());
        }
    }

    /**
     * Archive the specified article (give writer opportunity to review)
     */
    public function archive(Article $article)
    {
        $this->authorize('update', $article);

        $article->update(['status' => 'archived']);

        ActivityLogHelper::logArticle(
            'article.archived',
            $article,
            "Artikel '{$article->title}' diarsipkan oleh admin"
        );

        // Clear article cache to reflect changes immediately
        CacheHelper::clearArticleCache();

        // Return JSON response for AJAX requests
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Artikel berhasil diarsipkan. Penulis dapat mereview kembali tulisannya.',
                'status' => 'archived'
            ]);
        }

        return back()->with('success', 'Artikel berhasil diarsipkan. Penulis dapat mereview kembali tulisannya.');
    }

    /**
     * Tangguhkan penayangan artikel publik (moderasi — bukan edit konten).
     */
    public function suspend(Request $request, Article $article)
    {
        $this->authorize('suspend', $article);

        if (!in_array($article->status, ['published', 'archived'], true)) {
            return back()->with('error', 'Hanya artikel yang sudah tayang (atau diarsipkan) yang dapat ditangguhkan.');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ], [
            'reason.required' => 'Alasan penangguhan wajib diisi.',
        ]);

        $article->update([
            'status' => 'suspended',
            'suspension_reason' => $request->reason,
            'suspended_at' => now(),
            'suspended_by' => Auth::id(),
            'is_featured' => false,
            'is_breaking' => false,
        ]);

        CacheHelper::clearArticleCache();
        CacheHelper::clearDashboardCache();

        ActivityLogHelper::logArticle(
            'article.suspended',
            $article,
            "Penayangan '{$article->title}' ditangguhkan: {$request->reason}"
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Penayangan artikel ditangguhkan.',
                'status' => 'suspended',
            ]);
        }

        return redirect()
            ->route('admin.articles.detail', $article)
            ->with('success', 'Penayangan artikel ditangguhkan.');
    }

    /**
     * Pulihkan penayangan artikel yang ditangguhkan.
     */
    public function unsuspend(Article $article)
    {
        $this->authorize('suspend', $article);

        if ($article->status !== 'suspended') {
            return back()->with('error', 'Artikel ini tidak dalam status ditangguhkan.');
        }

        $article->update([
            'status' => 'published',
            'published_at' => $article->published_at ?? now(),
            'suspension_reason' => null,
            'suspended_at' => null,
            'suspended_by' => null,
        ]);

        CacheHelper::clearArticleCache();
        CacheHelper::clearDashboardCache();

        ActivityLogHelper::logArticle(
            'article.unsuspended',
            $article,
            "Penayangan '{$article->title}' dipulihkan"
        );

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Penayangan artikel dipulihkan.',
                'status' => 'published',
            ]);
        }

        return back()->with('success', 'Penayangan artikel dipulihkan.');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Article $article)
    {
        $this->authorize('curate', $article);

        $article->update(['is_featured' => !$article->is_featured]);
        
        // Clear article cache to reflect changes immediately
        CacheHelper::clearArticleCache();
        
        $status = $article->is_featured ? 'ditandai sebagai' : 'dihapus dari';
        
        // Return JSON response for AJAX requests
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Artikel {$status} featured!",
                'is_featured' => $article->is_featured
            ]);
        }
        
        return back()->with('success', "Artikel {$status} featured!");
    }

    /**
     * Toggle breaking news status
     */
    public function toggleBreaking(Article $article)
    {
        $this->authorize('curate', $article);

        $article->update(['is_breaking' => !$article->is_breaking]);
        
        // Clear article cache to reflect changes immediately
        CacheHelper::clearArticleCache();
        
        $status = $article->is_breaking ? 'ditandai sebagai' : 'dihapus dari';
        
        // Return JSON response for AJAX requests
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Artikel {$status} breaking news!",
                'is_breaking' => $article->is_breaking
            ]);
        }
        
        return back()->with('success', "Artikel {$status} breaking news!");
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,publish,draft,featured',
            'articles' => 'required|array',
            'articles.*' => 'exists:articles,id',
        ]);

        try {
            $articleIds = $request->articles;
            $articles = Article::whereIn('id', $articleIds)->get();

            // Publish pending = moderasi (boleh artikel penulis). Featured = curate (admin/editor).
            // Mutasi lain = hanya artikel milik admin.
            if ($request->action === 'publish') {
                $allowed = $articles;
                $skippedOwnership = 0;
            } elseif ($request->action === 'featured') {
                $allowed = $articles->filter(fn (Article $article) => Auth::user()->can('curate', $article));
                $skippedOwnership = $articles->count() - $allowed->count();
            } else {
                $ability = $request->action === 'delete' ? 'delete' : 'update';
                $allowed = $articles->filter(fn (Article $article) => Auth::user()->can($ability, $article));
                $skippedOwnership = $articles->count() - $allowed->count();
            }

            $articles = $allowed;
            $articleIds = $articles->pluck('id')->all();

            if ($articles->isEmpty()) {
                $errorMessage = $skippedOwnership > 0
                    ? 'Tidak ada artikel yang boleh diubah. Artikel penulis hanya bisa dimoderasi lewat approve/reject.'
                    : 'Tidak ada artikel yang ditemukan.';
                
                // Return JSON for AJAX requests
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 422);
                }
                
                return back()->with('error', $errorMessage);
            }

            switch ($request->action) {
                case 'delete':
                    DB::beginTransaction();
                    try {
                        $count = $articles->count();
                        
                        // Get all comment IDs for these articles
                        $commentIds = Comment::whereIn('article_id', $articleIds)->pluck('id');
                        
                        // Delete comment likes first
                        if ($commentIds->isNotEmpty()) {
                            CommentLike::whereIn('comment_id', $commentIds)->delete();
                        }
                        
                        // Delete comments
                        Comment::whereIn('article_id', $articleIds)->delete();
                        
                        // Delete bookmarks
                        Bookmark::whereIn('article_id', $articleIds)->delete();
                        
                        // Delete reading history
                        ReadingHistory::whereIn('article_id', $articleIds)->delete();
                        
                        // Delete featured images and detach tags for each article
                        $articles->each(function ($article) {
                            // Delete featured image
                            if ($article->featured_image) {
                                try {
                                    Storage::disk('public')->delete($article->featured_image);
                                } catch (\Exception $e) {
                                    \Log::warning('Failed to delete featured image for article ' . $article->id . ': ' . $e->getMessage());
                                }
                            }
                            
                            // Detach tags
                            $article->tags()->detach();
                        });
                        
                        // Delete articles
                        Article::whereIn('id', $articleIds)->delete();
                        
                        DB::commit();
                        
                        // Clear cache
                        CacheHelper::clearArticleCache();
                        CacheHelper::clearDashboardCache();
                        
                        // Log activity
                        ActivityLogHelper::log('article.bulk_deleted', $count . ' artikel dihapus secara massal', [
                            'count' => $count,
                            'skipped_ownership' => $skippedOwnership,
                            'article_ids' => $articleIds
                        ]);
                        
                        $message = $count . ' artikel berhasil dihapus!';
                        if ($skippedOwnership > 0) {
                            $message .= " {$skippedOwnership} dilewati (bukan milik Anda).";
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        throw $e;
                    }
                    break;

                case 'publish':
                    // Admin boleh publikasi massal draft (bukan suspended).
                    $publishable = $articles->where('status', 'draft');
                    $skipped = $articles->count() - $publishable->count();
                    $publishedCount = 0;
                    $scheduledCount = 0;

                    foreach ($publishable as $article) {
                        $hasFutureSchedule = $article->scheduled_at && $article->scheduled_at->isFuture();

                        if ($hasFutureSchedule) {
                            $article->update([
                                'status' => 'draft',
                                'published_at' => null,
                            ]);
                            $scheduledCount++;
                        } else {
                            $article->update([
                                'status' => 'published',
                                'published_at' => $article->published_at ?? now(),
                                'scheduled_at' => null,
                            ]);
                            $publishedCount++;
                        }
                    }

                    $count = $publishedCount + $scheduledCount;

                    CacheHelper::clearArticleCache();
                    CacheHelper::clearDashboardCache();

                    ActivityLogHelper::log('article.bulk_published', $count . ' artikel dipublikasi secara massal', [
                        'count' => $count,
                        'published' => $publishedCount,
                        'scheduled' => $scheduledCount,
                        'skipped' => $skipped,
                        'article_ids' => $publishable->pluck('id')->all(),
                    ]);

                    if ($count === 0) {
                        $message = 'Tidak ada draft yang dapat dipublikasi massal.';
                    } elseif ($scheduledCount > 0) {
                        $message = "{$publishedCount} dipublikasi, {$scheduledCount} tetap dijadwalkan.";
                        if ($skipped > 0) {
                            $message .= " {$skipped} dilewati.";
                        }
                    } elseif ($skipped > 0) {
                        $message = "{$count} artikel dipublikasi. {$skipped} dilewati.";
                    } else {
                        $message = $count . ' artikel berhasil dipublikasi!';
                    }
                    break;

                case 'draft':
                    $count = 0;
                    foreach ($articles as $article) {
                        $article->update([
                            'status' => 'draft',
                        ]);
                        $count++;
                    }

                    CacheHelper::clearArticleCache();
                    CacheHelper::clearDashboardCache();

                    ActivityLogHelper::log('article.bulk_drafted', $count . ' artikel diubah ke draft secara massal', [
                        'count' => $count,
                        'skipped_ownership' => $skippedOwnership,
                        'article_ids' => $articleIds,
                    ]);

                    $message = $count . ' artikel berhasil diubah ke draft!';
                    if ($skippedOwnership > 0) {
                        $message .= " {$skippedOwnership} dilewati (bukan milik Anda).";
                    }
                    break;

                case 'featured':
                    $count = Article::whereIn('id', $articleIds)->update(['is_featured' => true]);
                    
                    // Clear cache
                    CacheHelper::clearArticleCache();
                    
                    // Log activity
                    ActivityLogHelper::log('article.bulk_featured', $count . ' artikel ditandai sebagai featured secara massal', [
                        'count' => $count,
                        'skipped_ownership' => $skippedOwnership,
                        'article_ids' => $articleIds
                    ]);
                    
                    $message = $count . ' artikel berhasil ditandai sebagai featured!';
                    if ($skippedOwnership > 0) {
                        $message .= " {$skippedOwnership} dilewati (bukan milik Anda).";
                    }
                    break;
            }

            // Return JSON for AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }
            
            return back()->with('success', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => collect($e->errors())->flatten()->first() ?? 'Validasi gagal.',
                    'errors' => $e->errors(),
                ], 422);
            }

            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Bulk Action Error: ' . $e->getMessage(), [
                'action' => $request->action,
                'article_ids' => $request->articles,
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'Terjadi kesalahan saat memproses aksi bulk: ' . $e->getMessage();
            
            // Return JSON for AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Merge checkbox tag IDs with newly typed tag names.
     *
     * @return array<int, int>
     */
    private function resolveTagIds(Request $request): array
    {
        $tagIds = collect($request->input('tags', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->all();

        if (!$request->filled('new_tags')) {
            return array_values(array_unique($tagIds));
        }

        $names = collect(explode(',', $request->new_tags))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique(fn ($name) => mb_strtolower($name));

        foreach ($names as $name) {
            if (mb_strlen($name) > 50) {
                $name = mb_substr($name, 0, 50);
            }

            $tag = Tag::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();

            if (!$tag) {
                $baseSlug = Str::slug($name) ?: 'tag';
                $slug = $baseSlug;
                $counter = 1;
                while (Tag::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $tag = Tag::create([
                    'name' => $name,
                    'slug' => $slug,
                ]);
            }

            $tagIds[] = $tag->id;
        }

        return array_values(array_unique($tagIds));
    }
}
