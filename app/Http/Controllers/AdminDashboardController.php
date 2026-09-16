<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleReport;
use App\Models\Comment;
use App\Models\ReadingHistory;
use App\Models\User;
use App\Helpers\ActivityLogHelper;
use App\Helpers\AdminTableHelper;
use App\Helpers\CacheHelper;
use App\Helpers\UploadValidation;
use App\Services\BackupService;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Live queries only â€” admin overview must not use dashboard_stats cache
        $pendingCommentsCount = Comment::where('is_approved', false)->count();
        $suspendedArticlesCount = Article::where('status', 'suspended')->count();

        $stats = [
            'total_users' => User::count(),
            'total_penulis' => User::where('role', 'penulis')->count(),
            'total_articles' => Article::count(),
            'suspended_articles' => $suspendedArticlesCount,
            'published_articles' => Article::published()->count(),
            'draft_articles' => Article::where('status', 'draft')->count(),
            'total_views' => (int) Article::sum('views'),
            'total_categories' => \App\Models\Category::count(),
            'total_comments' => Comment::count(),
            'pending_comments' => $pendingCommentsCount,
            'newsletter_subscribers' => \App\Models\NewsletterSubscriber::where('is_active', true)->count(),
            'articles_today' => Article::whereDate('created_at', today())->count(),
            'published_today' => Article::published()
                ->whereDate('published_at', today())
                ->count(),
            'reads_today' => ReadingHistory::whereDate('read_at', today())->count(),
            'comments_today' => Comment::whereDate('created_at', today())->count(),
            'pending_verification_requests' => User::where('verification_request_status', 'pending')
                ->where('role', 'user')
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

        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();

        $articlesByDay = Article::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('day')
            ->pluck('total', 'day');

        $commentsByDay = Comment::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('day')
            ->pluck('total', 'day');

        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayKey = $date->toDateString();
            $chartData[] = [
                'date' => $date->format('d/m'),
                'articles' => (int) ($articlesByDay[$dayKey] ?? 0),
                'comments' => (int) ($commentsByDay[$dayKey] ?? 0),
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
            $isPublished = $article->status === 'published';
            $recent_activity->push([
                'type' => 'article',
                'action' => $isPublished ? 'menerbitkan artikel' : 'membuat artikel',
                'title' => $article->title,
                'user' => $article->author->name ?? 'Sistem',
                'time' => $isPublished && $article->published_at
                    ? $article->published_at
                    : $article->created_at,
                'color' => $isPublished ? 'green' : ($article->status === 'suspended' ? 'yellow' : 'gray'),
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
            'suspendedArticlesCount'
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
            'action' => 'required|in:unverify',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:users,id',
        ]);

        $users = User::whereIn('id', $request->ids)->get();
        $count = 0;

        foreach ($users as $user) {
            // Never mutate admin/editor via bulk user tools
            if (in_array($user->role, ['admin', 'editor'], true)) {
                continue;
            }

            if ($request->action === 'unverify') {
                if ($user->role === 'penulis' && $user->verified && !$user->is_internal) {
                    $user->revokeVerifiedToUser();
                    $count++;
                }
            }
        }

        ActivityLogHelper::log('user.bulk', "{$count} pengguna diproses (action: {$request->action})", [
            'action' => $request->action,
            'count' => $count,
            'ids' => $request->ids,
        ]);

        return redirect()->back()->with('success', "{$count} pengguna berhasil diproses.");
    }

    public function verificationRequests(Request $request)
    {
        $query = User::where('verification_request_status', 'pending')
            ->where('role', 'user')
            ->with('profile')
            ->withCount('articles');

        $query = AdminTableHelper::applySort($query, $request, [
            'name' => 'name',
            'role' => 'role',
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
            'reason' => 'nullable|string|max:1000',
        ]);

        $users = User::whereIn('id', $request->ids)
            ->where('role', 'user')
            ->where('verification_request_status', 'pending')
            ->get();

        $count = 0;
        foreach ($users as $user) {
            if ($request->action === 'approve') {
                $this->applyVerificationApproval($user);
            } else {
                $this->applyVerificationRejection($user, $request->reason);
            }
            $count++;
        }

        $label = $request->action === 'approve' ? 'disetujui' : 'ditolak';
        return redirect()->back()->with('success', "{$count} permintaan berhasil {$label}.");
    }

    public function approveVerification(User $user)
    {
        try {
            if ($user->role !== 'user' || $user->verification_request_status !== 'pending') {
                return redirect()->back()->with('error', 'Permintaan tidak valid!');
            }

            $this->applyVerificationApproval($user);

            return redirect()->back()->with(
                'success',
                'Upgrade ke penulis berhasil disetujui. User sekarang penulis terverifikasi.'
            );
        } catch (\Exception $e) {
            \Log::error('Approve Verification Error: ' . $e->getMessage());
            ActivityLogHelper::logSecurity('verification.approve.failed', 'Gagal approve verifikasi', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyetujui permintaan.');
        }
    }

    public function rejectVerification(Request $request, User $user)
    {
        try {
            if ($user->role !== 'user' || $user->verification_request_status !== 'pending') {
                return redirect()->back()->with('error', 'Permintaan tidak valid!');
            }

            $request->validate([
                'reason' => 'nullable|string|max:1000',
            ]);

            $this->applyVerificationRejection($user, $request->reason);

            return redirect()->back()->with(
                'success',
                'Permintaan upgrade ke penulis ditolak. User tetap sebagai pembaca.'
            );
        } catch (\Exception $e) {
            \Log::error('Reject Verification Error: ' . $e->getMessage());
            ActivityLogHelper::logSecurity('verification.reject.failed', 'Gagal reject verifikasi', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menolak permintaan.');
        }
    }

    /**
     * Setujui upgrade user â†’ penulis terverifikasi.
     */
    private function applyVerificationApproval(User $user): void
    {
        if ($user->role !== 'user') {
            throw new \RuntimeException('Hanya user yang dapat di-upgrade menjadi penulis.');
        }

        if ($user->verification_request_status !== 'pending') {
            throw new \RuntimeException('Hanya permintaan berstatus pending yang dapat disetujui.');
        }

        if (!in_array($user->verification_type, ['perorangan', 'lembaga'], true)) {
            throw new \RuntimeException('Pengajuan tidak lengkap: tipe verifikasi wajib diisi.');
        }

        $user->update([
            'role' => 'penulis',
            'verified' => true,
            'verification_request_status' => 'approved',
            'verification_rejection_reason' => null,
        ]);

        // Draft milik user (jika ada) tidak di-auto-publish; penulis publish sendiri setelah upgrade.
        $user->articles()
            ->where('status', 'pending_review')
            ->update([
                'status' => 'draft',
                'rejection_reason' => null,
            ]);

        $logMessage = "Upgrade user {$user->name} ke penulis terverifikasi disetujui";

        ActivityLogHelper::logUser('upgrade.approved', $user, $logMessage);
        ActivityLogHelper::logSecurity('upgrade.approved', $logMessage, ['user_id' => $user->id]);
    }

    /**
     * Tolak permintaan upgrade. User tetap role user.
     */
    private function applyVerificationRejection(User $user, ?string $reason = null): void
    {
        $reason = $reason ? trim($reason) : null;

        $user->update([
            'role' => 'user',
            'verification_request_status' => 'rejected',
            'verification_rejection_reason' => $reason ?: null,
            'verified' => false,
        ]);

        $logMessage = "Upgrade user {$user->name} ditolak" . ($reason ? ". Alasan: {$reason}" : '');

        ActivityLogHelper::logUser('upgrade.rejected', $user, $logMessage);
        ActivityLogHelper::logSecurity('upgrade.rejected', $logMessage, ['user_id' => $user->id, 'reason' => $reason]);
    }

    public function toggleVerified(User $user)
    {
        try {
            if (in_array($user->role, ['admin', 'editor'], true)) {
                return redirect()->back()->with('error', 'Tidak dapat mengubah verifikasi admin atau editor.');
            }

            if ($user->role !== 'penulis') {
                return redirect()->back()->with('error', 'Hanya penulis yang relevan untuk aksi ini.');
            }

            if ($user->is_internal) {
                return redirect()->back()->with(
                    'error',
                    'Tidak dapat mencabut verified penulis Redaksi. Cabut status Redaksi terlebih dahulu.'
                );
            }

            $user->revokeVerifiedToUser();
            ActivityLogHelper::logUser('user.verification.revoked_to_user', $user, "Verified {$user->name} dicabut; diturunkan ke user");

            return redirect()->back()->with(
                'success',
                'Status verified dicabut. Akun diturunkan menjadi user biasa; harus ajukan upgrade lagi untuk menulis.'
            );
        } catch (\Exception $e) {
            \Log::error('Toggle Verified Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah status verifikasi.');
        }
    }

    public function articleDetail(Article $article)
    {
        $article->load(['author.profile', 'category', 'tags']);

        return view('admin.articles.detail', compact('article'));
    }

    public function suspendedArticles(Request $request)
    {
        $query = Article::with(['author', 'category', 'tags', 'suspendedBy'])
            ->where('status', 'suspended');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('author', function ($authorQuery) use ($search) {
                      $authorQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('suspended_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('suspended_at', '<=', $request->date_to);
        }

        $articles = AdminTableHelper::applySort($query, $request, [
            'title' => 'title',
            'suspended_at' => 'suspended_at',
            'created_at' => 'created_at',
        ], 'suspended_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.articles.suspended', compact('articles'));
    }

    /**
     * Cari artikel tayang untuk ditangguhkan (admin & editor) — tanpa menunggu laporan.
     */
    public function moderateArticles(Request $request)
    {
        $query = Article::with(['author', 'category'])
            ->where('status', 'published')
            ->whereNotNull('published_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhereHas('author', function ($authorQuery) use ($search) {
                      $authorQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $articles = AdminTableHelper::applySort($query, $request, [
            'title' => 'title',
            'published_at' => 'published_at',
            'created_at' => 'created_at',
        ], 'published_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.articles.moderate', compact('articles'));
    }

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
        $category->loadCount('articles');

        return view('admin.categories.edit', compact('category'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'is_active' => 'nullable|boolean',
        ]);

        \App\Models\Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function updateCategory(Request $request, \App\Models\Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'is_active' => 'nullable|boolean',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            // Preserve when quick-edit modal omits the field
            'is_active' => $request->has('is_active')
                ? $request->boolean('is_active')
                : $category->is_active,
        ]);
        
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
            'action' => 'required|in:delete,activate,deactivate',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:categories,id',
        ]);

        if ($request->action === 'activate' || $request->action === 'deactivate') {
            $active = $request->action === 'activate';
            $count = \App\Models\Category::whereIn('id', $request->ids)
                ->update(['is_active' => $active]);

            return redirect()->back()->with(
                'success',
                $active
                    ? "{$count} kategori diaktifkan."
                    : "{$count} kategori dinonaktifkan."
            );
        }

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
        if (!empty($errors)) {
            $message .= ' Sebagian gagal: ' . implode(' ', array_slice($errors, 0, 3));
            return redirect()->back()->with('warning', $message);
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
                $q->where('comment', 'like', "%{$search}%")
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

        $commentStats = [
            'total' => \App\Models\Comment::count(),
            'approved' => \App\Models\Comment::where('is_approved', true)->count(),
            'pending' => \App\Models\Comment::where('is_approved', false)->count(),
        ];

        return view('admin.comments.index', compact('comments', 'commentStats'));
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
            ->withCount([
                'articles',
                'articles as published_articles_count' => fn ($q) => $q->published(),
            ])
            ->withSum('articles', 'views');

        $query = AdminTableHelper::applySort($query, $request, [
            'name' => 'name',
            'verified' => 'verified',
            'articles_count' => 'published_articles_count',
            'created_at' => 'created_at',
        ], 'created_at', 'desc');

        $penulis = $query->paginate(15)->withQueryString();
        return view('admin.penulis.index', compact('penulis'));
    }

    public function bulkPenulis(Request $request)
    {
        $request->validate([
            'action' => 'required|in:unverify,demote',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:users,id',
        ]);

        $users = User::whereIn('id', $request->ids)->where('role', 'penulis')->get();
        $count = 0;

        foreach ($users as $user) {
            if ($user->is_internal) {
                continue;
            }

            if ($request->action === 'unverify') {
                if ($user->verified) {
                    $user->revokeVerifiedToUser();
                    $count++;
                }
            } elseif ($request->action === 'demote') {
                $user->demoteToUser();
                $count++;
            }
        }

        ActivityLogHelper::log('penulis.bulk', "{$count} penulis diproses (action: {$request->action})", [
            'action' => $request->action,
            'count' => $count,
            'ids' => $request->ids,
        ]);

        return redirect()->back()->with('success', "{$count} penulis berhasil diproses.");
    }

    public function demoteFromPenulis(User $user)
    {
        try {
            if (in_array($user->role, ['admin', 'editor'], true)) {
                ActivityLogHelper::logSecurity('user.demote.blocked', 'Percobaan demote admin/editor diblokir', [
                    'target_user_id' => $user->id,
                    'target_role' => $user->role,
                ]);
                return redirect()->back()->with('error', 'Tidak dapat menurunkan role admin atau editor.');
            }

            if ($user->role !== 'penulis') {
                return redirect()->back()->with('error', 'User ini bukan penulis!');
            }

            if ($user->is_internal) {
                return redirect()->back()->with(
                    'error',
                    'Cabut status Redaksi terlebih dahulu sebelum menurunkan ke user.'
                );
            }

            $user->demoteToUser();

            // Refresh user model dari database
            $user->refresh();

            ActivityLogHelper::logUser('user.demoted', $user, "Penulis {$user->name} diturunkan menjadi user biasa");
            
            // Jika user yang sedang login adalah user yang role-nya berubah, redirect ke user dashboard
            if (Auth::check() && Auth::id() === $user->id) {
                // Refresh session untuk memastikan data terbaru
                Auth::user()->refresh();
                return redirect()->route('user.dashboard')->with('success', 'Role Anda telah diturunkan menjadi user biasa.');
            }
            
            return redirect()->back()->with('success', 'Penulis berhasil diturunkan menjadi user! Artikel yang sudah ada tetap tersimpan atas nama penulis ini.');
        } catch (\Exception $e) {
            \Log::error('Demote From Penulis Error: ' . $e->getMessage());
            ActivityLogHelper::logSecurity('user.demote.failed', 'Gagal demote penulis', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menurunkan penulis.');
        }
    }

    /**
     * Jadikan user/penulis sebagai staf redaksi (verified, tanpa pengajuan publik).
     */
    public function markAsRedaksi(User $user)
    {
        try {
            if (in_array($user->role, ['admin', 'editor'], true)) {
                return redirect()->back()->with('error', 'Tidak dapat mengubah role admin atau editor.');
            }

            if (!in_array($user->role, ['user', 'penulis'], true)) {
                return redirect()->back()->with('error', 'Hanya user atau penulis yang dapat dijadikan Redaksi.');
            }

            if ($user->isRedaksi()) {
                return redirect()->back()->with('info', 'Akun ini sudah berstatus Redaksi.');
            }

            $user->markAsRedaksi();
            ActivityLogHelper::logUser('user.marked_redaksi', $user, "{$user->name} dijadikan penulis Redaksi");

            return redirect()->back()->with('success', "{$user->name} sekarang penulis Redaksi (terverifikasi).");
        } catch (\Exception $e) {
            \Log::error('Mark As Redaksi Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menandai Redaksi.');
        }
    }

    /**
     * Cabut status Redaksi; tetap penulis terverifikasi.
     */
    public function unmarkRedaksi(User $user)
    {
        try {
            if (!$user->isRedaksi()) {
                return redirect()->back()->with('error', 'Akun ini bukan penulis Redaksi.');
            }

            $user->unmarkRedaksi();
            ActivityLogHelper::logUser('user.unmarked_redaksi', $user, "Status Redaksi {$user->name} dicabut");

            return redirect()->back()->with(
                'success',
                "Status Redaksi dicabut. {$user->name} tetap penulis terverifikasi."
            );
        } catch (\Exception $e) {
            \Log::error('Unmark Redaksi Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mencabut status Redaksi.');
        }
    }

    /**
     * Sanksi konten: demote ke user + ban 7 / 30 / 365 hari (tercatat).
     */
    public function warnPenulis(Request $request, User $user)
    {
        try {
            if ($user->isRedaksi()) {
                return redirect()->back()->with('error', 'Cabut status Redaksi terlebih dahulu sebelum memberi sanksi.');
            }

            if ($user->role !== 'penulis') {
                return redirect()->back()->with('error', 'Hanya penulis yang dapat diberi sanksi konten.');
            }

            $request->validate([
                'reason' => 'nullable|string|max:1000',
            ]);

            $actions = $user->issueSanction($request->input('reason'), Auth::user());

            ActivityLogHelper::logUser(
                'user.content_sanction',
                $user,
                "Sanksi #{$actions['level']} untuk {$user->name}: ban {$actions['days']} hari s/d {$actions['banned_until']->format('d/m/Y H:i')}"
            );

            return redirect()->route('admin.users')
                ->with(
                    'success',
                    "Sanksi #{$actions['level']}: {$user->name} diturunkan ke user dan dibanned {$actions['days']} hari (sampai {$actions['banned_until']->format('d/m/Y H:i')})."
                );
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Sanction Penulis Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memberi sanksi.');
        }
    }

    /**
     * Buka ban lebih awal.
     */
    public function liftBan(Request $request, User $user)
    {
        try {
            if (!$user->isBanned()) {
                return redirect()->back()->with('error', 'Pengguna ini tidak sedang dibanned.');
            }

            $request->validate([
                'note' => 'nullable|string|max:1000',
            ]);

            $user->liftBan(Auth::user(), $request->input('note'));
            ActivityLogHelper::logUser('user.ban_lifted', $user, "Ban {$user->name} dibuka oleh admin");

            return redirect()->back()->with(
                'success',
                "Ban {$user->name} dibuka. Akun tetap user; mereka dapat mengajukan upgrade lagi."
            );
        } catch (\Exception $e) {
            \Log::error('Lift Ban Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuka ban.');
        }
    }

    public function banAppeals(Request $request)
    {
        $query = User::where('ban_appeal_status', 'pending')
            ->whereNotNull('banned_until')
            ->where('banned_until', '>', now())
            ->with(['profile', 'contentSanctions' => fn ($q) => $q->latest()->limit(5)])
            ->withCount('contentSanctions');

        $query = AdminTableHelper::applySort($query, $request, [
            'name' => 'name',
            'banned_until' => 'banned_until',
            'ban_appeal_at' => 'ban_appeal_at',
            'content_warning_count' => 'content_warning_count',
        ], 'ban_appeal_at', 'desc');

        $appeals = $query->paginate(15)->withQueryString();

        return view('admin.ban-appeals', compact('appeals'));
    }

    public function approveBanAppeal(User $user)
    {
        try {
            if ($user->ban_appeal_status !== 'pending') {
                return redirect()->back()->with('error', 'Tidak ada banding pending untuk akun ini.');
            }

            $user->liftBan(Auth::user(), 'Banding disetujui');
            ActivityLogHelper::logUser('user.ban_appeal.approved', $user, "Banding ban {$user->name} disetujui");

            return redirect()->back()->with('success', "Banding disetujui. Ban {$user->name} dibuka.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyetujui banding.');
        }
    }

    public function rejectBanAppeal(Request $request, User $user)
    {
        try {
            if ($user->ban_appeal_status !== 'pending') {
                return redirect()->back()->with('error', 'Tidak ada banding pending untuk akun ini.');
            }

            $request->validate([
                'reason' => 'nullable|string|max:1000',
            ]);

            $user->rejectBanAppeal($request->input('reason'));
            ActivityLogHelper::logUser('user.ban_appeal.rejected', $user, "Banding ban {$user->name} ditolak");

            return redirect()->back()->with('success', "Banding {$user->name} ditolak. Masa ban tetap berlaku.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menolak banding.');
        }
    }

    /**
     * Batasi publish langsung penulis selama N hari (default 7).
     */
    public function restrictPenulisPublish(Request $request, User $user)
    {
        if ($user->role !== 'penulis') {
            return redirect()->back()->with('error', 'Hanya penulis yang dapat dibatasi publish.');
        }

        $days = (int) ($request->input('days', 7));
        $days = max(1, min($days, 90));

        $user->update([
            'publish_restricted_until' => now()->addDays($days),
        ]);

        ActivityLogHelper::logUser(
            'user.publish_restricted',
            $user,
            "Publish langsung {$user->name} dibatasi {$days} hari"
        );

        return redirect()->back()->with('success', "Publish langsung {$user->name} dibatasi {$days} hari. Artikel baru wajib lewat review.");
    }

    public function clearPenulisPublishRestriction(User $user)
    {
        if ($user->role !== 'penulis') {
            return redirect()->back()->with('error', 'Hanya penulis yang relevan.');
        }

        $user->clearPublishRestriction();
        ActivityLogHelper::logUser('user.publish_restriction_cleared', $user, "Pembatasan publish {$user->name} dicabut");

        return redirect()->back()->with('success', "Pembatasan publish untuk {$user->name} telah dicabut.");
    }

    /**
     * Cabut verified = turunkan ke user biasa.
     */
    public function revokePenulisVerified(User $user)
    {
        if ($user->role !== 'penulis') {
            return redirect()->back()->with('error', 'Hanya penulis yang dapat dicabut verified-nya.');
        }

        if ($user->is_internal) {
            return redirect()->back()->with(
                'error',
                'Tidak dapat mencabut verified penulis Redaksi. Cabut status Redaksi terlebih dahulu.'
            );
        }

        $user->revokeVerifiedToUser();
        ActivityLogHelper::logUser('user.verified_revoked', $user, "Verified {$user->name} dicabut; diturunkan ke user");

        return redirect()->back()->with(
            'success',
            "{$user->name} diturunkan menjadi user biasa. Harus ajukan upgrade lagi untuk menulis."
        );
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
        $activeSubscriberCount = \App\Models\NewsletterSubscriber::where('is_active', true)->count();

        return view('admin.newsletter.index', compact('subscribers', 'activeSubscriberCount'));
    }

    public function exportNewsletter(Request $request)
    {
        $query = \App\Models\NewsletterSubscriber::query()->orderBy('id');

        if ($request->filled('status') && in_array($request->input('status'), ['0', '1'], true)) {
            $query->where('is_active', $request->input('status') === '1');
        }

        $filename = 'newsletter_subscribers_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Email', 'Nama', 'Aktif', 'Subscribed At']);
            $query->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $sub) {
                    fputcsv($out, [
                        $sub->id,
                        $sub->email,
                        $sub->name,
                        $sub->is_active ? 'yes' : 'no',
                        optional($sub->created_at)->format('Y-m-d H:i:s'),
                    ]);
                }
            });
            fclose($out);
        };

        ActivityLogHelper::log('newsletter.export', 'Export subscriber newsletter');

        return response()->stream($callback, 200, $headers);
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

        $subscribers = \App\Models\NewsletterSubscriber::where('is_active', true)->get();

        if ($subscribers->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada subscriber aktif untuk dikirimi newsletter.');
        }

        $sent = 0;
        $failed = 0;

        foreach ($subscribers as $subscriber) {
            try {
                \Illuminate\Support\Facades\Mail::to($subscriber->email)
                    ->send(new \App\Mail\NewsletterBroadcast($request->subject, $request->content));
                $sent++;
            } catch (\Throwable $e) {
                $failed++;
                \Log::warning('Newsletter send failed', [
                    'email' => $subscriber->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        ActivityLogHelper::log('newsletter.sent', "Newsletter dikirim ke {$sent} subscriber" . ($failed ? " ({$failed} gagal)" : ''), [
            'subject' => $request->subject,
            'sent' => $sent,
            'failed' => $failed,
        ]);

        if ($sent === 0) {
            return redirect()->back()->with('error', 'Gagal mengirim newsletter. Periksa konfigurasi email.');
        }

        $message = "Newsletter berhasil dikirim ke {$sent} subscriber aktif.";
        if ($failed > 0) {
            $message .= " {$failed} gagal terkirim.";
        }

        return redirect()->back()->with('success', $message);
    }

    public function removeSubscriber(\App\Models\NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();
        return redirect()->back()->with('success', 'Subscriber berhasil dihapus!');
    }

    // Media Library (admin-owned only â€” never mass-delete penulis/article media)
    public function media()
    {
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        $adminDir = 'media/admin';

        if (!$disk->exists($adminDir)) {
            $disk->makeDirectory($adminDir);
        }

        $mediaFiles = collect();

        foreach ($disk->files($adminDir) as $path) {
            $fullPath = storage_path('app/public/' . $path);
            if (!is_file($fullPath)) {
                continue;
            }

            $mediaFiles->push([
                'path' => $path,
                'name' => basename($path),
                'size' => filesize($fullPath),
                'modified' => filemtime($fullPath),
                'type' => mime_content_type($fullPath) ?: 'application/octet-stream',
                'url' => $disk->url($path),
                'scope' => 'admin',
            ]);
        }

        // Legacy flat uploads in storage root (not under articles/ or media/penulis/)
        foreach ($disk->files('') as $path) {
            if (str_contains($path, '/') || str_contains($path, '\\')) {
                continue;
            }
            // Skip non-media clutter
            if (in_array($path, ['.gitignore', 'index.php'], true)) {
                continue;
            }

            $fullPath = storage_path('app/public/' . $path);
            if (!is_file($fullPath)) {
                continue;
            }

            $mediaFiles->push([
                'path' => $path,
                'name' => basename($path),
                'size' => filesize($fullPath),
                'modified' => filemtime($fullPath),
                'type' => mime_content_type($fullPath) ?: 'application/octet-stream',
                'url' => $disk->url($path),
                'scope' => 'legacy',
            ]);
        }

        $mediaFiles = $mediaFiles->sortByDesc('modified')->values();

        return view('admin.media.index', compact('mediaFiles'));
    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => UploadValidation::adminMedia(),
        ]);

        $file = $request->file('file');

        $originalName = $file->getClientOriginalName();
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);

        $path = $file->storeAs('media/admin', $filename, 'public');

        ActivityLogHelper::log('media.uploaded', 'File diupload: ' . $path);

        return redirect()->back()->with('success', 'File berhasil diupload!');
    }

    public function deleteMedia(Request $request)
    {
        $request->validate([
            'path' => 'required|string|max:500',
        ]);

        $path = str_replace('\\', '/', (string) $request->input('path'));

        if (
            $path === ''
            || str_contains($path, '..')
            || str_contains($path, "\0")
            || str_starts_with($path, '/')
            || preg_match('#^[a-zA-Z]:#', $path)
        ) {
            return redirect()->back()->with('error', 'Path file tidak valid.');
        }

        // Block penulis libraries and article featured images from admin media UI
        if (
            str_starts_with($path, 'media/penulis/')
            || str_starts_with($path, 'articles/')
        ) {
            ActivityLogHelper::logSecurity('media.delete.blocked', 'Admin diblok menghapus media penulis/artikel lewat media library', [
                'path' => $path,
            ]);

            return redirect()->back()->with(
                'error',
                'Media penulis atau gambar artikel tidak dapat dihapus dari Media Library. Kelola lewat modul Artikel / Penulis.'
            );
        }

        // Allow only admin library + legacy root files
        $allowed = str_starts_with($path, 'media/admin/')
            || !str_contains($path, '/');

        if (!$allowed) {
            return redirect()->back()->with('error', 'File di luar ruang media admin tidak dapat dihapus di sini.');
        }

        $disk = \Illuminate\Support\Facades\Storage::disk('public');

        if (!$disk->exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan!');
        }

        $disk->delete($path);
        ActivityLogHelper::log('media.deleted', 'File dihapus: ' . $path, ['path' => $path]);

        return redirect()->back()->with('success', 'File berhasil dihapus!');
    }

    // Analytics
    public function analytics(Request $request, AnalyticsService $analyticsService)
    {
        $days = (int) $request->input('days', 30);
        if (!in_array($days, [7, 30, 90], true)) {
            $days = 30;
        }

        $engagement = $analyticsService->getEngagementMetrics($days);
        $popularArticles = $analyticsService->getArticlePerformance(10);
        $categoryStats = $analyticsService->getArticlePerformanceByCategory();

        $stats = [
            'total_articles' => Article::count(),
            'published_articles' => Article::published()->count(),
            'total_views' => (int) Article::sum('views'),
            'total_users' => User::count(),
            'total_comments' => Comment::count(),
            'period_views' => $engagement['total_views'] ?? 0,
            'period_articles' => $engagement['total_articles'] ?? 0,
            'period_comments' => $engagement['total_comments'] ?? 0,
            'days' => $days,
        ];

        $chartData = $engagement['trends'] ?? [];

        return view('admin.analytics.index', compact(
            'stats',
            'chartData',
            'popularArticles',
            'categoryStats',
            'days'
        ));
    }

    // Article reports (user/guest flagging)
    public function articleReports(Request $request)
    {
        $status = $request->get('status', 'open');
        $query = ArticleReport::with(['article.author', 'article.category', 'user', 'resolver'])
            ->latest();

        if (in_array($status, ['open', 'resolved', 'dismissed'], true)) {
            $query->where('status', $status);
        }

        $reports = $query->paginate(20)->withQueryString();
        $openCount = ArticleReport::open()->count();

        return view('admin.article-reports.index', compact('reports', 'status', 'openCount'));
    }

    public function dismissArticleReport(Request $request, ArticleReport $articleReport)
    {
        if ($articleReport->status !== 'open') {
            return back()->with('error', 'Laporan ini sudah diproses.');
        }

        $request->validate([
            'resolution_note' => 'nullable|string|max:1000',
        ]);

        $articleReport->update([
            'status' => 'dismissed',
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
            'resolution_note' => $request->resolution_note,
        ]);

        ActivityLogHelper::log(
            'article_report.dismissed',
            'Laporan artikel diabaikan #' . $articleReport->id,
            ['article_report_id' => $articleReport->id, 'article_id' => $articleReport->article_id]
        );

        return back()->with('success', 'Laporan diabaikan.');
    }

    public function resolveArticleReport(Request $request, ArticleReport $articleReport)
    {
        if ($articleReport->status !== 'open') {
            return back()->with('error', 'Laporan ini sudah diproses.');
        }

        $request->validate([
            'action' => 'required|in:resolve,suspend',
            'resolution_note' => 'nullable|string|max:1000',
            'reason' => 'required_if:action,suspend|nullable|string|max:1000',
        ], [
            'reason.required_if' => 'Alasan penangguhan wajib diisi.',
        ]);

        $article = $articleReport->article;

        if ($request->action === 'suspend') {
            if (!$article || !in_array($article->status, ['published', 'archived'], true)) {
                return back()->with('error', 'Artikel tidak dapat ditangguhkan dari status saat ini.');
            }

            $this->authorize('suspend', $article);

            $reason = $request->reason ?: ('Laporan pengguna: ' . $articleReport->reasonLabel());

            $article->update([
                'status' => 'suspended',
                'suspension_reason' => $reason,
                'suspended_at' => now(),
                'suspended_by' => Auth::id(),
                'is_featured' => false,
                'is_breaking' => false,
            ]);

            CacheHelper::clearArticleCache();
            CacheHelper::clearDashboardCache();

            ActivityLogHelper::logArticle(
                'article.suspended',
                $article,
                "Penayangan '{$article->title}' ditangguhkan dari laporan #{$articleReport->id}: {$reason}"
            );
        }

        $articleReport->update([
            'status' => 'resolved',
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
            'resolution_note' => $request->resolution_note ?: ($request->action === 'suspend' ? 'Artikel ditangguhkan' : 'Ditandai selesai'),
        ]);

        // Tandai laporan open lain untuk artikel yang sama sebagai resolved jika ditangguhkan
        if ($request->action === 'suspend' && $article) {
            ArticleReport::query()
                ->where('article_id', $article->id)
                ->where('status', 'open')
                ->where('id', '!=', $articleReport->id)
                ->update([
                    'status' => 'resolved',
                    'resolved_by' => Auth::id(),
                    'resolved_at' => now(),
                    'resolution_note' => 'Diselesaikan bersama penangguhan artikel',
                ]);
        }

        ActivityLogHelper::log(
            'article_report.resolved',
            'Laporan artikel diselesaikan #' . $articleReport->id,
            [
                'article_report_id' => $articleReport->id,
                'article_id' => $articleReport->article_id,
                'action' => $request->action,
            ]
        );

        $msg = $request->action === 'suspend'
            ? 'Laporan diselesaikan dan penayangan artikel ditangguhkan.'
            : 'Laporan ditandai selesai.';

        return back()->with('success', $msg);
    }

    // Reports
    public function reports()
    {
        $reports = [
            'articles_by_category' => \App\Models\Category::withCount('articles')->get(),
            'articles_by_author' => User::where('role', 'penulis')->withCount('articles')->orderByDesc('articles_count')->get(),
            'monthly_stats' => $this->getMonthlyStats(),
        ];

        return view('admin.reports.index', compact('reports'));
    }

    public function exportReport(Request $request)
    {
        $type = $request->input('type', 'articles');
        if (!in_array($type, ['articles', 'users', 'comments', 'newsletter'], true)) {
            return redirect()->back()->with('error', 'Tipe laporan tidak valid.');
        }

        $filename = 'laporan_' . $type . '_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($type) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fwrite($out, "\xEF\xBB\xBF");

            switch ($type) {
                case 'articles':
                    fputcsv($out, ['ID', 'Judul', 'Slug', 'Status', 'Kategori', 'Penulis', 'Views', 'Published At', 'Created At']);
                    Article::with(['category', 'author'])->orderBy('id')->chunk(200, function ($rows) use ($out) {
                        foreach ($rows as $article) {
                            fputcsv($out, [
                                $article->id,
                                $article->title,
                                $article->slug,
                                $article->status,
                                $article->category->name ?? '',
                                $article->author->name ?? '',
                                $article->views,
                                optional($article->published_at)->format('Y-m-d H:i:s'),
                                optional($article->created_at)->format('Y-m-d H:i:s'),
                            ]);
                        }
                    });
                    break;

                case 'users':
                    fputcsv($out, ['ID', 'Nama', 'Email', 'Role', 'Verified', 'Created At']);
                    User::orderBy('id')->chunk(200, function ($rows) use ($out) {
                        foreach ($rows as $user) {
                            fputcsv($out, [
                                $user->id,
                                $user->name,
                                $user->email,
                                $user->role,
                                $user->verified ? 'yes' : 'no',
                                optional($user->created_at)->format('Y-m-d H:i:s'),
                            ]);
                        }
                    });
                    break;

                case 'comments':
                    fputcsv($out, ['ID', 'Artikel', 'User', 'Konten', 'Approved', 'Created At']);
                    Comment::with(['article', 'user'])->orderBy('id')->chunk(200, function ($rows) use ($out) {
                        foreach ($rows as $comment) {
                            fputcsv($out, [
                                $comment->id,
                                $comment->article->title ?? '',
                                $comment->user->name ?? '',
                                $comment->comment,
                                $comment->is_approved ? 'yes' : 'no',
                                optional($comment->created_at)->format('Y-m-d H:i:s'),
                            ]);
                        }
                    });
                    break;

                case 'newsletter':
                    fputcsv($out, ['ID', 'Email', 'Aktif', 'Subscribed At']);
                    \App\Models\NewsletterSubscriber::orderBy('id')->chunk(200, function ($rows) use ($out) {
                        foreach ($rows as $sub) {
                            fputcsv($out, [
                                $sub->id,
                                $sub->email,
                                $sub->is_active ? 'yes' : 'no',
                                optional($sub->created_at)->format('Y-m-d H:i:s'),
                            ]);
                        }
                    });
                    break;
            }

            fclose($out);
        };

        ActivityLogHelper::log('reports.export', "Export laporan {$type}");

        return response()->stream($callback, 200, $headers);
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
        $type = $request->input('type', 'database');
        if (!in_array($type, ['database', 'files', 'full'], true)) {
            $type = 'database';
        }

        try {
            switch ($type) {
                case 'database':
                    $result = $backupService->createDatabaseBackup();
                    if ($result) {
                        ActivityLogHelper::log('backup.created', 'Database backup created: ' . basename($result));
                        return redirect()->back()->with('success', 'Database backup berhasil dibuat!');
                    }
                    break;

                case 'files':
                    $result = $backupService->createFilesBackup();
                    if ($result) {
                        ActivityLogHelper::log('backup.created', 'Files backup created: ' . basename($result));
                        return redirect()->back()->with('success', 'Files backup berhasil dibuat!');
                    }
                    break;

                case 'full':
                default:
                    $results = $backupService->createFullBackup();
                    if ($results['success']) {
                        ActivityLogHelper::log('backup.created', 'Full backup created');
                        return redirect()->back()->with('success', 'Full backup berhasil dibuat!');
                    }
                    break;
            }

            return redirect()->back()->with('error', 'Backup gagal dibuat!');
        } catch (\Exception $e) {
            ActivityLogHelper::log('backup.error', 'Backup failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat backup: ' . $e->getMessage());
        }
    }

    public function downloadBackup($backup, BackupService $backupService)
    {
        $backup = basename((string) $backup);
        $backups = $backupService->getBackups();
        $backupFile = collect($backups)->firstWhere('filename', $backup);

        if (!$backupFile || !file_exists($backupFile['path'])) {
            return redirect()->back()->with('error', 'File backup tidak ditemukan!');
        }

        ActivityLogHelper::log('backup.downloaded', 'Backup downloaded: ' . $backup);

        return response()->download($backupFile['path'], $backup);
    }

    public function deleteBackup($backup, BackupService $backupService)
    {
        $backup = basename((string) $backup);
        $backups = $backupService->getBackups();
        $backupFile = collect($backups)->firstWhere('filename', $backup);

        if (!$backupFile || !file_exists($backupFile['path'])) {
            return redirect()->back()->with('error', 'File backup tidak ditemukan!');
        }

        if (@unlink($backupFile['path'])) {
            ActivityLogHelper::log('backup.deleted', 'Backup deleted: ' . $backup);
            return redirect()->back()->with('success', 'Backup berhasil dihapus!');
        }

        return redirect()->back()->with('error', 'Gagal menghapus backup!');
    }

    // System Logs
    public function logs(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $level = strtoupper((string) $request->input('level', ''));
        $search = trim((string) $request->input('q', ''));
        $allowedLevels = ['ERROR', 'WARNING', 'INFO', 'DEBUG', 'CRITICAL', 'ALERT', 'EMERGENCY'];
        if ($level !== '' && !in_array($level, $allowedLevels, true)) {
            $level = '';
        }

        $logs = [];
        $fileSize = 0;
        $lastModified = null;

        if (file_exists($logFile)) {
            $fileSize = filesize($logFile) ?: 0;
            $lastModified = filemtime($logFile);
            $raw = @file($logFile, FILE_IGNORE_NEW_LINES);
            if (is_array($raw)) {
                $raw = array_slice($raw, -500);
                $logs = array_values(array_filter($raw, function ($line) use ($level, $search) {
                    if ($level !== '' && stripos($line, '.' . $level) === false && stripos($line, $level) === false) {
                        return false;
                    }
                    if ($search !== '' && stripos($line, $search) === false) {
                        return false;
                    }
                    return true;
                }));
                $logs = array_slice($logs, -100);
            }
        }

        return view('admin.logs.index', compact('logs', 'level', 'search', 'fileSize', 'lastModified'));
    }

    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');

        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
            ActivityLogHelper::log('logs.cleared', 'Laravel log cleared');
            return redirect()->route('admin.logs.index')->with('success', 'Log berhasil dibersihkan!');
        }

        return redirect()->route('admin.logs.index')->with('error', 'File log tidak ditemukan!');
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
