<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminArticleController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\PenulisDashboardController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\WidgetController;
use App\Http\Controllers\Admin\ContactImportantController;
use App\Http\Controllers\EventPopupController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::get('/tentang', [HomeController::class, 'about'])->name('about'); // Disembunyikan dari publik

// Sitemap
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-{part}.xml', [\App\Http\Controllers\SitemapController::class, 'part'])->name('sitemap.part')->where('part', '[0-9]+');
Route::get('/sitemap-news.xml', [\App\Http\Controllers\SitemapController::class, 'news'])->name('sitemap.news');

// Robots.txt (dynamic)
Route::get('/robots.txt', [\App\Http\Controllers\RobotsController::class, 'index'])->name('robots');

// Article routes (disembunyikan dari public)
// Route /berita disembunyikan - redirect ke home, tapi route name tetap ada untuk link internal
Route::get('/berita', function() {
    return redirect()->route('home');
})->name('articles.index');
Route::get('/artikel', [ArticleController::class, 'artikel'])->name('articles.artikel');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

// Events routes
Route::get('/agenda', [WidgetController::class, 'eventsIndex'])->name('events.index');

// Category routes
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

// Newsletter routes
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Search routes
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
Route::get('/search/popular', [SearchController::class, 'popular'])->name('search.popular');

// Comment routes (only for authenticated users)
Route::post('/comments', [CommentController::class, 'store'])
    ->middleware(['auth', 'rate.limit.comments'])
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
    
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isEditor()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isPenulis()) {
        return redirect()->route('penulis.dashboard');
    } else {
        return redirect()->route('home');
    }
})->middleware(['auth'])->name('dashboard');

// User routes
Route::middleware(['auth'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/upgrade-request', [UserProfileController::class, 'upgradeRequest'])->name('user.upgrade-request');
    Route::post('/upgrade-request', [UserProfileController::class, 'submitUpgradeRequest'])->name('user.submit-upgrade-request');
    
    // User comment management
    Route::put('/user/comments/{comment}', [UserDashboardController::class, 'updateComment'])->name('user.comments.update');
    Route::delete('/user/comments/{comment}', [UserDashboardController::class, 'destroyComment'])->name('user.comments.destroy');
});

