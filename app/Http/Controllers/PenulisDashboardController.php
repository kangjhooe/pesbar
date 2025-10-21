<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PenulisDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $articles = $user->articles()->with('category')->latest()->paginate(10);
        
        $stats = [
            'total_articles' => $user->articles()->count(),
            'published_articles' => $user->articles()->where('status', 'published')->count(),
            'pending_articles' => $user->articles()->where('status', 'pending_review')->count(),
            'total_views' => $user->articles()->sum('views'),
            'total_comments' => $user->articles()->withCount('comments')->get()->sum('comments_count'),
        ];

        return view('penulis.dashboard', compact('articles', 'stats'));
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
        ]);

        $user = Auth::user();
        $status = $user->isVerified() ? 'published' : 'pending_review';

        $article = $user->articles()->create([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'status' => $status,
            'featured_image' => $request->hasFile('featured_image') 
                ? $request->file('featured_image')->store('articles', 'public') 
                : null,
        ]);

        // Handle tags
        if ($request->tags) {
            $tagNames = array_map('trim', explode(',', $request->tags));
            foreach ($tagNames as $tagName) {
                $tag = \App\Models\Tag::firstOrCreate(['name' => $tagName]);
                $article->tags()->attach($tag);
            }
        }

        return redirect()->route('penulis.dashboard')->with('success', 'Artikel berhasil dibuat!');
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
        ]);

        $user = Auth::user();
        $status = $user->isVerified() ? 'published' : 'pending_review';

        $article->update([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'status' => $status,
        ]);

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
                $tag = \App\Models\Tag::firstOrCreate(['name' => $tagName]);
                $article->tags()->attach($tag);
            }
        }

        return redirect()->route('penulis.dashboard')->with('success', 'Artikel berhasil diperbarui!');
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
}
