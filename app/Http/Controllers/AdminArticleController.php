<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CacheHelper;
use App\Helpers\ActivityLogHelper;

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

        $articles = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->get();

        return view('admin.articles.index', compact('articles', 'categories', 'tab'));
    }

    /**
     * Show the form for creating a new article
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $tags = Tag::all();
        
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
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
            'type' => 'required|in:berita,artikel',
            'is_featured' => 'boolean',
            'is_breaking' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'published_at' => 'nullable|date',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
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

        // Attach tags
        if ($request->has('tags')) {
            $article->tags()->attach($request->tags);
        }

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
        $tags = Tag::all();
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
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
            'type' => 'required|in:berita,artikel',
            'is_featured' => 'boolean',
            'is_breaking' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'published_at' => 'nullable|date',
            'slug' => 'nullable|string|max:255|unique:articles,slug,' . $article->id,
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        
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

        $article->update($data);

        // Sync tags
        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        } else {
            $article->tags()->detach();
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui!');
    }

    /**
     * Remove the specified article
     */
    public function destroy(Article $article)
    {
        try {
            $articleTitle = $article->title;
            
            // Delete featured image
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }

            // Detach tags
            $article->tags()->detach();

            $article->delete();

            // Clear cache
            CacheHelper::clearArticleCache();
            CacheHelper::clearDashboardCache();
            
            // Log activity
            ActivityLogHelper::log('article', 'deleted', 'Artikel dihapus: ' . $articleTitle);

            return redirect()->back()
                ->with('success', 'Artikel berhasil dihapus!');
        } catch (\Exception $e) {
            \Log::error('Article Deletion Error: ' . $e->getMessage(), [
                'article_id' => $article->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus artikel. Silakan coba lagi.');
        }
    }

    /**
     * Archive the specified article (give writer opportunity to review)
     */
    public function archive(Article $article)
    {
        $article->update(['status' => 'archived']);

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
     * Toggle featured status
     */
    public function toggleFeatured(Article $article)
    {
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

            if ($articles->isEmpty()) {
                return back()->with('error', 'Tidak ada artikel yang ditemukan.');
            }

            switch ($request->action) {
                case 'delete':
                    $count = $articles->count();
                    
                    // Delete featured images and detach tags for each article
                    $articles->each(function ($article) {
                        // Delete featured image
                        if ($article->featured_image) {
                            Storage::disk('public')->delete($article->featured_image);
                        }
                        
                        // Detach tags
                        $article->tags()->detach();
                    });
                    
                    // Delete articles
                    Article::whereIn('id', $articleIds)->delete();
                    
                    // Clear cache
                    CacheHelper::clearArticleCache();
                    CacheHelper::clearDashboardCache();
                    
                    // Log activity
                    ActivityLogHelper::log('article', 'bulk_deleted', $count . ' artikel dihapus secara massal');
                    
                    $message = $count . ' artikel berhasil dihapus!';
                    break;

                case 'publish':
                    $count = Article::whereIn('id', $articleIds)->update([
                        'status' => 'published',
                        'published_at' => now()
                    ]);
                    
                    // Clear cache
                    CacheHelper::clearArticleCache();
                    CacheHelper::clearDashboardCache();
                    
                    // Log activity
                    ActivityLogHelper::log('article', 'bulk_published', $count . ' artikel dipublikasi secara massal');
                    
                    $message = $count . ' artikel berhasil dipublikasi!';
                    break;

                case 'draft':
                    $count = Article::whereIn('id', $articleIds)->update(['status' => 'draft']);
                    
                    // Clear cache
                    CacheHelper::clearArticleCache();
                    CacheHelper::clearDashboardCache();
                    
                    // Log activity
                    ActivityLogHelper::log('article', 'bulk_drafted', $count . ' artikel diubah ke draft secara massal');
                    
                    $message = $count . ' artikel berhasil diubah ke draft!';
                    break;

                case 'featured':
                    $count = Article::whereIn('id', $articleIds)->update(['is_featured' => true]);
                    
                    // Clear cache
                    CacheHelper::clearArticleCache();
                    
                    // Log activity
                    ActivityLogHelper::log('article', 'bulk_featured', $count . ' artikel ditandai sebagai featured secara massal');
                    
                    $message = $count . ' artikel berhasil ditandai sebagai featured!';
                    break;
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Bulk Action Error: ' . $e->getMessage(), [
                'action' => $request->action,
                'article_ids' => $request->articles,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat memproses aksi bulk. Silakan coba lagi.');
        }
    }
}
