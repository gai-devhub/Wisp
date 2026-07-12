<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DatabaseManagerController;
use App\Http\Controllers\ProfilePictureController;
use App\Http\Controllers\GeneratedLinksController;
use App\Http\Controllers\MediaFilesController;
use App\Http\Controllers\MessageViewController;
use App\Http\Controllers\ShareMessagesController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TemplateGalleryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserNotificationsController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\WishMessagesController;
use App\Http\Controllers\GuestMessageController;
use App\Models\WishMessages;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\MessageLifecycleController;
use App\Http\Controllers\PasscodeController;
use App\Http\Controllers\SpotifyPlaybackController;
use App\Http\Controllers\GithubUpdateController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

$appLocked = function () {
    if (!Schema::hasTable('system_settings')) {
        return false;
    }
    return (bool) DB::table('system_settings')->where('key', 'app_locked')->value('value');
};

// ===================== AUTH =====================
Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/auth/spotify', [AuthController::class, 'redirectToSpotify'])->name('auth.spotify');
Route::get('/auth/spotify/callback', [AuthController::class, 'handleSpotifyCallback'])->name('auth.spotify.callback');
Route::post('/password/reset/send-code', [AuthController::class, 'sendResetCode'])->name('auth.password.send-code');
Route::post('/password/reset/verify-code', [AuthController::class, 'verifyResetCode'])->name('auth.password.verify-code');
Route::post('/password/reset/update', [AuthController::class, 'updatePassword'])->name('auth.password.update');

// Fallback GET routes for wizard steps so refresh doesn't 404
Route::get('/forgot-password', [AuthController::class, 'showLogin'])->name('auth.forgot-password.view');
Route::get('/password-code-verification', [AuthController::class, 'showLogin']);
Route::get('/reset-password', [AuthController::class, 'showLogin']);
Route::get('/signup', [AuthController::class, 'showLogin']);
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::get('/account-blocked', [AuthController::class, 'showAccountBlocked'])->name('account.blocked');

// Email Verification Route
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

use App\Http\Controllers\SubscriberController;

// ===================== SUBSCRIBERS =====================
Route::post('/subscribe', [SubscriberController::class, 'store'])->name('subscribe');
Route::get('/unsubscribe/{token}', [SubscriberController::class, 'unsubscribe'])->name('unsubscribe');

