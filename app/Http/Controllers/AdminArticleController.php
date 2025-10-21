<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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
        $query = Article::with(['category', 'user', 'tags']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->where('category_id', $request->category);
        }

        // Search by title
        if ($request->has('search') && $request->search !== '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = Category::where('is_active', true)->get();

        return view('admin.articles.index', compact('articles', 'categories'));
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
        ]);

        $data = $request->all();
        $data['author_id'] = Auth::id();
        $data['slug'] = Str::slug($request->title);
        
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
        $article->load(['category', 'user', 'tags', 'comments']);
        
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
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        
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
        // Delete featured image
        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }

        // Detach tags
        $article->tags()->detach();

        $article->delete();

        return redirect()->back()
            ->with('success', 'Artikel berhasil dihapus!');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Article $article)
    {
        $article->update(['is_featured' => !$article->is_featured]);
        
        $status = $article->is_featured ? 'ditandai sebagai' : 'dihapus dari';
        
        return back()->with('success', "Artikel {$status} featured!");
    }

    /**
     * Toggle breaking news status
     */
    public function toggleBreaking(Article $article)
    {
        $article->update(['is_breaking' => !$article->is_breaking]);
        
        $status = $article->is_breaking ? 'ditandai sebagai' : 'dihapus dari';
        
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

        $articles = Article::whereIn('id', $request->articles);

        switch ($request->action) {
            case 'delete':
                // Delete featured images
                $articles->get()->each(function ($article) {
                    if ($article->featured_image) {
                        Storage::disk('public')->delete($article->featured_image);
                    }
                });
                $articles->delete();
                $message = 'Artikel berhasil dihapus!';
                break;

            case 'publish':
                $articles->update([
                    'status' => 'published',
                    'published_at' => now()
                ]);
                $message = 'Artikel berhasil dipublikasi!';
                break;

            case 'draft':
                $articles->update(['status' => 'draft']);
                $message = 'Artikel berhasil diubah ke draft!';
                break;

            case 'featured':
                $articles->update(['is_featured' => true]);
                $message = 'Artikel berhasil ditandai sebagai featured!';
                break;
        }

        return back()->with('success', $message);
    }
}
