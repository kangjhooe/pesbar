<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Bookmark;
use App\Models\ReadingHistory;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserFeatureController extends Controller
{
    /**
     * Toggle bookmark for an article
     */
    public function toggleBookmark(Article $article)
    {
        $user = Auth::user();

        $bookmark = Bookmark::where('user_id', $user->id)
            ->where('article_id', $article->id)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            return response()->json([
                'success' => true,
                'bookmarked' => false,
                'message' => 'Bookmark dihapus'
            ]);
        } else {
            Bookmark::create([
                'user_id' => $user->id,
                'article_id' => $article->id,
            ]);
            return response()->json([
                'success' => true,
                'bookmarked' => true,
                'message' => 'Artikel di-bookmark'
            ]);
        }
    }

    /**
     * Get user's bookmarks
     */
    public function bookmarks(Request $request)
    {
        $user = Auth::user();

        $bookmarks = Bookmark::where('user_id', $user->id)
            ->with(['article' => function($query) {
                $query->with(['author', 'category']);
            }])
            ->whereHas('article') // Only show bookmarks for existing articles
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('user.bookmarks', compact('bookmarks'));
    }

    /**
     * Track reading history
     */
    public function trackReading(Article $article)
    {
        $user = Auth::user();

        // Check if already exists in reading history
        $existing = ReadingHistory::where('user_id', $user->id)
            ->where('article_id', $article->id)
            ->first();

        if ($existing) {
            // Update read_at timestamp
            $existing->update(['read_at' => now()]);
        } else {
            // Create new reading history entry
            ReadingHistory::create([
                'user_id' => $user->id,
                'article_id' => $article->id,
                'read_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get user's reading history
     */
    public function readingHistory(Request $request)
    {
        $user = Auth::user();

        $history = ReadingHistory::where('user_id', $user->id)
            ->with(['article' => function($query) {
                $query->with(['author', 'category']);
            }])
            ->whereHas('article') // Only show history for existing articles
            ->orderBy('read_at', 'desc')
            ->paginate(12);

        return view('user.reading-history', compact('history'));
    }

    /**
     * Toggle follow for an author
     */
    public function toggleFollow(User $user)
    {
        $currentUser = Auth::user();

        // Prevent users from following themselves
        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak bisa mengikuti diri sendiri'
            ], 400);
        }

        $follow = Follow::where('follower_id', $currentUser->id)
            ->where('following_id', $user->id)
            ->first();

        if ($follow) {
            $follow->delete();
            return response()->json([
                'success' => true,
                'following' => false,
                'message' => 'Berhenti mengikuti'
            ]);
        } else {
            Follow::create([
                'follower_id' => $currentUser->id,
                'following_id' => $user->id,
            ]);
            return response()->json([
                'success' => true,
                'following' => true,
                'message' => 'Mengikuti penulis'
            ]);
        }
    }

    /**
     * Get authors that user follows
     */
    public function following(Request $request)
    {
        $user = Auth::user();

        $follows = Follow::where('follower_id', $user->id)
            ->with(['following' => function($query) {
                $query->withCount('articles');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('user.following', compact('follows'));
    }

    /**
     * Get user's followers
     */
    public function followers(Request $request)
    {
        $user = Auth::user();

        $followers = Follow::where('following_id', $user->id)
            ->with(['follower'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('user.followers', compact('followers'));
    }
}