// ===================== ADMIN =====================
Route::middleware(['auth', 'admin', 'require.passcode'])->group(function () {
    Route::get('/admin-page', [AdminController::class, 'index'])->name('admin.page');
    Route::get('/admin-page/users', [AdminController::class, 'usersPage'])->name('admin.users.page');
    Route::get('/admin-page/subscribers', [AdminController::class, 'subscribersPage'])->name('admin.subscribers.page');
    Route::post('/admin/subscribers/send-update', [AdminController::class, 'sendUpdateEmail'])->name('admin.subscribers.sendUpdate');
    Route::get('/admin-page/messages', [AdminController::class, 'messagesPage'])->name('admin.messages.page');
    Route::get('/admin-page/view-activity', [AdminController::class, 'viewActivityPageView'])->name('admin.view-activity.page');
    Route::get('/admin-page/search', [AdminController::class, 'searchPage'])->name('admin.search.page');
    Route::get('/admin-page/analytics', [AdminController::class, 'analyticsPage'])->name('admin.analytics.page');
    Route::get('/admin-page/notifications', [AdminController::class, 'notificationsPage'])->name('admin.notifications.page');
    Route::get('/admin-page/ai-usage', [AdminController::class, 'aiUsagePage'])->name('admin.ai-usage.page');
    Route::get('/admin-page/system', [AdminController::class, 'systemPage'])->name('admin.system.page');
    Route::get('/admin-page/settings/messages', [AdminController::class, 'settingsMessagePage'])->name('admin.settings.message.page');
    Route::get('/admin-page/settings/page', [AdminController::class, 'settingsPagePage'])->name('admin.settings.page.page');
    Route::get('/admin-page/settings/user', [AdminController::class, 'settingsUserPage'])->name('admin.settings.user.page');
    Route::get('/admin-page/logs', [AdminController::class, 'logsPage'])->name('admin.logs.page');
    Route::get('/admin-page/maintenance', [AdminController::class, 'maintenancePage'])->name('admin.maintenance.page');
    Route::get('/admin/analytics', [AdminController::class, 'analyticsData'])->name('admin.analytics');
    Route::get('/admin/activity', [AdminController::class, 'activityPage'])->name('admin.activity');
    Route::post('/admin/toggle-lock', [AdminController::class, 'toggleLock'])->name('admin.toggleLock');
    Route::post('/admin/toggle-registration', [AdminController::class, 'toggleRegistration'])->name('admin.toggleRegistration');
    Route::post('/admin/toggle-updates', [AdminController::class, 'toggleSystemUpdates'])->name('admin.toggleUpdates');
    Route::post('/admin/backup', [AdminController::class, 'createBackup'])->name('admin.backup');
    Route::post('/admin/send-notification', [AdminController::class, 'sendNotification'])->name('admin.sendNotification');
    Route::post('/admin/account', [AdminController::class, 'updateAdminAccount'])->name('admin.account');
    Route::post('/admin/security', [AdminController::class, 'saveSecuritySettings'])->name('admin.security');
    Route::post('/admin/preferences', [AdminController::class, 'savePreferences'])->name('admin.preferences');
    Route::post('/admin/privacy-theme', [AdminController::class, 'updatePrivacyAndTheme'])->name('admin.privacy_theme');
    Route::post('/admin/passcode', [AdminController::class, 'updateAdminPasscode'])->name('admin.settings.passcode');
    Route::post('/admin/lifecycle', [AdminController::class, 'updateGlobalLifecycle'])->name('admin.lifecycle');
    Route::post('/admin/notification-settings', [AdminController::class, 'saveNotificationSettings'])->name('admin.notificationSettings');
    Route::post('/admin/contact-settings', [AdminController::class, 'saveContactSettings'])->name('admin.contactSettings');
    Route::post('/admin/optimize-database', [AdminController::class, 'optimizeDatabase'])->name('admin.optimizeDatabase');
    Route::post('/admin/system-updates', [AdminController::class, 'saveSystemUpdates'])->name('admin.system.updates');
    // GitHub Update System
    Route::post('/admin/github/settings', [GithubUpdateController::class, 'saveSettings'])->name('admin.github.settings');
    Route::get('/admin/github/check', [GithubUpdateController::class, 'checkUpdate'])->name('admin.github.check');
    Route::post('/admin/github/update', [GithubUpdateController::class, 'runUpdate'])->name('admin.github.update');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::match(['put', 'patch'], '/admin/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::post('/admin/users/{id}/block', [AdminController::class, 'blockUser'])->name('admin.users.block');
    Route::post('/admin/users/{id}/unblock', [AdminController::class, 'unblockUser'])->name('admin.users.unblock');
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.destroy');
    Route::get('/admin/messages/{id}/preview', [AdminController::class, 'previewMessage'])->name('admin.messages.preview');
    Route::get('/admin/messages/{id}/details', [AdminController::class, 'messageDetails'])->name('admin.messages.details');
    Route::match(['put', 'patch'], '/admin/messages/{id}', [AdminController::class, 'updateMessage'])->name('admin.messages.update');
    Route::delete('/admin/messages/{id}', [AdminController::class, 'deleteMessage'])->name('admin.messages.destroy');
    Route::get('/admin/api/messages-by-date', [AdminController::class, 'getMessagesByDate'])->name('admin.api.messages_by_date');
    Route::get('/admin/api/views-chart-data', [AdminController::class, 'viewsChartData'])->name('admin.api.views-chart-data');
    // Maintenance
    Route::post('/admin/maintenance/clean-expired', [AdminController::class, 'cleanExpiredMessages'])->name('admin.maintenance.cleanExpired');
    Route::post('/admin/maintenance/clear-cache', [AdminController::class, 'clearCache'])->name('admin.maintenance.clearCache');
    Route::post('/admin/maintenance/clean-logs', [AdminController::class, 'cleanOldLogs'])->name('admin.maintenance.cleanLogs');
    Route::post('/admin/maintenance/optimize', [AdminController::class, 'optimizeApp'])->name('admin.maintenance.optimize');
    Route::post('/admin/system/ai-logs/delete', [AdminController::class, 'deleteAiLogs'])->name('admin.aiLogs.delete');
    Route::post('/admin/notifications/{id}/reply', [AdminController::class, 'replyToInquiry'])->name('admin.notifications.reply');

    // Advertisements
    Route::get('/admin-page/ads', [AdminController::class, 'adsPage'])->name('admin.ads.page');
    Route::post('/admin/ads', [AdminController::class, 'storeAd'])->name('admin.ads.store');
    Route::match(['put', 'patch'], '/admin/ads/{id}', [AdminController::class, 'updateAd'])->name('admin.ads.update');
    Route::delete('/admin/ads/{id}', [AdminController::class, 'deleteAd'])->name('admin.ads.destroy');

    // Billing
    Route::get('/admin-page/billing', [\App\Http\Controllers\AdminBillingController::class, 'index'])->name('admin.billing.index');
    Route::get('/admin-page/billing/users', [\App\Http\Controllers\AdminBillingController::class, 'users'])->name('admin.billing.users');
    Route::post('/admin/billing/approve/{id}', [\App\Http\Controllers\AdminBillingController::class, 'approve'])->name('admin.billing.approve');
    Route::post('/admin/billing/reject/{id}', [\App\Http\Controllers\AdminBillingController::class, 'reject'])->name('admin.billing.reject');
    Route::get('/admin-page/billing/settings', [\App\Http\Controllers\AdminBillingController::class, 'settings'])->name('admin.billing.settings');
    Route::post('/admin/billing/settings', [\App\Http\Controllers\AdminBillingController::class, 'updateSettings'])->name('admin.billing.settings.update');

    Route::post('/admin/billing/grant/{userId}', [\App\Http\Controllers\AdminBillingController::class, 'grantAccess'])->name('admin.billing.grant');
    Route::post('/admin/billing/revoke/{userId}', [\App\Http\Controllers\AdminBillingController::class, 'revokeAccess'])->name('admin.billing.revoke');

    // Finances
    Route::get('/admin-page/finances/transactions', [\App\Http\Controllers\AdminFinanceController::class, 'transactions'])->name('admin.finances.transactions');
    Route::get('/admin-page/finances/overview', [\App\Http\Controllers\AdminFinanceController::class, 'overview'])->name('admin.finances.overview');
    
    Route::post('/admin/finances/expenses', [\App\Http\Controllers\AdminFinanceController::class, 'storeExpense'])->name('admin.finances.expenses.store');
});

// ===================== DATABASE MANAGER =====================
Route::middleware(['auth', 'admin', 'require.passcode'])->group(function () {
    Route::get('/database-manager', [DatabaseManagerController::class, 'databaseManagerPage'])->name('database.manager');
    Route::get('/api/database/tables', [DatabaseManagerController::class, 'getTablesData'])->name('api.database.tables');
    Route::get('/api/database/table/{table}', [DatabaseManagerController::class, 'getTableData'])->name('api.database.table');
    Route::post('/api/database/table/{table}/row', [DatabaseManagerController::class, 'updateRowData'])->name('api.database.row.update');
});

// ===================== USER =====================
Route::get('/', function () use ($appLocked) {
    if (Auth::check()) {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.page');
        }
        if (Auth::user()->status === 'blocked') {
            return redirect()->route('account.blocked');
        }
        if ($appLocked()) {
            return redirect()->route('app.locked');
        }
        return redirect()->route('user.page');
    }
    return view('welcome.welcome');
})->name('home');

