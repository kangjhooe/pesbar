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
    public function index()
    {
        $user = Auth::user();

        $commentsBase = Comment::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('email', $user->email);
        })->whereHas('article');

        $comments = (clone $commentsBase)
            ->with(['article' => function ($query) {
                $query->select('id', 'title', 'slug', 'category_id')
                    ->with('category:id,name,slug');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_comments' => (clone $commentsBase)->count(),
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

    public function submitBanAppeal(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'message' => 'required|string|min:20|max:1000',
        ]);

        try {
            $user->submitBanAppeal($request->input('message'));
            return redirect()->route('user.dashboard')
                ->with('success', 'Banding berhasil dikirim. Admin akan meninjau permintaan Anda.');
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
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
        ]);

        return redirect()->route('user.dashboard')
            ->with('success', 'Komentar berhasil diperbarui.');
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
