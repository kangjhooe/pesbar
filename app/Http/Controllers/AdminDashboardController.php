<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\ReadingHistory;
use App\Models\User;
use App\Helpers\ActivityLogHelper;
use App\Helpers\AdminTableHelper;
use App\Helpers\CacheHelper;
use App\Services\BackupService;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $pendingCommentsCount = Comment::where('is_approved', false)->count();
        $pendingArticlesCount = Article::where('status', 'pending_review')->count();

        $stats = [
            'total_users' => User::count(),
            'total_penulis' => User::where('role', 'penulis')->count(),
            'total_articles' => Article::count(),
            'pending_articles' => $pendingArticlesCount,
            'published_articles' => Article::where('status', 'published')->count(),
            'draft_articles' => Article::where('status', 'draft')->count(),
            'rejected_articles' => Article::where('status', 'rejected')->count(),
            'total_views' => (int) Article::sum('views'),
            'total_categories' => \App\Models\Category::count(),
            'total_comments' => Comment::count(),
            'pending_comments' => $pendingCommentsCount,
            'newsletter_subscribers' => \App\Models\NewsletterSubscriber::count(),
            'articles_today' => Article::whereDate('created_at', today())->count(),
            'reads_today' => ReadingHistory::whereDate('read_at', today())->count(),
            'comments_today' => Comment::whereDate('created_at', today())->count(),
            'pending_verification_requests' => User::where('role', 'penulis')
                ->where('verification_request_status', 'pending')
                ->count(),
            'verified_penulis' => User::where('role', 'penulis')
                ->where('verified', true)
                ->count(),
        ];

        $monthlyStats = [
            'articles_this_month' => Article::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'users_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartData[] = [
                'date' => $date->format('d/m'),
                'articles' => Article::whereDate('created_at', $date)->count(),
                'comments' => Comment::whereDate('created_at', $date)->count(),
            ];
        }

        $recent_articles = Article::with(['author', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $recent_comments = Comment::with('article')
            ->latest()
            ->limit(5)
            ->get();

        $recent_activity = collect();

        $recent_articles->take(5)->each(function ($article) use ($recent_activity) {
            $recent_activity->push([
                'type' => 'article',
                'action' => $article->status === 'published' ? 'menerbitkan artikel' : 'membuat artikel',
                'title' => $article->title,
                'user' => $article->author->name ?? 'Sistem',
                'time' => $article->created_at,
                'color' => $article->status === 'published' ? 'green' : ($article->status === 'pending_review' ? 'yellow' : 'gray'),
            ]);
        });

        $recent_comments->each(function ($comment) use ($recent_activity) {
            $recent_activity->push([
                'type' => 'comment',
                'action' => 'berkomentar',
                'title' => $comment->article->title ?? 'Artikel',
                'user' => $comment->name ?? 'Anonim',
                'time' => $comment->created_at,
                'color' => 'blue',
            ]);
        });

        $recent_activity = $recent_activity->sortByDesc('time')->take(10)->values();

        return view('admin.dashboard', compact(
            'stats',
            'monthlyStats',
            'chartData',
            'recent_articles',
            'recent_activity',
            'pendingCommentsCount',
            'pendingArticlesCount'
        ));
    }

    public function users(Request $request)
    {
        $query = User::with('profile');
        $query = AdminTableHelper::applySort($query, $request, [
            'name' => 'name',
            'role' => 'role',
            'verified' => 'verified',
            'created_at' => 'created_at',
        ], 'created_at', 'desc');

        $users = $query->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function bulkUsers(Request $request)
    {
        $request->validate([
            'action' => 'required|in:verify,unverify,upgrade',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:users,id',
        ]);

        $users = User::whereIn('id', $request->ids)->get();
        $count = 0;

        foreach ($users as $user) {
            if ($request->action === 'verify' && !$user->verified) {
                $user->update(['verified' => true]);
                $count++;
            } elseif ($request->action === 'unverify' && $user->verified) {
                $user->update(['verified' => false]);
                $count++;
            } elseif ($request->action === 'upgrade' && $user->role === 'user') {
                $user->update(['role' => 'penulis']);
                $count++;
            }
        }

        return redirect()->back()->with('success', "{$count} pengguna berhasil diproses.");
    }

    public function upgradeUser(User $user)
    {
        try {
            if ($user->role === 'user') {
                $user->update(['role' => 'penulis']);
                ActivityLogHelper::logUser('user.upgraded', $user, "User {$user->name} diupgrade menjadi penulis");
                return redirect()->back()->with('success', 'User berhasil diupgrade menjadi penulis!');
            }
            
            return redirect()->back()->with('error', 'User sudah memiliki role yang lebih tinggi!');
        } catch (\Exception $e) {
            \Log::error('Upgrade User Error: ' . $e->getMessage());
            ActivityLogHelper::logSecurity('user.upgrade.failed', 'Gagal upgrade user', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupgrade user.');
        }
    }

    public function verificationRequests(Request $request)
    {
        $query = User::where('role', 'penulis')
            ->where('verification_request_status', 'pending')
            ->with('profile')
            ->withCount('articles');

        $query = AdminTableHelper::applySort($query, $request, [
            'name' => 'name',
            'verification_type' => 'verification_type',
            'articles_count' => 'articles_count',
            'verification_requested_at' => 'verification_requested_at',
        ], 'verification_requested_at', 'desc');

        $requests = $query->paginate(15)->withQueryString();

        return view('admin.verification-requests', compact('requests'));
    }

    public function bulkVerificationRequests(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:users,id',
        ]);

        $users = User::whereIn('id', $request->ids)
            ->where('role', 'penulis')
            ->where('verification_request_status', 'pending')
            ->get();

        $count = 0;
        foreach ($users as $user) {
            if ($request->action === 'approve') {
                $user->update([
                    'verified' => true,
                    'verification_request_status' => 'approved',
                ]);
                $user->articles()
                    ->where('status', 'pending_review')
                    ->update([
                        'status' => 'published',
                        'published_at' => now(),
                    ]);
            } else {
                $user->update([
                    'verification_request_status' => 'rejected',
                ]);
            }
            $count++;
        }

        $label = $request->action === 'approve' ? 'disetujui' : 'ditolak';
        return redirect()->back()->with('success', "{$count} permintaan verifikasi berhasil {$label}.");
    }

    public function approveVerification(User $user)
    {
        try {
            // Validasi: hanya penulis dengan pending request yang bisa di-approve
            if ($user->role !== 'penulis' || $user->verification_request_status !== 'pending') {
                return redirect()->back()->with('error', 'Permintaan verifikasi tidak valid!');
            }

            $user->update([
                'verified' => true,
                'verification_request_status' => 'approved',
            ]);

            // Auto-publish artikel pending_review milik penulis ini
            $user->articles()
                ->where('status', 'pending_review')
                ->update([
                    'status' => 'published',
                    'published_at' => now(),
                ]);

            ActivityLogHelper::logUser('verification.approved', $user, "Verifikasi penulis {$user->name} disetujui");
            ActivityLogHelper::logSecurity('verification.approved', 'Verifikasi penulis disetujui', ['user_id' => $user->id]);

            // TODO: Kirim notifikasi ke penulis

            return redirect()->back()->with('success', 'Verifikasi penulis berhasil disetujui!');
        } catch (\Exception $e) {
            \Log::error('Approve Verification Error: ' . $e->getMessage());
            ActivityLogHelper::logSecurity('verification.approve.failed', 'Gagal approve verifikasi', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyetujui verifikasi.');
        }
    }

    public function rejectVerification(Request $request, User $user)
    {
        try {
            // Validasi: hanya penulis dengan pending request yang bisa di-reject
            if ($user->role !== 'penulis' || $user->verification_request_status !== 'pending') {
                return redirect()->back()->with('error', 'Permintaan verifikasi tidak valid!');
            }

            $user->update([
                'verification_request_status' => 'rejected',
            ]);

            ActivityLogHelper::logUser('verification.rejected', $user, "Verifikasi penulis {$user->name} ditolak" . ($request->reason ? ". Alasan: {$request->reason}" : ""));
            ActivityLogHelper::logSecurity('verification.rejected', 'Verifikasi penulis ditolak', ['user_id' => $user->id, 'reason' => $request->reason ?? null]);

            // TODO: Kirim notifikasi ke penulis

            return redirect()->back()->with('success', 'Permintaan verifikasi ditolak.');
        } catch (\Exception $e) {
            \Log::error('Reject Verification Error: ' . $e->getMessage());
            ActivityLogHelper::logSecurity('verification.reject.failed', 'Gagal reject verifikasi', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menolak verifikasi.');
        }
    }

    public function toggleVerified(User $user)
    {
        // Method ini tetap ada untuk backward compatibility, tapi sekarang hanya untuk penulis yang sudah verified
        // Untuk request baru, gunakan approveVerification/rejectVerification
        try {
            if ($user->role !== 'penulis') {
                return redirect()->back()->with('error', 'Hanya penulis yang bisa diverifikasi!');
            }

            $wasVerified = $user->verified;
            $newVerifiedStatus = !$wasVerified;
            
            // Jika membatalkan verifikasi (dari verified ke tidak verified), kembalikan role ke user
            if ($wasVerified && !$newVerifiedStatus) {
                $user->update([
                    'verified' => false,
                    'role' => 'user',
                    'verification_request_status' => null, // Reset status verifikasi request
                ]);
                
                // Refresh user model dari database
                $user->refresh();
                
                $status = 'tidak diverifikasi dan role dikembalikan ke user biasa';
                ActivityLogHelper::logUser('user.verification.toggled', $user, "User {$user->name} {$status}");
                
                // Jika user yang sedang login adalah user yang role-nya berubah, redirect ke user dashboard
                if (Auth::check() && Auth::id() === $user->id) {
                    // Refresh session untuk memastikan data terbaru
                    Auth::user()->refresh();
                    return redirect()->route('user.dashboard')->with('success', "Verifikasi Anda telah dibatalkan. Anda sekarang adalah user biasa.");
                }
                
                return redirect()->back()->with('success', "Verifikasi berhasil dibatalkan dan user dikembalikan menjadi user biasa!");
            } 
            // Jika memberikan verifikasi (dari tidak verified ke verified)
            else {
                $user->update(['verified' => true]);
                $status = 'diverifikasi';
                ActivityLogHelper::logUser('user.verification.toggled', $user, "User {$user->name} {$status}");
                return redirect()->back()->with('success', "User berhasil {$status}!");
            }
        } catch (\Exception $e) {
            \Log::error('Toggle Verified Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah status verifikasi.');
        }
    }

    public function approveArticle(Request $request, Article $article)
    {
        // Authorization check
        if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
            abort(403, 'Anda tidak memiliki izin untuk menyetujui artikel.');
        }
        
        try {
            $article->update([
                'status' => 'published',
                'published_at' => $article->published_at ?? now()
            ]);
            ActivityLogHelper::logArticle('article.approved', $article, "Artikel '{$article->title}' disetujui dan dipublikasikan");
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Artikel berhasil disetujui!']);
            }
            
            return redirect()->back()->with('success', 'Artikel berhasil disetujui!');
        } catch (\Exception $e) {
            \Log::error('Approve Article Error: ' . $e->getMessage());
            ActivityLogHelper::logSecurity('article.approve.failed', 'Gagal approve artikel', ['article_id' => $article->id, 'error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyetujui artikel.'], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyetujui artikel.');
        }
    }

    public function rejectArticle(Request $request, Article $article)
    {
        // Authorization check
        if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
            abort(403, 'Anda tidak memiliki izin untuk menolak artikel.');
        }
        
        try {
            $request->validate([
                'reason' => 'nullable|string|max:1000'
            ]);
            
            $article->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason
            ]);
            
            ActivityLogHelper::logArticle('article.rejected', $article, "Artikel '{$article->title}' ditolak. Alasan: " . ($request->reason ?? 'Tidak ada alasan'));
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Artikel berhasil ditolak!']);
            }
            
            return redirect()->back()->with('success', 'Artikel berhasil ditolak!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Reject Article Error: ' . $e->getMessage());
            ActivityLogHelper::logSecurity('article.reject.failed', 'Gagal reject artikel', ['article_id' => $article->id, 'error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menolak artikel.'], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menolak artikel.');
        }
    }
    
    public function articleDetail(Article $article)
    {
        $article->load(['author.profile', 'category', 'tags']);
        
        return view('admin.articles.detail', compact('article'));
    }
    
    public function bulkApprove(Request $request)
    {
        // Authorization check
        if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
            abort(403, 'Anda tidak memiliki izin untuk menyetujui artikel secara massal.');
        }
        
        try {
            // Handle both array and JSON string formats
            $articleIds = $request->article_ids;
            if (is_string($articleIds)) {
                $articleIds = json_decode($articleIds, true);
            }
            
            // Validate that article_ids is an array and contains only integers
            $request->validate([
                'article_ids' => 'required|array',
                'article_ids.*' => 'required|integer|exists:articles,id',
            ]);
            
            // Additional validation: ensure article_ids is an array after decoding
            if (!is_array($articleIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'article_ids must be an array'
                ], 422);
            }
            
            // Filter to only pending articles
            $validIds = Article::whereIn('id', $articleIds)
                ->where('status', 'pending_review')
                ->pluck('id')
                ->toArray();
            
            if (empty($validIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada artikel pending yang dapat disetujui'
                ], 422);
            }
            
            $updated = Article::whereIn('id', $validIds)
                   ->update(['status' => 'published', 'published_at' => now()]);
            
            ActivityLogHelper::log('article', 'bulk_approved', count($validIds) . ' artikel disetujui secara massal');
            
            return response()->json([
                'success' => true,
                'message' => count($validIds) . ' artikel berhasil disetujui!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Bulk Approve Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function bulkReject(Request $request)
    {
        // Authorization check
        if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
            abort(403, 'Anda tidak memiliki izin untuk menolak artikel secara massal.');
        }
        
        try {
            // Handle both array and JSON string formats
            $articleIds = $request->article_ids;
            if (is_string($articleIds)) {
                $articleIds = json_decode($articleIds, true);
            }
            
            // Validate that article_ids is an array and contains only integers
            $request->validate([
                'article_ids' => 'required|array',
                'article_ids.*' => 'required|integer|exists:articles,id',
            ]);
            
            // Additional validation: ensure article_ids is an array after decoding
            if (!is_array($articleIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'article_ids must be an array'
                ], 422);
            }
            
            // Filter to only pending articles
            $validIds = Article::whereIn('id', $articleIds)
                ->where('status', 'pending_review')
                ->pluck('id')
                ->toArray();
            
            if (empty($validIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada artikel pending yang dapat ditolak'
                ], 422);
            }
            
            $updated = Article::whereIn('id', $validIds)
                   ->update(['status' => 'rejected']);
            
            ActivityLogHelper::log('article', 'bulk_rejected', count($validIds) . ' artikel ditolak secara massal');
            
            return response()->json([
                'success' => true,
                'message' => count($validIds) . ' artikel berhasil ditolak!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Bulk Reject Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function pendingArticles(Request $request)
    {
        $query = Article::with(['author', 'category', 'tags'])
            ->where('status', 'pending_review');
            
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('author', function($authorQuery) use ($search) {
                      $authorQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Date filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $articles = AdminTableHelper::applySort($query, $request, [
            'title' => 'title',
            'created_at' => 'created_at',
        ], 'created_at', 'desc')->paginate(15)->withQueryString();
        
        // Pass data for sidebar
        $pendingArticlesCount = Article::where('status', 'pending_review')->count();
        $pendingCommentsCount = \App\Models\Comment::where('is_approved', false)->count();
        
        return view('admin.articles.pending', compact('articles', 'pendingArticlesCount', 'pendingCommentsCount'));
    }

    // Categories Management
    public function categories(Request $request)
    {
        $query = \App\Models\Category::withCount('articles');
        $query = AdminTableHelper::applySort($query, $request, [
            'name' => 'name',
            'articles_count' => 'articles_count',
            'created_at' => 'created_at',
        ], 'name', 'asc');

        $categories = $query->paginate(15)->withQueryString();
        return view('admin.categories.index', compact('categories'));
    }

    public function editCategory(\App\Models\Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
        ]);

        \App\Models\Category::create($request->all());
        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function updateCategory(Request $request, \App\Models\Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
        ]);

        $category->update($request->all());
        
        // Check if the request came from the edit page or modal
        if ($request->header('Referer') && str_contains($request->header('Referer'), '/edit')) {
            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui!');
        }
        
        return redirect()->back()->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroyCategory(\App\Models\Category $category)
    {
        $result = $this->forceDeleteCategory($category);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    public function bulkCategories(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:categories,id',
        ]);

        $categories = \App\Models\Category::whereIn('id', $request->ids)->get();
        $deleted = 0;
        $movedArticles = 0;
        $errors = [];

        foreach ($categories as $category) {
            $result = $this->forceDeleteCategory($category, false);
            if ($result['success']) {
                $deleted++;
                $movedArticles += $result['moved'] ?? 0;
            } else {
                $errors[] = $result['message'];
            }
        }

        if ($movedArticles > 0) {
            CacheHelper::clearArticleCache();
        }

        if ($deleted === 0) {
            return redirect()->back()->with('error', $errors[0] ?? 'Tidak ada kategori yang dihapus.');
        }

        $message = "{$deleted} kategori berhasil dihapus.";
        if ($movedArticles > 0) {
            $message .= " {$movedArticles} artikel dialihkan ke kategori lain.";
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Force-delete a category and reassign its articles to another category.
     */
    protected function forceDeleteCategory(\App\Models\Category $category, bool $clearCache = true): array
    {
        $fallbackCategory = \App\Models\Category::where('id', '!=', $category->id)
            ->orderBy('name')
            ->first();

        $articleCount = $category->articles()->count();

        if ($articleCount > 0 && !$fallbackCategory) {
            return [
                'success' => false,
                'message' => 'Tidak dapat menghapus kategori terakhir yang masih memiliki artikel!',
                'moved' => 0,
            ];
        }

        DB::transaction(function () use ($category, $fallbackCategory, $articleCount) {
            if ($articleCount > 0) {
                $category->articles()->update([
                    'category_id' => $fallbackCategory->id,
                ]);
            }

            $category->delete();
        });

        if ($clearCache && $articleCount > 0) {
            CacheHelper::clearArticleCache();
        }

        $message = $articleCount > 0
            ? "Kategori berhasil dihapus! {$articleCount} artikel dialihkan ke kategori \"{$fallbackCategory->name}\"."
            : 'Kategori berhasil dihapus!';

        return [
            'success' => true,
            'message' => $message,
            'moved' => $articleCount,
        ];
    }

    // Comments Management
    public function comments(Request $request)
    {
        $query = \App\Models\Comment::with(['article']);

        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $query = AdminTableHelper::applySort($query, $request, [
            'name' => 'name',
            'is_approved' => 'is_approved',
            'created_at' => 'created_at',
        ], 'created_at', 'desc');

        $comments = $query->paginate(15)->withQueryString();
        return view('admin.comments.index', compact('comments'));
    }

    public function bulkComments(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:comments,id',
        ]);

        $query = \App\Models\Comment::whereIn('id', $request->ids);

        if ($request->action === 'approve') {
            $count = $query->update(['is_approved' => true]);
            return redirect()->back()->with('success', "{$count} komentar berhasil disetujui.");
        }

        if ($request->action === 'reject') {
            $count = $query->update(['is_approved' => false]);
            return redirect()->back()->with('success', "{$count} komentar berhasil ditolak.");
        }

        $count = $query->delete();
        return redirect()->back()->with('success', "{$count} komentar berhasil dihapus.");
    }

    public function approveComment(\App\Models\Comment $comment)
    {
        $comment->update(['is_approved' => true]);
        return redirect()->back()->with('success', 'Komentar berhasil disetujui!');
    }

    public function rejectComment(\App\Models\Comment $comment)
    {
        $comment->update(['is_approved' => false]);
        return redirect()->back()->with('success', 'Komentar berhasil ditolak!');
    }

    public function destroyComment(\App\Models\Comment $comment)
    {
        $comment->delete();
        return redirect()->back()->with('success', 'Komentar berhasil dihapus!');
    }

    // Penulis Management
    public function penulis(Request $request)
    {
        $query = User::where('role', 'penulis')
            ->with('profile')
            ->withCount('articles')
            ->withSum('articles', 'views');

        $query = AdminTableHelper::applySort($query, $request, [
            'name' => 'name',
            'verified' => 'verified',
            'articles_count' => 'articles_count',
            'created_at' => 'created_at',
        ], 'created_at', 'desc');

        $penulis = $query->paginate(15)->withQueryString();
        return view('admin.penulis.index', compact('penulis'));
    }

    public function bulkPenulis(Request $request)
    {
        $request->validate([
            'action' => 'required|in:verify,unverify,demote',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:users,id',
        ]);

        $users = User::whereIn('id', $request->ids)->where('role', 'penulis')->get();
        $count = 0;

        foreach ($users as $user) {
            if ($request->action === 'verify') {
                $user->update(['verified' => true]);
                $count++;
            } elseif ($request->action === 'unverify') {
                $user->update(['verified' => false]);
                $count++;
            } elseif ($request->action === 'demote') {
                $user->update([
                    'role' => 'user',
                    'verified' => false,
                    'verification_request_status' => null,
                ]);
                $count++;
            }
        }

        return redirect()->back()->with('success', "{$count} penulis berhasil diproses.");
    }

    public function promoteToPenulis(User $user)
    {
        $user->update(['role' => 'penulis']);
        return redirect()->back()->with('success', 'User berhasil dipromosikan menjadi penulis!');
    }

    public function demoteFromPenulis(User $user)
    {
        try {
            if ($user->role !== 'penulis') {
                return redirect()->back()->with('error', 'User ini bukan penulis!');
            }

            $user->update([
                'role' => 'user',
                'verified' => false, // Reset verified status saat diturunkan
                'verification_request_status' => null, // Reset status verifikasi request
            ]);

            // Refresh user model dari database
            $user->refresh();

            ActivityLogHelper::logUser('user.demoted', $user, "Penulis {$user->name} diturunkan menjadi user biasa");
            
            // Jika user yang sedang login adalah user yang role-nya berubah, redirect ke user dashboard
            if (Auth::check() && Auth::id() === $user->id) {
                // Refresh session untuk memastikan data terbaru
                Auth::user()->refresh();
                return redirect()->route('user.dashboard')->with('success', 'Role Anda telah diturunkan menjadi user biasa.');
            }
            
            return redirect()->back()->with('success', 'Penulis berhasil diturunkan menjadi user!');
        } catch (\Exception $e) {
            \Log::error('Demote From Penulis Error: ' . $e->getMessage());
            ActivityLogHelper::logSecurity('user.demote.failed', 'Gagal demote penulis', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menurunkan penulis.');
        }
    }

    // Newsletter Management
    public function newsletter(Request $request)
    {
        $query = \App\Models\NewsletterSubscriber::query();
        $query = AdminTableHelper::applySort($query, $request, [
            'email' => 'email',
            'is_active' => 'is_active',
            'created_at' => 'created_at',
        ], 'created_at', 'desc');

        $subscribers = $query->paginate(15)->withQueryString();
        return view('admin.newsletter.index', compact('subscribers'));
    }

    public function bulkNewsletter(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:newsletter_subscribers,id',
        ]);

        $query = \App\Models\NewsletterSubscriber::whereIn('id', $request->ids);

        if ($request->action === 'activate') {
            $count = $query->update(['is_active' => true]);
            return redirect()->back()->with('success', "{$count} subscriber diaktifkan.");
        }

        if ($request->action === 'deactivate') {
            $count = $query->update(['is_active' => false]);
            return redirect()->back()->with('success', "{$count} subscriber dinonaktifkan.");
        }

        $count = $query->delete();
        return redirect()->back()->with('success', "{$count} subscriber dihapus.");
    }

    public function sendNewsletter(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Here you would implement the newsletter sending logic
        // For now, just return success
        return redirect()->back()->with('success', 'Newsletter berhasil dikirim!');
    }

    public function removeSubscriber(\App\Models\NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();
        return redirect()->back()->with('success', 'Subscriber berhasil dihapus!');
    }

    // Media Library
    public function media()
    {
        // Get all files from storage/app/public
        $mediaFiles = collect();
        $storagePath = storage_path('app/public');
        
        if (is_dir($storagePath)) {
            $files = glob($storagePath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    $mediaFiles->push([
                        'name' => basename($file),
                        'size' => filesize($file),
                        'modified' => filemtime($file),
                        'type' => mime_content_type($file),
                    ]);
                }
            }
        }

        return view('admin.media.index', compact('mediaFiles'));
    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB max
                'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,mp4,avi,mov,wmv,mp3,wav,ogg',
            ],
        ]);

        $file = $request->file('file');
        
        // Sanitize filename
        $originalName = $file->getClientOriginalName();
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        
        $file->storeAs('public', $filename);
        
        ActivityLogHelper::log('media', 'uploaded', 'File diupload: ' . $filename);

        return redirect()->back()->with('success', 'File berhasil diupload!');
    }

    public function deleteMedia(Request $request)
    {
        $filename = $request->input('filename');
        $filePath = storage_path('app/public/' . $filename);
        
        if (file_exists($filePath)) {
            unlink($filePath);
            return redirect()->back()->with('success', 'File berhasil dihapus!');
        }

        return redirect()->back()->with('error', 'File tidak ditemukan!');
    }

    // Analytics
    public function analytics(AnalyticsService $analyticsService)
    {
        // Get comprehensive analytics
        $analytics = $analyticsService->getAnalyticsSummary();

        // Basic stats for compatibility
        $stats = [
            'total_articles' => Article::count(),
            'published_articles' => Article::where('status', 'published')->count(),
            'total_views' => Article::sum('views'),
            'total_users' => User::count(),
            'total_comments' => \App\Models\Comment::count(),
        ];

        // Chart data for last 30 days (engagement trends)
        $chartData = $analytics['engagement']['trends'] ?? [];

        return view('admin.analytics.index', compact('stats', 'chartData', 'analytics'));
    }

    // Reports
    public function reports()
    {
        $reports = [
            'articles_by_category' => \App\Models\Category::withCount('articles')->get(),
            'articles_by_author' => User::where('role', 'penulis')->withCount('articles')->get(),
            'monthly_stats' => $this->getMonthlyStats(),
        ];

        return view('admin.reports.index', compact('reports'));
    }

    public function exportReport(Request $request)
    {
        $type = $request->input('type', 'articles');
        
        // Here you would implement the export logic
        // For now, just return success
        return redirect()->back()->with('success', 'Laporan berhasil diekspor!');
    }

    // Backup
    public function backup(BackupService $backupService)
    {
        $backups = $backupService->getBackups();
        $storageInfo = $backupService->getBackupStorageSize();

        return view('admin.backup.index', compact('backups', 'storageInfo'));
    }

    public function createBackup(Request $request, BackupService $backupService)
    {
        $type = $request->input('type', 'full');

        try {
            switch ($type) {
                case 'database':
                    $result = $backupService->createDatabaseBackup();
                    if ($result) {
                        ActivityLogHelper::log('backup', 'created', 'Database backup created: ' . basename($result));
                        return redirect()->back()->with('success', 'Database backup berhasil dibuat!');
                    }
                    break;

                case 'files':
                    $result = $backupService->createFilesBackup();
                    if ($result) {
                        ActivityLogHelper::log('backup', 'created', 'Files backup created: ' . basename($result));
                        return redirect()->back()->with('success', 'Files backup berhasil dibuat!');
                    }
                    break;

                case 'full':
                default:
                    $results = $backupService->createFullBackup();
                    if ($results['success']) {
                        ActivityLogHelper::log('backup', 'created', 'Full backup created');
                        return redirect()->back()->with('success', 'Full backup berhasil dibuat!');
                    }
                    break;
            }

            return redirect()->back()->with('error', 'Backup gagal dibuat!');
        } catch (\Exception $e) {
            ActivityLogHelper::log('backup', 'error', 'Backup failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat backup: ' . $e->getMessage());
        }
    }

    public function downloadBackup($backup, BackupService $backupService)
    {
        $backups = $backupService->getBackups();
        $backupFile = collect($backups)->firstWhere('filename', $backup);

        if (!$backupFile || !file_exists($backupFile['path'])) {
            return redirect()->back()->with('error', 'File backup tidak ditemukan!');
        }

        ActivityLogHelper::log('backup', 'downloaded', 'Backup downloaded: ' . $backup);

        return response()->download($backupFile['path'], $backup);
    }

    public function deleteBackup($backup, BackupService $backupService)
    {
        $backups = $backupService->getBackups();
        $backupFile = collect($backups)->firstWhere('filename', $backup);

        if (!$backupFile || !file_exists($backupFile['path'])) {
            return redirect()->back()->with('error', 'File backup tidak ditemukan!');
        }

        if (@unlink($backupFile['path'])) {
            ActivityLogHelper::log('backup', 'deleted', 'Backup deleted: ' . $backup);
            return redirect()->back()->with('success', 'Backup berhasil dihapus!');
        }

        return redirect()->back()->with('error', 'Gagal menghapus backup!');
    }

    // System Logs
    public function logs()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        
        if (file_exists($logFile)) {
            $logs = file($logFile);
            $logs = array_slice($logs, -100); // Last 100 lines
        }

        return view('admin.logs.index', compact('logs'));
    }

    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
            return redirect()->back()->with('success', 'Log berhasil dibersihkan!');
        }

        return redirect()->back()->with('error', 'File log tidak ditemukan!');
    }

    private function getMonthlyStats()
    {
        $stats = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $stats[] = [
                'month' => $date->format('M Y'),
                'articles' => Article::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'users' => User::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
            ];
        }
        return $stats;
    }
}
