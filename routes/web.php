<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminArticleController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\PenulisDashboardController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserFeatureController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\WidgetController;
use App\Http\Controllers\Admin\ContactImportantController;
use App\Http\Controllers\EventPopupController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\ImpersonationController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Static pages
Route::get('/terms', [StaticPageController::class, 'terms'])->name('terms');
Route::get('/privacy', [StaticPageController::class, 'privacy'])->name('privacy');
// Route::get('/tentang', [HomeController::class, 'about'])->name('about'); // Disembunyikan dari publik

// Sitemap
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-{part}.xml', [\App\Http\Controllers\SitemapController::class, 'part'])->name('sitemap.part')->where('part', '[0-9]+');
Route::get('/sitemap-news.xml', [\App\Http\Controllers\SitemapController::class, 'news'])->name('sitemap.news');

// Robots.txt (dynamic)
Route::get('/robots.txt', [\App\Http\Controllers\RobotsController::class, 'index'])->name('robots');

// Article routes — listing tunggal; /artikel diarahkan ke /berita
Route::get('/berita', [ArticleController::class, 'index'])->name('articles.index');
Route::redirect('/artikel', '/berita')->name('articles.artikel');
Route::post('/berita/{article}/report', [ArticleReportController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('articles.report');

// Events routes
Route::get('/agenda', [WidgetController::class, 'eventsIndex'])->name('events.index');

// Legacy public URLs (redirect ke /{kategori}/...)
Route::get('/articles/{article}', function (\App\Models\Article $article) {
    return redirect()->to($article->publicUrl(), 301);
})->name('articles.show.legacy');

Route::get('/categories/{category}', function (\App\Models\Category $category) {
    return redirect()->to(route('categories.show', $category), 301);
})->name('categories.show.legacy');

// Newsletter routes
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Search routes
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
Route::get('/search/popular', [SearchController::class, 'popular'])->name('search.popular');

// Comment routes — store terbuka untuk guest (moderasi); like/edit/hapus tetap auth
Route::post('/comments', [CommentController::class, 'store'])
    ->middleware(['rate.limit.comments'])
    ->name('comments.store');
Route::post('/comments/{comment}/like', [CommentController::class, 'like'])
    ->middleware(['auth'])
    ->name('comments.like');
Route::put('/comments/{comment}', [CommentController::class, 'update'])
    ->middleware(['auth'])
    ->name('comments.update');
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('comments.destroy');

// Widget API routes
Route::prefix('api/widgets')->middleware('rate.limit.api:60,60')->group(function () {
    Route::get('/weather', [WidgetController::class, 'getWeather'])->name('widgets.weather');
    Route::get('/prayer-times', [WidgetController::class, 'getPrayerTimes'])->name('widgets.prayer-times');
    Route::get('/next-prayer', [WidgetController::class, 'getNextPrayer'])->name('widgets.next-prayer');
    Route::get('/maritime', [WidgetController::class, 'getMaritime'])->name('widgets.maritime');
    Route::get('/contact-importants', [WidgetController::class, 'getContactImportants'])->name('widgets.contact-importants');
    Route::get('/events', [WidgetController::class, 'getEvents'])->name('widgets.events');
    Route::get('/active-poll', [WidgetController::class, 'getActivePoll'])->name('widgets.active-poll');
    Route::post('/submit-poll-vote', [WidgetController::class, 'submitPollVote'])->name('widgets.submit-poll-vote');
    Route::get('/poll-results', [WidgetController::class, 'getPollResults'])->name('widgets.poll-results');
    Route::get('/all', [WidgetController::class, 'getAllWidgets'])->name('widgets.all');
});

// Google OAuth routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Dashboard route (redirects based on role)
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Refresh user dari database untuk memastikan data terbaru (terutama role)
    // Ini penting ketika role user berubah saat mereka masih login
    $user->refresh();
    
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isEditor()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isPenulis()) {
        return redirect()->route('penulis.dashboard');
    } else {
        return redirect()->route('user.dashboard');
    }
})->middleware(['auth'])->name('dashboard');

// User-only area (admin/editor/penulis redirected away — no accidental fall-in)
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/upgrade-request', [UserProfileController::class, 'upgradeRequest'])->name('user.upgrade-request');
    Route::post('/upgrade-request', [UserProfileController::class, 'submitUpgradeRequest'])->name('user.submit-upgrade-request');
    Route::post('/user/ban-appeal', [UserDashboardController::class, 'submitBanAppeal'])->name('user.ban-appeal');
    Route::put('/user/comments/{comment}', [UserDashboardController::class, 'updateComment'])->name('user.comments.update');
    Route::delete('/user/comments/{comment}', [UserDashboardController::class, 'destroyComment'])->name('user.comments.destroy');
});

