<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get user's comments (prefer user_id, fallback to email for old comments)
        $commentsQuery = Comment::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('email', $user->email);
            })
            ->with(['article' => function($query) {
                $query->select('id', 'title', 'slug', 'category_id')
                      ->with('category:id,name');
            }])
            ->orderBy('created_at', 'desc');
        
        // Filter by approval status
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'approved') {
                $commentsQuery->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $commentsQuery->where('is_approved', false);
            }
        }
        
        $comments = $commentsQuery->paginate(15)->withQueryString();
        
        // Stats
        $stats = [
            'total_comments' => Comment::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('email', $user->email);
            })->count(),
            'approved_comments' => Comment::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('email', $user->email);
            })->where('is_approved', true)->count(),
            'pending_comments' => Comment::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('email', $user->email);
            })->where('is_approved', false)->count(),
        ];
        
        // Recent articles (for reference)
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
        
        // Verify comment belongs to user
        if ($comment->user_id !== $user->id && $comment->email !== $user->email) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit komentar ini.');
        }

        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment->update([
            'comment' => $request->comment,
            'is_approved' => false, // Reset approval status when edited
        ]);

        return redirect()->route('user.dashboard')
            ->with('success', 'Komentar berhasil diperbarui. Komentar akan ditinjau ulang oleh admin.');
    }

    public function destroyComment(Comment $comment)
    {
        $user = Auth::user();
        
        // Verify comment belongs to user
        if ($comment->user_id !== $user->id && $comment->email !== $user->email) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus komentar ini.');
        }

        $comment->delete();

        return redirect()->route('user.dashboard')
            ->with('success', 'Komentar berhasil dihapus.');
    }
}

