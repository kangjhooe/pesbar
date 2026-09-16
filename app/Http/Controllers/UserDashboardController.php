<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Bookmark;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\ReadingHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $commentsBase = Comment::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('email', $user->email);
        })->whereHas('article');

        $commentsQuery = (clone $commentsBase)
            ->with(['article' => function ($query) {
                $query->select('id', 'title', 'slug', 'category_id')
                    ->with('category:id,name,slug');
            }])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $commentsQuery->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $commentsQuery->where('is_approved', false);
            }
        }

        $comments = $commentsQuery->paginate(15)->withQueryString();

        $stats = [
            'total_comments' => (clone $commentsBase)->count(),
            'approved_comments' => (clone $commentsBase)->where('is_approved', true)->count(),
            'pending_comments' => (clone $commentsBase)->where('is_approved', false)->count(),
            'bookmarks' => Bookmark::where('user_id', $user->id)->whereHas('article')->count(),
            'reading_history' => ReadingHistory::where('user_id', $user->id)->whereHas('article')->count(),
            'following' => Follow::where('follower_id', $user->id)->count(),
        ];

        $recentArticles = Article::where('status', 'published')
            ->with('category')
            ->latest('published_at')
            ->limit(5)
            ->get();

        return view('user.dashboard', compact('comments', 'stats', 'recentArticles'));
    }

    public function updateComment(Request $request, Comment $comment)
    {
        $user = Auth::user();

        if (!$this->ownsComment($comment, $user)) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit komentar ini.');
        }

        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment->update([
            'comment' => $request->comment,
            'is_approved' => false,
        ]);

        return redirect()->route('user.dashboard')
            ->with('success', 'Komentar berhasil diperbarui. Komentar akan ditinjau ulang oleh admin.');
    }

    public function destroyComment(Comment $comment)
    {
        $user = Auth::user();

        if (!$this->ownsComment($comment, $user)) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus komentar ini.');
        }

        $comment->delete();

        return redirect()->route('user.dashboard')
            ->with('success', 'Komentar berhasil dihapus.');
    }

    /**
     * Prefer user_id; only fall back to email for legacy guest comments (null user_id).
     */
    private function ownsComment(Comment $comment, $user): bool
    {
        if ($comment->user_id !== null) {
            return (int) $comment->user_id === (int) $user->id;
        }

        return $comment->email !== null
            && strcasecmp((string) $comment->email, (string) $user->email) === 0;
    }
}