// Shared authenticated user features (any logged-in role)
Route::middleware(['auth'])->group(function () {
    Route::post('/articles/{article}/bookmark', [UserFeatureController::class, 'toggleBookmark'])->name('articles.bookmark');
    Route::get('/user/bookmarks', [UserFeatureController::class, 'bookmarks'])->name('user.bookmarks');
    Route::get('/user/reading-history', [UserFeatureController::class, 'readingHistory'])->name('user.reading-history');
    Route::post('/users/{user}/follow', [UserFeatureController::class, 'toggleFollow'])->name('users.follow');
    Route::get('/user/following', [UserFeatureController::class, 'following'])->name('user.following');
    Route::get('/user/followers', [UserFeatureController::class, 'followers'])->name('user.followers');
});

// Penulis routes
Route::middleware(['auth', 'role:penulis'])->prefix('penulis')->name('penulis.')->group(function () {
    Route::get('/dashboard', [PenulisDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [PenulisDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [PenulisDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/verification/request', [PenulisDashboardController::class, 'requestVerification'])->name('verification.request');
    Route::post('/verification/request', [PenulisDashboardController::class, 'submitVerificationRequest'])->name('verification.submit');
    
    // Article management
    Route::get('/articles', [PenulisDashboardController::class, 'articles'])->name('articles.index');
    Route::get('/articles/create', [PenulisDashboardController::class, 'create'])->name('articles.create');
    Route::post('/articles', [PenulisDashboardController::class, 'store'])->name('articles.store');
    Route::post('/articles/bulk', [PenulisDashboardController::class, 'bulkArticles'])->name('articles.bulk');
    Route::post('/articles/save-draft', [PenulisDashboardController::class, 'saveDraft'])->name('articles.save-draft-create');
    Route::get('/articles/{article}', [PenulisDashboardController::class, 'show'])->name('articles.show');
    Route::get('/articles/{article}/edit', [PenulisDashboardController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}', [PenulisDashboardController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [PenulisDashboardController::class, 'destroy'])->name('articles.destroy');
    Route::post('/articles/{article}/save-draft', [PenulisDashboardController::class, 'saveDraft'])->name('articles.save-draft');
    
    // Comments management
    Route::get('/articles/{article}/comments', [PenulisDashboardController::class, 'comments'])->name('articles.comments');
    Route::post('/articles/{article}/comments/{comment}/status', [PenulisDashboardController::class, 'updateCommentStatus'])->name('articles.comments.status');
    Route::delete('/articles/{article}/comments/{comment}', [PenulisDashboardController::class, 'deleteComment'])->name('articles.comments.delete');
    
    // Additional features
    Route::post('/articles/{article}/duplicate', [PenulisDashboardController::class, 'duplicate'])->name('articles.duplicate');
    Route::get('/articles/{article}/export', [PenulisDashboardController::class, 'export'])->name('articles.export');
    
    // Analytics Dashboard
    Route::get('/analytics', [PenulisDashboardController::class, 'analytics'])->name('analytics');
    
    // Media Library
    Route::get('/media', [PenulisDashboardController::class, 'mediaLibrary'])->name('media.index');
    Route::post('/media/upload', [PenulisDashboardController::class, 'uploadMedia'])->name('media.upload');
    Route::delete('/media/delete', [PenulisDashboardController::class, 'deleteMedia'])->name('media.delete');
    
    // Advanced Comment Management
    Route::get('/comments', [PenulisDashboardController::class, 'commentsAdvanced'])->name('comments.advanced');
    Route::post('/comments/bulk-action', [PenulisDashboardController::class, 'bulkCommentAction'])->name('comments.bulk-action');
    Route::post('/articles/{article}/comments/{comment}/reply', [PenulisDashboardController::class, 'replyComment'])->name('articles.comments.reply');
    Route::get('/articles/{article}/comments/export', [PenulisDashboardController::class, 'exportComments'])->name('articles.comments.export');
    
    // SEO Tools
    Route::get('/seo', [PenulisDashboardController::class, 'seoTools'])->name('seo.index');
    Route::get('/seo/analyze/{article}', [PenulisDashboardController::class, 'seoTools'])->name('seo.analyze');
});

// Public penulis profile route (must be after penulis group to avoid conflicts)
Route::get('/penulis/{username}', [UserProfileController::class, 'show'])->name('penulis.public-profile');

// Shared admin panel (admin + editor): dashboard, suspended list, reports, suspend/unsuspend
// Must be registered BEFORE admin-only routes so these URIs are not shadowed.
Route::middleware(['auth', 'role:admin,editor'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/articles/suspended', [AdminDashboardController::class, 'suspendedArticles'])->name('articles.suspended');
    Route::get('/articles/moderate', [AdminDashboardController::class, 'moderateArticles'])->name('articles.moderate');
    Route::get('/articles/{article}/detail', [AdminDashboardController::class, 'articleDetail'])->name('articles.detail');
    Route::post('/articles/{article}/suspend', [AdminArticleController::class, 'suspend'])->name('articles.suspend');
    Route::post('/articles/{article}/unsuspend', [AdminArticleController::class, 'unsuspend'])->name('articles.unsuspend');
    Route::get('/article-reports', [AdminDashboardController::class, 'articleReports'])->name('article-reports.index');
    Route::post('/article-reports/{articleReport}/dismiss', [AdminDashboardController::class, 'dismissArticleReport'])->name('article-reports.dismiss');
    Route::post('/article-reports/{articleReport}/resolve', [AdminDashboardController::class, 'resolveArticleReport'])->name('article-reports.resolve');
});

// Admin-only routes (full panel)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Widget Management Routes
    Route::resource('events', \App\Http\Controllers\Admin\EventController::class);
    Route::post('events/bulk-action', [\App\Http\Controllers\Admin\EventController::class, 'bulkAction'])->name('events.bulk-action');
    Route::post('events/{event}/toggle-status', [\App\Http\Controllers\Admin\EventController::class, 'toggleStatus'])->name('events.toggle-status');
    
    Route::resource('polls', \App\Http\Controllers\Admin\PollController::class);
    Route::post('polls/bulk-action', [\App\Http\Controllers\Admin\PollController::class, 'bulkAction'])->name('polls.bulk-action');
    Route::post('polls/{poll}/toggle-status', [\App\Http\Controllers\Admin\PollController::class, 'toggleStatus'])->name('polls.toggle-status');
    Route::post('polls/{poll}/reset-votes', [\App\Http\Controllers\Admin\PollController::class, 'resetVotes'])->name('polls.reset-votes');
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
    Route::post('/users/bulk', [AdminDashboardController::class, 'bulkUsers'])->name('users.bulk');
    Route::post('/users/{user}/mark-redaksi', [AdminDashboardController::class, 'markAsRedaksi'])->name('users.mark-redaksi');
    Route::post('/users/{user}/toggle-verified', [AdminDashboardController::class, 'toggleVerified'])->name('users.toggle-verified');
    Route::get('/verification-requests', [AdminDashboardController::class, 'verificationRequests'])->name('verification-requests');
    Route::post('/verification-requests/bulk', [AdminDashboardController::class, 'bulkVerificationRequests'])->name('verification-requests.bulk');
    Route::post('/verification-requests/{user}/approve', [AdminDashboardController::class, 'approveVerification'])->name('verification-requests.approve');
    Route::post('/verification-requests/{user}/reject', [AdminDashboardController::class, 'rejectVerification'])->name('verification-requests.reject');
    Route::get('/ban-appeals', [AdminDashboardController::class, 'banAppeals'])->name('ban-appeals');
    Route::post('/ban-appeals/{user}/approve', [AdminDashboardController::class, 'approveBanAppeal'])->name('ban-appeals.approve');
    Route::post('/ban-appeals/{user}/reject', [AdminDashboardController::class, 'rejectBanAppeal'])->name('ban-appeals.reject');
    Route::post('/users/{user}/lift-ban', [AdminDashboardController::class, 'liftBan'])->name('users.lift-ban');
    
    // Admin Article CRUD
    Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/create', [AdminArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [AdminArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article}/admin', [AdminArticleController::class, 'show'])->name('articles.show');
    Route::get('/articles/{article}/edit', [AdminArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}', [AdminArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');
    Route::post('/articles/bulk', [AdminArticleController::class, 'bulkAction'])->name('articles.bulk');
    Route::post('/articles/{article}/toggle-featured', [AdminArticleController::class, 'toggleFeatured'])->name('articles.toggle-featured');
    Route::post('/articles/{article}/toggle-breaking', [AdminArticleController::class, 'toggleBreaking'])->name('articles.toggle-breaking');
    Route::post('/articles/{article}/archive', [AdminArticleController::class, 'archive'])->name('articles.archive');
    
    // Categories Management
    Route::get('/categories', [AdminDashboardController::class, 'categories'])->name('categories.index');
    Route::get('/categories/{category}/edit', [AdminDashboardController::class, 'editCategory'])->name('categories.edit');
    Route::post('/categories', [AdminDashboardController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [AdminDashboardController::class, 'updateCategory'])->name('categories.update');
    Route::post('/categories/bulk', [AdminDashboardController::class, 'bulkCategories'])->name('categories.bulk');
    Route::delete('/categories/{category}', [AdminDashboardController::class, 'destroyCategory'])->name('categories.destroy');
    
    // Comments Management
    Route::get('/comments', [AdminDashboardController::class, 'comments'])->name('comments.index');
    Route::post('/comments/bulk', [AdminDashboardController::class, 'bulkComments'])->name('comments.bulk');
    Route::post('/comments/{comment}/approve', [AdminDashboardController::class, 'approveComment'])->name('comments.approve');
    Route::post('/comments/{comment}/reject', [AdminDashboardController::class, 'rejectComment'])->name('comments.reject');
    Route::delete('/comments/{comment}', [AdminDashboardController::class, 'destroyComment'])->name('comments.destroy');
    
    // Penulis Management
    Route::get('/penulis', [AdminDashboardController::class, 'penulis'])->name('penulis.index');
    Route::post('/penulis/bulk', [AdminDashboardController::class, 'bulkPenulis'])->name('penulis.bulk');
    Route::post('/penulis/{user}/mark-redaksi', [AdminDashboardController::class, 'markAsRedaksi'])->name('penulis.mark-redaksi');
    Route::post('/penulis/{user}/unmark-redaksi', [AdminDashboardController::class, 'unmarkRedaksi'])->name('penulis.unmark-redaksi');
    Route::post('/penulis/{user}/demote', [AdminDashboardController::class, 'demoteFromPenulis'])->name('penulis.demote');
    Route::post('/penulis/{user}/warn', [AdminDashboardController::class, 'warnPenulis'])->name('penulis.warn');
    Route::post('/penulis/{user}/restrict-publish', [AdminDashboardController::class, 'restrictPenulisPublish'])->name('penulis.restrict-publish');
    Route::post('/penulis/{user}/clear-publish-restriction', [AdminDashboardController::class, 'clearPenulisPublishRestriction'])->name('penulis.clear-publish-restriction');
    Route::post('/penulis/{user}/revoke-verified', [AdminDashboardController::class, 'revokePenulisVerified'])->name('penulis.revoke-verified');
    Route::post('/penulis/{user}/impersonate', [ImpersonationController::class, 'start'])->name('penulis.impersonate');
    
    // Newsletter Management
    Route::get('/newsletter', [AdminDashboardController::class, 'newsletter'])->name('newsletter.index');
    Route::get('/newsletter/export', [AdminDashboardController::class, 'exportNewsletter'])->name('newsletter.export');
    Route::post('/newsletter/send', [AdminDashboardController::class, 'sendNewsletter'])->name('newsletter.send');
    Route::post('/newsletter/bulk', [AdminDashboardController::class, 'bulkNewsletter'])->name('newsletter.bulk');
    Route::delete('/newsletter/{subscriber}', [AdminDashboardController::class, 'removeSubscriber'])->name('newsletter.remove');
    
    // Media Library
    Route::get('/media', [AdminDashboardController::class, 'media'])->name('media.index');
    Route::post('/media/upload', [AdminDashboardController::class, 'uploadMedia'])->name('media.upload');
    Route::delete('/media/delete', [AdminDashboardController::class, 'deleteMedia'])->name('media.delete');
    
    // Analytics
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics.index');
    
    // Reports
    Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports.index');
    Route::get('/reports/export', [AdminDashboardController::class, 'exportReport'])->name('reports.export');
    
    // Backup
    Route::get('/backup', [AdminDashboardController::class, 'backup'])->name('backup.index');
    Route::post('/backup/create', [AdminDashboardController::class, 'createBackup'])->name('backup.create');
    Route::get('/backup/download/{backup}', [AdminDashboardController::class, 'downloadBackup'])->name('backup.download');
    Route::delete('/backup/{backup}', [AdminDashboardController::class, 'deleteBackup'])->name('backup.delete');
    
    // System Logs
    Route::get('/logs', [AdminDashboardController::class, 'logs'])->name('logs.index');
    Route::post('/logs/clear', [AdminDashboardController::class, 'clearLogs'])->name('logs.clear');
    
    // Contact Importants Management (export/bulk BEFORE resource so /export is not captured as {id})
    Route::get('/contact-importants/export', [ContactImportantController::class, 'export'])->name('contact-importants.export');
    Route::post('/contact-importants/bulk-activate', [ContactImportantController::class, 'bulkActivate'])->name('contact-importants.bulk-activate');
    Route::post('/contact-importants/bulk-deactivate', [ContactImportantController::class, 'bulkDeactivate'])->name('contact-importants.bulk-deactivate');
    Route::post('/contact-importants/bulk-delete', [ContactImportantController::class, 'bulkDelete'])->name('contact-importants.bulk-delete');
    Route::resource('contact-importants', ContactImportantController::class);
    Route::patch('/contact-importants/{contactImportant}/toggle-status', [ContactImportantController::class, 'toggleStatus'])->name('contact-importants.toggle-status');
    
    // Event Popup Management
    Route::resource('event-popups', EventPopupController::class);
    Route::post('/event-popups/bulk', [EventPopupController::class, 'bulkAction'])->name('event-popups.bulk');
    Route::patch('/event-popups/{eventPopup}/toggle-status', [EventPopupController::class, 'toggleStatus'])->name('event-popups.toggle-status');
    
    // Admin Settings
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/general', [AdminSettingsController::class, 'updateGeneral'])->name('settings.general');
    Route::post('/settings/logo', [AdminSettingsController::class, 'updateLogo'])->name('settings.logo');
    Route::post('/settings/about', [AdminSettingsController::class, 'updateAbout'])->name('settings.about');
    Route::post('/settings/editorial', [AdminSettingsController::class, 'updateEditorial'])->name('settings.editorial');
    Route::post('/settings/seo', [AdminSettingsController::class, 'updateSeo'])->name('settings.seo');
    Route::post('/settings/system', [AdminSettingsController::class, 'updateSystem'])->name('settings.system');
    Route::post('/settings/clear-cache', [AdminSettingsController::class, 'clearCache'])->name('settings.clear-cache');
});

// Default auth routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Leave impersonation while acting as the target user (must not sit behind role:admin)
    Route::post('/impersonation/leave', [ImpersonationController::class, 'leave'])->name('impersonation.leave');
});

// Error testing routes (only in development)
if (config('app.debug')) {
    Route::get('/test-error/403', function () {
        abort(403, 'Test 403 error');
    })->name('test.403');
    
    Route::get('/test-error/404', function () {
        abort(404, 'Test 404 error');
    })->name('test.404');
    
    Route::get('/test-error/500', function () {
        abort(500, 'Test 500 error');
    })->name('test.500');
    
    Route::get('/test-error/405', function () {
        abort(405, 'Test 405 error');
    })->name('test.405');
    
    Route::get('/test-error/419', function () {
        abort(419, 'Test 419 error');
    })->name('test.419');
    
}

require __DIR__.'/auth.php';

// Pretty public URLs: /{kategori} dan /{kategori}/{artikel}
// Harus di akhir agar tidak menabrak route lain (admin, user, auth, dll.)
$reservedCategorySlugs = implode('|', [
    'admin', 'penulis', 'user', 'auth', 'api', 'search', 'berita', 'artikel',
    'articles', 'categories', 'login', 'register', 'password', 'forgot-password',
    'reset-password', 'confirm-password', 'verify-email', 'email', 'newsletter',
    'comments', 'agenda', 'terms', 'privacy', 'dashboard', 'upgrade-request',
    'profile', 'storage', 'up', 'sanctum', 'livewire', 'build', 'test-error',
]);

// Segmen pertama yang punya sub-route (hindari konflik dengan /{kategori}/{artikel})
$reservedArticlePrefixes = implode('|', [
    'admin', 'penulis', 'user', 'auth', 'api', 'articles', 'categories',
    'password', 'reset-password', 'confirm-password', 'verify-email', 'email',
    'newsletter', 'comments', 'upgrade-request', 'test-error', 'sanctum', 'livewire',
]);

Route::get('/{category}', [CategoryController::class, 'show'])
    ->name('categories.show')
    ->where('category', '^(?!' . $reservedCategorySlugs . ')([a-z0-9\-]+)$');

Route::get('/{category}/{article}', [ArticleController::class, 'show'])
    ->name('articles.show')
    ->where('category', '^(?!' . $reservedArticlePrefixes . ')([a-z0-9\-]+)$')
    ->where('article', '[a-z0-9\-]+');
