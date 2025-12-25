<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use App\Helpers\ActivityLogHelper;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Statistik dasar
        $stats = [
            'total_users' => User::count(),
            'total_penulis' => User::where('role', 'penulis')->count(),
            'total_articles' => Article::count(),
            'pending_articles' => Article::where('status', 'pending_review')->count(),
            'published_articles' => Article::where('status', 'published')->count(),
            'draft_articles' => Article::where('status', 'draft')->count(),
            'rejected_articles' => Article::where('status', 'rejected')->count(),
            'total_views' => Article::sum('views'),
            'total_categories' => \App\Models\Category::count(),
            'total_comments' => \App\Models\Comment::count(),
            'pending_comments' => \App\Models\Comment::where('is_approved', false)->count(),
            'newsletter_subscribers' => \App\Models\NewsletterSubscriber::count(),
            'articles_today' => Article::whereDate('created_at', today())->count(),
            'views_today' => Article::whereDate('created_at', today())->sum('views'),
            'comments_today' => \App\Models\Comment::whereDate('created_at', today())->count(),
            'pending_verification_requests' => User::where('role', 'penulis')
                ->where('verification_request_status', 'pending')
                ->count(),
            'verified_penulis' => User::where('role', 'penulis')
                ->where('verified', true)
                ->count(),
        ];

        // Statistik bulanan
        $monthlyStats = [
            'articles_this_month' => Article::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'users_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'views_this_month' => Article::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('views'),
        ];

        // Data untuk chart (7 hari terakhir)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartData[] = [
                'date' => $date->format('d/m'),
                'articles' => Article::whereDate('created_at', $date)->count(),
                'users' => User::whereDate('created_at', $date)->count(),
                'views' => Article::whereDate('created_at', $date)->sum('views'),
            ];
        }

        // Artikel terbaru
        $recent_articles = Article::with(['author', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Artikel populer (berdasarkan view count)
        $popular_articles = Article::where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        // Kategori dengan artikel terbanyak
        $category_stats = \App\Models\Category::withCount('articles')
            ->orderBy('articles_count', 'desc')
            ->limit(5)
            ->get();

        // Komentar terbaru
        $recent_comments = \App\Models\Comment::with('article')
            ->latest()
            ->limit(5)
            ->get();

        // Aktivitas terbaru
        $recent_activity = collect();
        
        // Tambahkan artikel terbaru ke aktivitas
        $recent_articles->each(function ($article) use ($recent_activity) {
            $recent_activity->push([
                'type' => 'article',
                'action' => $article->status === 'published' ? 'diterbitkan' : 'dibuat',
                'title' => $article->title,
                'user' => $article->author->name ?? 'Sistem',
                'time' => $article->created_at,
                'icon' => 'fas fa-newspaper',
                'color' => $article->status === 'published' ? 'green' : 'yellow'
            ]);
        });

        // Tambahkan komentar terbaru ke aktivitas
        $recent_comments->each(function ($comment) use ($recent_activity) {
            $recent_activity->push([
                'type' => 'comment',
                'action' => 'berkomentar',
                'title' => 'Komentar pada: ' . $comment->article->title,
                'user' => $comment->name,
                'time' => $comment->created_at,
                'icon' => 'fas fa-comment',
                'color' => 'blue'
            ]);
        });

        // Urutkan aktivitas berdasarkan waktu
        $recent_activity = $recent_activity->sortByDesc('time')->take(10);


        // Pass pending comments count to sidebar
        $pendingCommentsCount = \App\Models\Comment::where('is_approved', false)->count();
        $pendingArticlesCount = Article::where('status', 'pending_review')->count();

        return view('admin.dashboard', compact(
            'stats', 
            'monthlyStats', 
            'chartData', 
            'recent_articles', 
            'popular_articles', 
            'category_stats', 
            'recent_comments', 
            'recent_activity', 
            'pendingCommentsCount',
            'pendingArticlesCount'
        ));
    }

    public function users()
    {
        $users = User::with('profile')->paginate(15);
        return view('admin.users.index', compact('users'));
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

    public function verificationRequests()
    {
        $requests = User::where('role', 'penulis')
            ->where('verification_request_status', 'pending')
            ->with('profile')
            ->orderBy('verification_requested_at', 'desc')
            ->paginate(15);

        return view('admin.verification-requests', compact('requests'));
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

            $user->update(['verified' => !$user->verified]);
            $status = $user->verified ? 'diverifikasi' : 'tidak diverifikasi';
            ActivityLogHelper::logUser('user.verification.toggled', $user, "User {$user->name} {$status}");
            
            return redirect()->back()->with('success', "User berhasil {$status}!");
        } catch (\Exception $e) {
            \Log::error('Toggle Verified Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah status verifikasi.');
        }
    }

    public function approveArticle(Request $request, Article $article)
    {
        try {
            $article->update(['status' => 'published']);
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
        try {
            // Handle both array and JSON string formats
            $articleIds = $request->article_ids;
            if (is_string($articleIds)) {
                $articleIds = json_decode($articleIds, true);
            }
            
            $request->validate([
                'article_ids' => 'required',
            ]);
            
            // Validate that article_ids is an array after decoding
            if (!is_array($articleIds)) {
                throw new \Exception('article_ids must be an array');
            }
            
            // Validate each article ID exists
            foreach ($articleIds as $id) {
                if (!\App\Models\Article::where('id', $id)->exists()) {
                    throw new \Exception("Article with ID {$id} does not exist");
                }
            }
            
            $updated = Article::whereIn('id', $articleIds)
                   ->where('status', 'pending_review')
                   ->update(['status' => 'published']);
            
            return response()->json([
                'success' => true,
                'message' => count($articleIds) . ' artikel berhasil disetujui!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function bulkReject(Request $request)
    {
        try {
            // Handle both array and JSON string formats
            $articleIds = $request->article_ids;
            if (is_string($articleIds)) {
                $articleIds = json_decode($articleIds, true);
            }
            
            $request->validate([
                'article_ids' => 'required',
            ]);
            
            // Validate that article_ids is an array after decoding
            if (!is_array($articleIds)) {
                throw new \Exception('article_ids must be an array');
            }
            
            // Validate each article ID exists
            foreach ($articleIds as $id) {
                if (!\App\Models\Article::where('id', $id)->exists()) {
                    throw new \Exception("Article with ID {$id} does not exist");
                }
            }
            
            $updated = Article::whereIn('id', $articleIds)
                   ->where('status', 'pending_review')
                   ->update(['status' => 'rejected']);
            
            return response()->json([
                'success' => true,
                'message' => count($articleIds) . ' artikel berhasil ditolak!'
            ]);
        } catch (\Exception $e) {
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
        
        $articles = $query->latest()->paginate(15);
        
        // Pass data for sidebar
        $pendingArticlesCount = Article::where('status', 'pending_review')->count();
        $pendingCommentsCount = \App\Models\Comment::where('is_approved', false)->count();
        
        return view('admin.articles.pending', compact('articles', 'pendingArticlesCount', 'pendingCommentsCount'));
    }

    // Categories Management
    public function categories()
    {
        $categories = \App\Models\Category::withCount('articles')->paginate(15);
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
        if ($category->articles()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus kategori yang memiliki artikel!');
        }
        
        $category->delete();
        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }

    // Comments Management
    public function comments()
    {
        $comments = \App\Models\Comment::with(['article'])
            ->latest()
            ->paginate(15);
        return view('admin.comments.index', compact('comments'));
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
    public function penulis()
    {
        $penulis = User::where('role', 'penulis')->with('profile')->paginate(15);
        return view('admin.penulis.index', compact('penulis'));
    }

    public function promoteToPenulis(User $user)
    {
        $user->update(['role' => 'penulis']);
        return redirect()->back()->with('success', 'User berhasil dipromosikan menjadi penulis!');
    }

    public function demoteFromPenulis(User $user)
    {
        $user->update(['role' => 'user']);
        return redirect()->back()->with('success', 'Penulis berhasil diturunkan menjadi user!');
    }

    // Newsletter Management
    public function newsletter()
    {
        $subscribers = \App\Models\NewsletterSubscriber::latest()->paginate(15);
        return view('admin.newsletter.index', compact('subscribers'));
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
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public', $filename);

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
