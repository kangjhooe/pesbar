<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PenulisDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Build query with filters
        $query = $user->articles()->with(['category', 'tags'])->withCount('comments');
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Search by title
        if ($request->has('search') && $request->search !== '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->where('category_id', $request->category);
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if ($sortBy === 'views') {
            $query->orderBy('views', $sortOrder);
        } elseif ($sortBy === 'title') {
            $query->orderBy('title', $sortOrder);
        } else {
            $query->orderBy('created_at', $sortOrder);
        }
        
        $articles = $query->paginate(15)->withQueryString();
        
        // Enhanced stats
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
        
        // Get categories for filter
        $categories = \App\Models\Category::where('is_active', true)->get();
        
        // Get popular articles
        $popularArticles = $user->articles()
            ->where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        return view('penulis.dashboard', compact('articles', 'stats', 'categories', 'popularArticles'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('penulis.articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string',
            'save_as_draft' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        
        // Determine status
        if ($request->has('save_as_draft') && $request->save_as_draft) {
            $status = 'draft';
        } else {
            $status = $user->isVerified() ? 'published' : 'pending_review';
        }

        $article = $user->articles()->create([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'status' => $status,
            'featured_image' => $request->hasFile('featured_image') 
                ? $request->file('featured_image')->store('articles', 'public') 
                : null,
            'published_at' => $status === 'published' ? now() : null,
        ]);

        // Handle tags
        if ($request->tags) {
            $tagNames = array_map('trim', explode(',', $request->tags));
            foreach ($tagNames as $tagName) {
                if (!empty($tagName)) {
                    $tag = \App\Models\Tag::firstOrCreate(['name' => $tagName]);
                    $article->tags()->attach($tag);
                }
            }
        }

        $message = $status === 'draft' ? 'Draft artikel berhasil disimpan!' : 'Artikel berhasil dibuat!';
        return redirect()->route('penulis.dashboard')->with('success', $message);
    }

    public function edit(Article $article)
    {
        $this->authorize('update', $article);
        $categories = \App\Models\Category::all();
        return view('penulis.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, Article $article)
    {
        $this->authorize('update', $article);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string',
            'save_as_draft' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        
        // Determine status - don't change if already published, unless saving as draft
        if ($request->has('save_as_draft') && $request->save_as_draft) {
            $status = 'draft';
        } elseif ($article->status === 'published') {
            // Keep published status if already published
            $status = 'published';
        } else {
            // For pending/rejected/draft, set based on verification
            $status = $user->isVerified() ? 'published' : 'pending_review';
        }

        $updateData = [
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'status' => $status,
        ];

        // Set published_at if publishing for the first time
        if ($status === 'published' && !$article->published_at) {
            $updateData['published_at'] = now();
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
        $article->tags()->detach();
        if ($request->tags) {
            $tagNames = array_map('trim', explode(',', $request->tags));
            foreach ($tagNames as $tagName) {
                if (!empty($tagName)) {
                    $tag = \App\Models\Tag::firstOrCreate(['name' => $tagName]);
                    $article->tags()->attach($tag);
                }
            }
        }

        $message = $status === 'draft' ? 'Draft artikel berhasil diperbarui!' : 'Artikel berhasil diperbarui!';
        return redirect()->route('penulis.dashboard')->with('success', $message);
    }

    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);
        
        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }
        
        $article->delete();
        return redirect()->route('penulis.dashboard')->with('success', 'Artikel berhasil dihapus!');
    }

    public function profile()
    {
        $user = Auth::user();
        $profile = $user->profile;
        return view('penulis.profile', compact('profile'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'website' => 'nullable|url',
            'location' => 'nullable|string|max:255',
            'social_links' => 'nullable|array',
        ]);

        $user = Auth::user();
        $profile = $user->profile;

        $data = [
            'bio' => $request->bio,
            'website' => $request->website,
            'location' => $request->location,
            'social_links' => $request->social_links,
        ];

        if ($request->hasFile('avatar')) {
            if ($profile && $profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($profile) {
            $profile->update($data);
        } else {
            $user->profile()->create($data);
        }

        return redirect()->route('penulis.profile')->with('success', 'Profil berhasil diperbarui!');
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
        $this->authorize('view', $article);
        
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
        $this->authorize('view', $article);
        
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
            'content' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        if ($article) {
            $this->authorize('update', $article);
        }

        $data = [
            'title' => $request->title ?? 'Draft tanpa judul',
            'content' => $request->content ?? '',
            'category_id' => $request->category_id,
            'status' => 'draft',
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
        if ($article->exists) {
            $article->tags()->detach();
            if ($request->tags) {
                $tagNames = array_map('trim', explode(',', $request->tags));
                foreach ($tagNames as $tagName) {
                    if (!empty($tagName)) {
                        $tag = \App\Models\Tag::firstOrCreate(['name' => $tagName]);
                        $article->tags()->attach($tag);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Draft berhasil disimpan!',
            'article_id' => $article->id
        ]);
    }
}
