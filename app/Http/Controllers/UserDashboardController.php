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
        
        // Get user's comments (based on email since comments don't have user_id)
        $commentsQuery = Comment::where('email', $user->email)
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
            'total_comments' => Comment::where('email', $user->email)->count(),
            'approved_comments' => Comment::where('email', $user->email)->where('is_approved', true)->count(),
            'pending_comments' => Comment::where('email', $user->email)->where('is_approved', false)->count(),
        ];
        
        // Recent articles (for reference)
        $recentArticles = Article::where('status', 'published')
            ->with('category')
            ->latest('published_at')
            ->limit(5)
            ->get();
        
        return view('user.dashboard', compact('comments', 'stats', 'recentArticles'));
    }
}

