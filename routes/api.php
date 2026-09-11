<?php

use Illuminate\Support\Facades\Route;

// ===================== AUTH API =====================
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/password/reset/send-code', [App\Http\Controllers\AuthController::class, 'sendResetCode']);
Route::post('/password/reset/verify-code', [App\Http\Controllers\AuthController::class, 'verifyResetCode']);
Route::post('/password/reset/update', [App\Http\Controllers\AuthController::class, 'updatePassword']);

// ===================== USER API =====================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [App\Http\Controllers\UserController::class, 'showCurrentUser']);
    Route::get('/user/settings', [App\Http\Controllers\UserSettingsController::class, 'showSettings']);
    Route::put('/user/settings', [App\Http\Controllers\UserSettingsController::class, 'updateSettings']);
    Route::post('/user/settings/password', [App\Http\Controllers\UserController::class, 'updatePassword']);
    Route::delete('/user/settings/password', [App\Http\Controllers\UserController::class, 'deletePassword']);
    Route::post('/user/profile-picture', [App\Http\Controllers\UserController::class, 'updateProfilePicture']);
    Route::delete('/user/profile-picture', [App\Http\Controllers\UserController::class, 'deleteProfilePicture']);
    Route::delete('/user/account', [App\Http\Controllers\UserController::class, 'deleteAccountApi']);
    Route::put('/user/profile', [App\Http\Controllers\UserController::class, 'updateProfile']);
    Route::get('/user/messages', [App\Http\Controllers\UserController::class, 'listMessages']);
    Route::get('/user/messages/{id}', [App\Http\Controllers\UserController::class, 'showMessage']);
    Route::get('/user/trash', [App\Http\Controllers\UserController::class, 'listTrash']);
    Route::get('/user/analytics', [App\Http\Controllers\UserController::class, 'analytics']);
    Route::get('/user/analytics/chart', [App\Http\Controllers\UserController::class, 'viewsChartData']);
    Route::get('/user/notifications', [App\Http\Controllers\UserController::class, 'listNotifications']);
    Route::post('/user/notifications/mark-all-read', [App\Http\Controllers\UserController::class, 'markAllRead']);
    Route::delete('/user/notifications/{id}', [App\Http\Controllers\UserController::class, 'deleteNotification']);
    Route::get('/user/ai-credits', [App\Http\Controllers\UserController::class, 'aiCredits']);
    Route::post('/user/ai-credits/redeem-monthly', [App\Http\Controllers\UserController::class, 'redeemMonthlyCredits']);
    Route::get('/user/subscriptions', [App\Http\Controllers\UserController::class, 'listSubscriptions']);
    Route::get('/user/subscriptions/{id}', [App\Http\Controllers\UserController::class, 'showSubscription']);
    Route::get('/user/links', [App\Http\Controllers\UserController::class, 'listLinks']);
});

// ===================== MESSAGING API =====================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/messages', [App\Http\Controllers\WishMessagesController::class, 'store']);
    Route::put('/messages/{id}', [App\Http\Controllers\WishMessagesController::class, 'update']);
    Route::post('/messages/{id}/share', [App\Http\Controllers\WishMessagesController::class, 'shareMessage']);
    Route::delete('/messages/{id}', [App\Http\Controllers\WishMessagesController::class, 'destroy']);
    Route::delete('/messages/{id}/force', [App\Http\Controllers\WishMessagesController::class, 'forceDestroy']);
    Route::put('/messages/{id}/favorite', [App\Http\Controllers\WishMessagesController::class, 'toggleFavorite']);
    Route::put('/messages/{id}/passcode', [App\Http\Controllers\WishMessagesController::class, 'setPasscode']);
    Route::post('/messages/{id}/resend', [App\Http\Controllers\WishMessagesController::class, 'resendMessage']);
    Route::put('/messages/{id}/archive', [App\Http\Controllers\WishMessagesController::class, 'toggleArchive']);
    Route::post('/messages/{id}/ai-enhance', [App\Http\Controllers\WishMessagesController::class, 'aiEnhance']);
    Route::post('/messages/{id}/restore', [App\Http\Controllers\WishMessagesController::class, 'restoreMessage']);
    Route::put('/messages/{id}/view-type', [App\Http\Controllers\WishMessagesController::class, 'updateViewType']);
});

// ===================== AI ASSISTANT API =====================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/ai/message-content', [App\Http\Controllers\AiAssistantController::class, 'generateMessageContent']);
    Route::post('/ai/generate-image', [App\Http\Controllers\AiAssistantController::class, 'generateImage']);
    Route::post('/ai/generate-music', [App\Http\Controllers\AiAssistantController::class, 'generateMusic']);
    Route::post('/ai/reply', [App\Http\Controllers\AiAssistantController::class, 'generateReply']);
    Route::post('/ai/image-to-text', [App\Http\Controllers\AiAssistantController::class, 'imageToText']);
});

// ====================== Generated Links & Sharing =====================
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/generated-links/{id}/status', [App\Http\Controllers\GeneratedLinksController::class, 'toggleLinkStatus']);
    Route::delete('/generated-links/{id}', [App\Http\Controllers\GeneratedLinksController::class, 'deleteLink']);
});


// ====================== Sharing & Scheduling Messages =====================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/share-messages/send-email', [App\Http\Controllers\ShareMessagesController::class, 'sendEmail']);
    Route::post('/share-messages/send-sms', [App\Http\Controllers\ShareMessagesController::class, 'sendSms']);
    Route::post('/share-messages/send-whatsapp', [App\Http\Controllers\ShareMessagesController::class, 'sendWhatsapp']);
    Route::post('/share-messages/send-all', [App\Http\Controllers\ShareMessagesController::class, 'sendAll']);
    Route::post('/share-messages/schedule', [App\Http\Controllers\ShareMessagesController::class, 'schedule']);
});


// ===================== MEDIA API =====================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/media/save', [App\Http\Controllers\MediaFilesController::class, 'saveApi']);
});

// ===================== TEMPLATES API =====================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/templates/list', [App\Http\Controllers\TemplateController::class, 'listTemplates']);
    Route::get('/templates/preview', [App\Http\Controllers\TemplateController::class, 'preview']);
    Route::get('/templates/find-by-date', [App\Http\Controllers\TemplateController::class, 'findByDate']);
    Route::get('/templates/latest', [App\Http\Controllers\TemplateController::class, 'latest']);
    Route::post('/templates/select', [App\Http\Controllers\TemplateController::class, 'select']);
});

// ===================== BILLING API =====================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/billing/info', [\App\Http\Controllers\Api\BillingController::class, 'info']);
    Route::post('/billing/verify', [\App\Http\Controllers\Api\BillingController::class, 'verify']);
    Route::post('/billing/charge', [\App\Http\Controllers\Api\BillingController::class, 'charge']);
    Route::post('/billing/otp', [\App\Http\Controllers\Api\BillingController::class, 'submitOtp']);
});