// Penulis routes
Route::middleware(['auth', 'role:penulis'])->prefix('penulis')->name('penulis.')->group(function () {
    Route::get('/dashboard', [PenulisDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [PenulisDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [PenulisDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/verification/request', [PenulisDashboardController::class, 'requestVerification'])->name('verification.request');
    Route::post('/verification/request', [PenulisDashboardController::class, 'submitVerificationRequest'])->name('verification.submit');
    
    // Article management
    Route::get('/articles/create', [PenulisDashboardController::class, 'create'])->name('articles.create');
    Route::post('/articles', [PenulisDashboardController::class, 'store'])->name('articles.store');
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
});

// Public penulis profile route (must be after penulis group to avoid conflicts)
Route::get('/penulis/{username}', [UserProfileController::class, 'show'])->name('penulis.public-profile');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Widget Management Routes
    Route::resource('events', \App\Http\Controllers\Admin\EventController::class);
    Route::post('events/bulk-action', [\App\Http\Controllers\Admin\EventController::class, 'bulkAction'])->name('events.bulk-action');
    Route::post('events/{event}/toggle-status', [\App\Http\Controllers\Admin\EventController::class, 'toggleStatus'])->name('events.toggle-status');
    
    Route::resource('polls', \App\Http\Controllers\Admin\PollController::class);
    Route::post('polls/bulk-action', [\App\Http\Controllers\Admin\PollController::class, 'bulkAction'])->name('polls.bulk-action');
    Route::post('polls/{poll}/toggle-status', [\App\Http\Controllers\Admin\PollController::class, 'toggleStatus'])->name('polls.toggle-status');
    Route::post('polls/{poll}/reset-votes', [\App\Http\Controllers\Admin\PollController::class, 'resetVotes'])->name('polls.reset-votes');
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
    Route::post('/users/{user}/upgrade', [AdminDashboardController::class, 'upgradeUser'])->name('users.upgrade');
    Route::post('/users/{user}/toggle-verified', [AdminDashboardController::class, 'toggleVerified'])->name('users.toggle-verified');
    Route::get('/verification-requests', [AdminDashboardController::class, 'verificationRequests'])->name('verification-requests');
    Route::post('/verification-requests/{user}/approve', [AdminDashboardController::class, 'approveVerification'])->name('verification-requests.approve');
    Route::post('/verification-requests/{user}/reject', [AdminDashboardController::class, 'rejectVerification'])->name('verification-requests.reject');
    Route::get('/articles/pending', [AdminDashboardController::class, 'pendingArticles'])->name('articles.pending');
    Route::post('/articles/{article}/approve', [AdminDashboardController::class, 'approveArticle'])->name('articles.approve');
    Route::post('/articles/{article}/reject', [AdminDashboardController::class, 'rejectArticle'])->name('articles.reject');
    Route::get('/articles/{article}/detail', [AdminDashboardController::class, 'articleDetail'])->name('articles.detail');
    Route::post('/articles/bulk-approve', [AdminDashboardController::class, 'bulkApprove'])->name('articles.bulk-approve');
    Route::post('/articles/bulk-reject', [AdminDashboardController::class, 'bulkReject'])->name('articles.bulk-reject');
    
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
    Route::delete('/categories/{category}', [AdminDashboardController::class, 'destroyCategory'])->name('categories.destroy');
    
    // Comments Management
    Route::get('/comments', [AdminDashboardController::class, 'comments'])->name('comments.index');
    Route::post('/comments/{comment}/approve', [AdminDashboardController::class, 'approveComment'])->name('comments.approve');
    Route::post('/comments/{comment}/reject', [AdminDashboardController::class, 'rejectComment'])->name('comments.reject');
    Route::delete('/comments/{comment}', [AdminDashboardController::class, 'destroyComment'])->name('comments.destroy');
    
    // Penulis Management
    Route::get('/penulis', [AdminDashboardController::class, 'penulis'])->name('penulis.index');
    Route::post('/penulis/{user}/promote', [AdminDashboardController::class, 'promoteToPenulis'])->name('penulis.promote');
    Route::post('/penulis/{user}/demote', [AdminDashboardController::class, 'demoteFromPenulis'])->name('penulis.demote');
    
    // Newsletter Management
    Route::get('/newsletter', [AdminDashboardController::class, 'newsletter'])->name('newsletter.index');
    Route::post('/newsletter/send', [AdminDashboardController::class, 'sendNewsletter'])->name('newsletter.send');
    Route::delete('/newsletter/{subscriber}', [AdminDashboardController::class, 'removeSubscriber'])->name('newsletter.remove');
    
    // Media Library
    Route::get('/media', [AdminDashboardController::class, 'media'])->name('media.index');
    Route::post('/media/upload', [AdminDashboardController::class, 'uploadMedia'])->name('media.upload');
    Route::delete('/media/{media}', [AdminDashboardController::class, 'deleteMedia'])->name('media.delete');
    
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
    Route::get('/logs/clear', [AdminDashboardController::class, 'clearLogs'])->name('logs.clear');
    
    // Contact Importants Management
    Route::resource('contact-importants', ContactImportantController::class);
    Route::patch('/contact-importants/{contactImportant}/toggle-status', [ContactImportantController::class, 'toggleStatus'])->name('contact-importants.toggle-status');
    Route::post('/contact-importants/bulk-activate', [ContactImportantController::class, 'bulkActivate'])->name('contact-importants.bulk-activate');
    Route::post('/contact-importants/bulk-deactivate', [ContactImportantController::class, 'bulkDeactivate'])->name('contact-importants.bulk-deactivate');
    Route::post('/contact-importants/bulk-delete', [ContactImportantController::class, 'bulkDelete'])->name('contact-importants.bulk-delete');
    Route::get('/contact-importants/export', [ContactImportantController::class, 'export'])->name('contact-importants.export');
    
    // Event Popup Management
    Route::resource('event-popups', EventPopupController::class);
    Route::patch('/event-popups/{eventPopup}/toggle-status', [EventPopupController::class, 'toggleStatus'])->name('event-popups.toggle-status');
    
    // Admin Settings
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/general', [AdminSettingsController::class, 'updateGeneral'])->name('settings.general');
    Route::post('/settings/logo', [AdminSettingsController::class, 'updateLogo'])->name('settings.logo');
    Route::post('/settings/about', [AdminSettingsController::class, 'updateAbout'])->name('settings.about');
    Route::post('/settings/editorial', [AdminSettingsController::class, 'updateEditorial'])->name('settings.editorial');
    Route::post('/settings/seo', [AdminSettingsController::class, 'updateSeo'])->name('settings.seo');
    Route::post('/settings/system', [AdminSettingsController::class, 'updateSystem'])->name('settings.system');
    Route::get('/settings/clear-cache', [AdminSettingsController::class, 'clearCache'])->name('settings.clear-cache');
});

// Editor routes (can access admin dashboard)
Route::middleware(['auth', 'role:editor'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/articles/pending', [AdminDashboardController::class, 'pendingArticles'])->name('articles.pending');
    Route::post('/articles/{article}/approve', [AdminDashboardController::class, 'approveArticle'])->name('articles.approve');
    Route::post('/articles/{article}/reject', [AdminDashboardController::class, 'rejectArticle'])->name('articles.reject');
    Route::get('/articles/{article}/detail', [AdminDashboardController::class, 'articleDetail'])->name('articles.detail');
    Route::post('/articles/bulk-approve', [AdminDashboardController::class, 'bulkApprove'])->name('articles.bulk-approve');
    Route::post('/articles/bulk-reject', [AdminDashboardController::class, 'bulkReject'])->name('articles.bulk-reject');
});

// Default auth routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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