Route::get('/app-locked', function () {
    if (!Auth::check()) {
        return redirect()->route('auth.login');
    }
    if (Auth::user()->role === 'admin') {
        return redirect()->route('admin.page');
    }
    $user = Auth::user();
    $userProfileUrl = url()->route('profile.picture');
    return view('sub-folder.app-locked', ['user_profile_url' => $userProfileUrl]);
})->name('app.locked')->middleware('auth');

Route::get('/profile-picture', [ProfilePictureController::class, 'show'])->name('profile.picture')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/auth/verify-passcode', [PasscodeController::class, 'showVerify'])->name('auth.passcode.verify');
    Route::post('/auth/verify-passcode', [PasscodeController::class, 'verify'])->name('auth.passcode.verify.post');
});

Route::middleware(['auth', 'user.role', 'require.passcode', 'billing'])->group(function () {
    Route::get('/user-page', [UserController::class, 'index'])->name('user.page');
    Route::get('/user-page/google-calendar-events', [UserController::class, 'googleCalendarEvents'])->name('user.google-calendar-events');
    Route::get('/user-page/views-chart-data', [UserController::class, 'viewsChartData'])->name('user.views-chart-data');
    Route::get('/user-page/create', [UserController::class, 'createPage'])->name('user.create.page');
    Route::get('/user-page/edit-messages/{id}', [UserController::class, 'editMessagesPage'])->name('user.edit-messages.page');
    Route::get('/user-page/media', [UserController::class, 'mediaPage'])->name('user.media.page');
    Route::get('/user-page/music', [UserController::class, 'musicPage'])->name('user.music.page');
    Route::post('/user-page/music/save', [UserController::class, 'saveMusicSelection'])->name('user.music.save');
    Route::get('/user-page/music/spotify/search', [\App\Http\Controllers\SpotifyController::class, 'search'])->name('user.music.spotify.search');
    Route::get('/user-page/music/spotify/album/{id}', [\App\Http\Controllers\SpotifyController::class, 'albumTracks'])->name('user.music.spotify.album');
    Route::get('/user-page/template', [UserController::class, 'templatePage'])->name('user.template.page');
    Route::get('/user-page/links', [UserController::class, 'linksPage'])->name('user.links.page');
    Route::get('/user-page/share-messages', [UserController::class, 'shareMessagesPage'])->name('user.share-messages.page');
    Route::get('/user-page/my-messages', [UserController::class, 'myMessagesPage'])->name('user.my-messages.page');
    Route::get('/user-page/notifications-page', [UserController::class, 'notificationsPage'])->name('user.notifications.page');
    Route::get('/user-page/settings/messages', [UserController::class, 'messageSettingsPage'])->name('user.settings.messages.page');
    Route::get('/user-page/search', [UserController::class, 'searchPage'])->name('user.search.page');
    Route::get('/user-page/control-center/security', [UserController::class, 'controlCenterSecurity'])->name('user.control-center.security.page');
    Route::get('/user-page/settings/user', [UserController::class, 'userSettingsPage'])->name('user.settings.user.page');
    Route::get('/user-page/help-support', function () {return view('user.pages.settings.help-support');})->name('user.help-support.page');
    Route::post('/user-page/settings/delete-account', [UserController::class, 'deleteAccount'])->name('user.settings.delete-account');
    Route::post('/user-page/settings/passcode', [UserSettingsController::class, 'updatePasscode'])->name('user.settings.passcode');
    Route::post('/user/github/update', [GithubUpdateController::class, 'userUpdate'])->name('user.github.update');
    Route::post('/user-page/settings/general', [UserSettingsController::class, 'updateGeneral'])->name('user.settings.general');
    Route::get('/user-page/message-preview/{id}', [UserController::class, 'messagePreviewPage'])->name('user.message-preview.page');
    Route::get('/user-page/settings/download-data', [UserSettingsController::class, 'downloadData'])->name('user.settings.download-data');

    // Lifecycle & Vault
    Route::post('/settings/lifecycle', [MessageLifecycleController::class, 'updateSettings'])->name('settings.lifecycle.update');
    Route::get('/settings/lifecycle/archive', [MessageLifecycleController::class, 'runAutoArchive'])->name('settings.lifecycle.run_archive');
    Route::get('/user-page/vault', [MessageLifecycleController::class, 'vaultPage'])->name('user.vault.page');
    Route::post('/user-page/vault/auth', [MessageLifecycleController::class, 'authVault'])->name('user.vault.auth');
    Route::get('/user-page/vault/lock', [MessageLifecycleController::class, 'lockVault'])->name('user.vault.lock');
    Route::post('/messages/{id}/vault', [MessageLifecycleController::class, 'toggleVault'])->name('user.lifecycle.toggle_vault');
    Route::post('/messages/{id}/vault-pin', [MessageLifecycleController::class, 'updateSpecificPin'])->name('user.lifecycle.update_pin');
    Route::post('/messages/{id}/archive', [MessageLifecycleController::class, 'toggleArchive'])->name('user.lifecycle.toggle_archive');

    // Trash
    Route::get('/user-page/trash', [MessageLifecycleController::class, 'trashPage'])->name('user.trash.page');
    Route::post('/messages/{id}/restore', [MessageLifecycleController::class, 'restore'])->name('user.lifecycle.restore');
    Route::delete('/messages/{id}/force', [MessageLifecycleController::class, 'forceDelete'])->name('user.lifecycle.force_delete');
    Route::get('/messages/create', fn () => redirect()->route('user.page'))->name('messages.create');
    Route::get('/messages/list', [WishMessagesController::class, 'index'])->name('messages.list');
    Route::get('/messages/{id}', [WishMessagesController::class, 'show'])->name('messages.show');
    Route::post('/messages', [WishMessagesController::class, 'store'])->name('messages.store');
    Route::match(['put', 'patch'], '/messages/{id}', [WishMessagesController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{id}', [WishMessagesController::class, 'destroy'])->name('messages.destroy');
    Route::get('/media', [MediaFilesController::class, 'show'])->name('media.show');
    Route::post('/media', [MediaFilesController::class, 'store'])->name('media.store');
    Route::get('/notifications', [UserNotificationsController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [UserNotificationsController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [UserNotificationsController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::post('/notifications/compose', [UserNotificationsController::class, 'composeToAdmin'])->name('notifications.compose');
    Route::post('/notifications/{id}/reply', [UserNotificationsController::class, 'replyToAdmin'])->name('notifications.reply');
    Route::delete('/notifications/{id}/delete', [UserNotificationsController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/settings/account', [UserSettingsController::class, 'updateAccount'])->name('settings.account');
    Route::post('/settings/page', [UserSettingsController::class, 'updatePageSettings'])->name('settings.page');
    Route::post('/settings/notifications', [UserSettingsController::class, 'updateNotificationSettings'])->name('settings.notifications');
    Route::post('/settings/reset', [UserSettingsController::class, 'resetSettings'])->name('settings.reset');
    Route::post('/settings/hub/privacy-theme', [UserSettingsController::class, 'updatePrivacyAndTheme'])->name('settings.hub.privacy_theme');
    Route::post('/links/generate', [GeneratedLinksController::class, 'store'])->name('links.generate');
    Route::post('/share-messages/load', [ShareMessagesController::class, 'loadMessage'])->name('share-messages.load');
    Route::get('/share-messages/recent-sends', [ShareMessagesController::class, 'recentSends'])->name('share-messages.recentSends');
    Route::post('/share-messages/send-email', [ShareMessagesController::class, 'sendEmail'])->name('share-messages.sendEmail');
    Route::post('/share-messages/send-sms', [ShareMessagesController::class, 'sendSms'])->name('share-messages.sendSms');
    Route::get('/user-page/share-messages/schedule', [ShareMessagesController::class, 'schedulePage'])->name('share-messages.schedule.page');
    Route::post('/user-page/share-messages/schedule', [ShareMessagesController::class, 'schedule'])->name('share-messages.schedule');

    // Billing (Disabled per user request)
    // Route::get('/billing', [App\Http\Controllers\UserBillingController::class, 'index'])->name('user.billing.index');
    // Route::get('/billing/method', [App\Http\Controllers\UserBillingController::class, 'methodSelection'])->name('user.billing.method');
    // Route::get('/billing/checkout', [App\Http\Controllers\UserBillingController::class, 'checkout'])->name('user.billing.checkout');
    // Route::get('/billing/verify', [App\Http\Controllers\UserBillingController::class, 'verifyPaystack'])->name('user.billing.verify');
    // Route::post('/billing/charge', [App\Http\Controllers\UserBillingController::class, 'charge'])->name('user.billing.charge');
    // Route::post('/billing/otp', [App\Http\Controllers\UserBillingController::class, 'submitOtp'])->name('user.billing.otp');

    Route::get('/ai-assistant', [AiAssistantController::class, 'page'])->name('user.ai.page');
});

Route::post('/ai/generate', [AiAssistantController::class, 'generate'])->name('ai.generate');
Route::get('/ai/tts', [AiAssistantController::class, 'tts'])->name('ai.tts');

Route::prefix('templates')->middleware('auth')->group(function () {
    Route::get('/preview', [TemplateController::class, 'preview'])->name('templates.preview');
    Route::post('/select', [TemplateController::class, 'select'])->name('templates.select');
    Route::get('/find-by-date', [TemplateController::class, 'findByDate'])->name('templates.findByDate');
    Route::get('/latest', [TemplateController::class, 'latest'])->name('templates.latest');
});

// ===================== PUBLIC TEMPLATE GALLERY (no auth) =====================
Route::get('/templates', [TemplateGalleryController::class, 'index'])->name('templates.gallery');
Route::get('/templates/preview/{template}', [TemplateGalleryController::class, 'preview'])->name('templates.gallery.preview')->where('template', '[a-zA-Z0-9\-]+');

// ===================== TRY WISP (Guest creation) =====================
Route::get('/try', [GuestMessageController::class, 'index'])->name('guest.try');
Route::post('/try/create', [GuestMessageController::class, 'store'])->name('guest.try.create');
Route::delete('/try/message/{id}', [GuestMessageController::class, 'destroy'])->name('guest.try.delete');
Route::get('/try/spotify/search', [\App\Http\Controllers\SpotifyController::class, 'search'])->name('try.spotify.search');

// ===================== PUBLIC =====================
Route::get('/help', function () {
    return view('doc.index');
})->name('help');

Route::get('/spotify/connect', [SpotifyPlaybackController::class, 'connect'])->name('spotify.connect');
Route::get('/spotify/callback', [SpotifyPlaybackController::class, 'callback'])->name('spotify.callback');
Route::get('/spotify/token', [SpotifyPlaybackController::class, 'token'])->name('spotify.token');

// Alternative "long form" links (generated by mobile app)
Route::post('/view/message/{type}/{slug}/consent', [MessageViewController::class, 'acceptConsent'])
    ->where('type', '[a-zA-Z\s\-]+')->name('message.consent.accept.alt');
Route::get('/view/message/{type}/{slug}', [MessageViewController::class, 'show'])
    ->where('type', '[a-zA-Z\s\-]+')->name('message.show.alt');

// Standard "short form" links (generated by web app)
Route::post('/message/consent/{type}/{slug}', [MessageViewController::class, 'acceptConsent'])
    ->where('type', '[a-zA-Z\s\-]+')->name('message.consent.accept');
Route::get('/{type}/{slug}', [MessageViewController::class, 'show'])
    ->where('type', '[a-zA-Z\s\-]+')->name('message.show');

// Dynamic XML Sitemap for Search Engines
Route::get('/sitemap.xml', function () {
    $url = config('app.url');
    $content = '<?xml version="1.0" encoding="UTF-8"?>';
    $content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    // Homepage
    $content .= '<url>';
    $content .= '<loc>' . $url . '</loc>';
    $content .= '<lastmod>' . now()->toDateString() . '</lastmod>';
    $content .= '<changefreq>daily</changefreq>';
    $content .= '<priority>1.0</priority>';
    $content .= '</url>';
    
    // Login
    $content .= '<url>';
    $content .= '<loc>' . $url . '/login</loc>';
    $content .= '<lastmod>' . now()->toDateString() . '</lastmod>';
    $content .= '<changefreq>monthly</changefreq>';
    $content .= '<priority>0.8</priority>';
    $content .= '</url>';
    
    // Templates
    $content .= '<url>';
    $content .= '<loc>' . $url . '/templates</loc>';
    $content .= '<lastmod>' . now()->toDateString() . '</lastmod>';
    $content .= '<changefreq>weekly</changefreq>';
    $content .= '<priority>0.9</priority>';
    $content .= '</url>';
    
    // Try WISP
    $content .= '<url>';
    $content .= '<loc>' . $url . '/try</loc>';
    $content .= '<lastmod>' . now()->toDateString() . '</lastmod>';
    $content .= '<changefreq>weekly</changefreq>';
    $content .= '<priority>0.8</priority>';
    $content .= '</url>';
    
    $content .= '</urlset>';
    
    return response($content, 200, [
        'Content-Type' => 'application/xml'
    ]);
})->name('sitemap');
