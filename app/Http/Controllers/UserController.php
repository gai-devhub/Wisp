<?php

namespace App\Http\Controllers;

use App\Models\GeneratedLinks;
use App\Models\MessageViews;
use App\Models\ShareSend;
use App\Models\Template;
use App\Models\UserNotification;
use App\Models\UserSettings;
use App\Models\WishMessages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        return $this->renderSection($request, 'dashboard');
    }

    public function showCurrentUser(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'user' => [
                'id' => (string)$user->id,
                'firstName' => explode(' ', $user->name)[0] ?? $user->name,
                'lastName' => implode(' ', array_slice(explode(' ', $user->name), 1)) ?? '',
                'username' => $user->username,
                'email' => $user->email,
                'profileImage' => $user->profile_picture_url,
            ]
        ]);
    }

    public function viewsChartData(Request $request)
    {
        $userId = Auth::id();
        $period = $request->query('period', 'week');

        $data = [];
        switch ($period) {
            case 'day':
                for ($h = 23; $h >= 0; $h--) {
                    $hour = Carbon::now()->subHours($h);
                    $data[] = [
                        'label' => $hour->format('H:i'),
                        'count' => \App\Models\MessageViews::where('user_id', $userId)
                            ->whereBetween('viewed_at', [$hour->copy()->startOfHour(), $hour->copy()->endOfHour()])
                            ->count(),
                    ];
                }
                break;
            case 'month':
                for ($d = 29; $d >= 0; $d--) {
                    $date = Carbon::today()->subDays($d)->toDateString();
                    $data[] = [
                        'label' => Carbon::parse($date)->format('j/n'),
                        'count' => \App\Models\MessageViews::where('user_id', $userId)->whereDate('viewed_at', $date)->count(),
                    ];
                }
                break;
            default:
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::today()->subDays($i)->toDateString();
                    $data[] = [
                        'label' => Carbon::parse($date)->format('D'),
                        'count' => \App\Models\MessageViews::where('user_id', $userId)->whereDate('viewed_at', $date)->count(),
                    ];
                }
        }

        return response()->json($data);
    }

    public function googleCalendarEvents(Request $request)
    {
        $user = Auth::user();
        if (!$user->google_token) {
            // Return 200 (not 401) — the global fetch interceptor redirects to login on 401,
            // which causes an infinite reload loop since the user is already authenticated.
            // Not having a Google token is a missing integration, not an auth failure.
            return response()->json(['error' => 'No Google token found'], 200);
        }

        $date = $request->query('date');
        if (!$date) {
            return response()->json(['error' => 'Date parameter is required'], 400);
        }

        $timeMin = Carbon::parse($date)->startOfDay()->toRfc3339String();
        $timeMax = Carbon::parse($date)->endOfDay()->toRfc3339String();

        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('GET', 'https://www.googleapis.com/calendar/v3/calendars/primary/events', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $user->google_token,
                    'Accept'        => 'application/json',
                ],
                'query' => [
                    'timeMin'      => $timeMin,
                    'timeMax'      => $timeMax,
                    'singleEvents' => 'true',
                    'orderBy'      => 'startTime',
                ],
            ]);

            $events = json_decode($response->getBody(), true);
            return response()->json($events['items'] ?? []);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 401) {
                // Token might be expired, need to refresh
                if ($user->google_refresh_token) {
                    try {
                        $refreshResponse = $client->request('POST', 'https://oauth2.googleapis.com/token', [
                            'form_params' => [
                                'client_id'     => config('services.google.client_id'),
                                'client_secret' => config('services.google.client_secret'),
                                'refresh_token' => $user->google_refresh_token,
                                'grant_type'    => 'refresh_token',
                            ]
                        ]);
                        $refreshData = json_decode($refreshResponse->getBody(), true);
                        if (isset($refreshData['access_token'])) {
                            $user->update(['google_token' => $refreshData['access_token']]);
                            
                            // Retry the original request
                            $response = $client->request('GET', 'https://www.googleapis.com/calendar/v3/calendars/primary/events', [
                                'headers' => [
                                    'Authorization' => 'Bearer ' . $refreshData['access_token'],
                                    'Accept'        => 'application/json',
                                ],
                                'query' => [
                                    'timeMin'      => $timeMin,
                                    'timeMax'      => $timeMax,
                                    'singleEvents' => 'true',
                                    'orderBy'      => 'startTime',
                                ],
                            ]);

                            $events = json_decode($response->getBody(), true);
                            return response()->json($events['items'] ?? []);
                        }
                    } catch (\Exception $refreshEx) {
                        return response()->json(['error' => 'Failed to refresh token: ' . $refreshEx->getMessage()], 200);
                    }
                }
            }
            return response()->json(['error' => 'Failed to fetch calendar events: ' . $e->getMessage()], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function createPage(Request $request): View|RedirectResponse
    {
        return $this->renderSection($request, 'create');
    }

    public function editMessagesPage(Request $request, $id)
    {
        $message = WishMessages::where('user_id', Auth::id())->findOrFail($id);
        $request->merge(['_edit_message' => $message]);
        return $this->renderSection($request, 'edit-messages');
    }

    public function messagePreviewPage(Request $request, $id)
    {
        $user = Auth::user();
        
        $message = WishMessages::where('user_id', $user->id)
            ->with(['mediaFiles', 'template', 'shareSends', 'generatedLinks'])
            ->findOrFail($id);
            
        // We also need some basic variables that the main layout might expect if we use a specific layout.
        // Let's use the 'user.pages.messages.message-preview' view and pass the message.
        $notifications = \App\Models\UserNotification::forUser($user->id)->orderBy('created_at', 'desc')->limit(50)->get();
        
        return view('user.pages.messages.message-preview', compact('user', 'message', 'notifications'));
    }

    public function mediaPage(Request $request): View|RedirectResponse
    {
        return $this->renderSection($request, 'media');
    }

    public function musicPage(Request $request): View|RedirectResponse
    {
        return $this->renderSection($request, 'music');
    }

    public function saveMusicSelection(Request $request)
    {
        $request->validate([
            'wish_message_id' => 'required|exists:wish_messages,id',
            'track_url' => 'required|url'
        ]);

        $message = WishMessages::where('user_id', Auth::id())->findOrFail($request->wish_message_id);
        
        \App\Models\MediaFiles::updateOrCreate(
            ['user_id' => Auth::id(), 'wish_message_id' => $message->id],
            ['background_music' => $request->track_url]
        );

        return redirect()->back()->with('success', 'Background music saved successfully!');
    }

    public function templatePage(Request $request): View|RedirectResponse
    {
        return $this->renderSection($request, 'template');
    }

    public function linksPage(Request $request): View|RedirectResponse
    {
        return $this->renderSection($request, 'links');
    }

    public function shareMessagesPage(Request $request): View|RedirectResponse
    {
        $messageId = $request->input('message_id');
        if ($messageId) {
            $message = \App\Models\WishMessages::where('user_id', \Illuminate\Support\Facades\Auth::id())->find($messageId);
            if (!$message) {
                return redirect()->route('user.my-messages.page')->with('error', 'Message not found.');
            }
            if (!$message->generated_link) {
                return redirect()->route('user.my-messages.page')->with('error', 'Please generate a link first.');
            }
            return view('user.pages.messages.share-messages', ['user' => \Illuminate\Support\Facades\Auth::user(), 'message' => $message]);
        }
        return $this->renderSection($request, 'general.shared-messages');
    }

    public function myMessagesPage(Request $request): View|RedirectResponse
    {
        return $this->renderSection($request, 'my-messages');
    }

    public function notificationsPage(Request $request): View|RedirectResponse
    {
        return $this->renderSection($request, 'notifications');
    }

    public function messageSettingsPage(Request $request): View|RedirectResponse
    {
        return $this->renderSection($request, 'message-settings');
    }

    public function searchPage(Request $request): View|RedirectResponse
    {
        return $this->renderSection($request, 'search');
    }

    public function controlCenterSecurity(Request $request): View|RedirectResponse
    {
        return $this->renderSection($request, 'control-center-security');
    }

    public function userSettingsPage(Request $request): View|RedirectResponse
    {
        return $this->renderSection($request, 'user-settings');
    }

    private function renderSection(Request $request, string $section): View|RedirectResponse|JsonResponse
    {
        if ($this->appLocked()) {
            return redirect()->route('app.locked');
        }
        $user = Auth::user();
        $userId = $user->id;
        $userSettings = UserSettings::firstOrCreate(
            ['user_id' => $userId],
            ['page_expiry' => 24, 'page_view_alerts' => true, 'whatsapp_notifications' => true, 'auto_delete_expired' => true]
        );

        $messagesPage = max(1, (int) $request->input('messages_page', 1));
        $linksPage = max(1, (int) $request->input('links_page', 1));
        $activityPage = max(1, (int) $request->input('activity_page', 1));

        $messageIds = WishMessages::where('user_id', $userId)->pluck('id');
        $messages = WishMessages::where('user_id', $userId)
            ->withCount('views')
            ->withMax('views', 'viewed_at')
            ->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'messages_page', $messagesPage);
        $allMessagesForSelect = WishMessages::where('user_id', $userId)->orderBy('created_at', 'desc')->get(['id', 'title', 'recipient_name', 'recipient_special_name', 'is_published']);
        $allMessagesForEdit = WishMessages::where('user_id', $userId)->get(['id', 'message_type', 'title', 'recipient_name', 'recipient_special_name', 'greeting', 'message', 'last_note', 'receiving_date', 'sender_name', 'is_published']);
        $lastTwoMessages = WishMessages::where('user_id', $userId)->orderBy('created_at', 'desc')->take(2)->get();
        $notifications = UserNotification::where(function($q) use ($userId) { $q->where('user_id', $userId)->orWhere('sender_id', $userId); })->where('deleted_by_user', false)->rootThreads()->with(['sender', 'replies.sender'])->orderBy('created_at', 'desc')->limit(50)->get();

        $templateList = TemplateController::getAllTemplateKeys();
        $messageTemplates = Template::where('user_id', $userId)
            ->whereIn('wish_message_id', $messageIds)
            ->get()
            ->keyBy('wish_message_id');
        $generatedLinks = GeneratedLinks::whereIn('wish_message_id', $messageIds)
            ->orderBy('updated_at', 'desc')
            ->paginate(20, ['*'], 'links_page', $linksPage);

        $dashboardStats = [
            'generated_links' => GeneratedLinks::whereIn('wish_message_id', $messageIds)->count(),
            'total_views' => MessageViews::where('user_id', $userId)->count(),
            'active_messages' => WishMessages::where('user_id', $userId)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })->count(),
            'expired_messages' => WishMessages::where('user_id', $userId)
                ->whereNotNull('expires_at')->where('expires_at', '<', now())->count(),
        ];

        $activityItems = collect();
        $allMessagesForActivity = WishMessages::where('user_id', $userId)->get(['id', 'title', 'created_at', 'recipient_name', 'recipient_special_name']);
        foreach ($allMessagesForActivity as $m) {
            $activityItems->push((object)['time' => $m->created_at, 'activity' => 'Message created', 'details' => 'Created message "' . ($m->title ?? 'Untitled') . '" for ' . ($m->recipient_name ?? $m->recipient_special_name ?? '—')]);
        }
        $linksWithMessage = GeneratedLinks::whereIn('wish_message_id', $messageIds)->with('wishMessage:id,title')->get();
        foreach ($linksWithMessage as $link) {
            $title = $link->wishMessage ? $link->wishMessage->title : 'Message';
            $activityItems->push((object)['time' => $link->created_at, 'activity' => 'Link generated', 'details' => 'Generated link for "' . $title . '"']);
        }
        $viewsWithMessage = MessageViews::where('user_id', $userId)->with('wishMessage:id,title')->orderBy('viewed_at', 'desc')->get();
        foreach ($viewsWithMessage as $v) {
            $title = $v->wishMessage ? $v->wishMessage->title : 'Message';
            $activityItems->push((object)['time' => $v->viewed_at, 'activity' => 'Page viewed', 'details' => 'Someone viewed "' . $title . '"']);
        }
        $activityItems = $activityItems->sortByDesc(fn ($item) => $item->time instanceof \DateTimeInterface ? $item->time->getTimestamp() : strtotime((string) $item->time))->values();
        $activityTotal = $activityItems->count();
        $recentActivity = $activityItems->forPage($activityPage, 20)->values();
        $activityHasPrev = $activityPage > 1;
        $activityHasNext = ($activityPage * 20) < $activityTotal;

        $viewsLast7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $viewsLast7Days[] = [
                'date' => $date,
                'label' => Carbon::parse($date)->format('j/n'),
                'count' => MessageViews::where('user_id', $userId)->whereDate('viewed_at', $date)->count(),
            ];
        }

        $recentShareSends = ShareSend::where('user_id', $userId)->with('wishMessage')->latest()->take(5)->get();
        $sharesPage = max(1, (int) $request->input('shares_page', 1));
        $allShareSends = ShareSend::where('user_id', $userId)->with('wishMessage')->latest()->paginate(20, ['*'], 'shares_page', $sharesPage);
        $shareMessagesLink = null;
        $shareMessagesRecipientName = null;
        $shareMessagesRecipientPhone = null;
        $shareMessagesMessageText = null;
        $shareMessagesId = null;
        $messagesWithLink = WishMessages::where('user_id', $userId)->whereHas('generatedLinks')->get();
        if (session('share_messages_id')) {
            $waMsg = $messagesWithLink->firstWhere('id', session('share_messages_id'));
            if ($waMsg && $waMsg->generated_link) {
                $shareMessagesId = $waMsg->id;
                $shareMessagesLink = $waMsg->generated_link;
                $shareMessagesRecipientName = $waMsg->recipient_name;
                $shareMessagesRecipientPhone = $waMsg->recipient_phone;
                $vaultPin = null;
                if ($waMsg->is_vaulted && $waMsg->specific_vault_pin) {
                    try {
                        $vaultPin = \Illuminate\Support\Facades\Crypt::decryptString($waMsg->getRawOriginal('specific_vault_pin'));
                    } catch (\Exception $e) {}
                }
                $shareMessagesMessageText = \App\Http\Controllers\ShareMessagesController::buildShareMessage($waMsg->recipient_name, $waMsg->generated_link, $vaultPin);
            }
        }

        $message = $request->get('_edit_message');

        $mediaFiles = \App\Models\MediaFiles::where('user_id', $userId)->get();
        $imagesSize = 0;
        $audioSize = 0;
        foreach ($mediaFiles as $file) {
            try {
                if ($file->recipient_image && \Illuminate\Support\Facades\Storage::disk(config('filesystems.media_disk'))->exists($file->recipient_image)) {
                    $imagesSize += \Illuminate\Support\Facades\Storage::disk(config('filesystems.media_disk'))->size($file->recipient_image);
                }
                if ($file->background_music && \Illuminate\Support\Facades\Storage::disk(config('filesystems.media_disk'))->exists($file->background_music)) {
                    $audioSize += \Illuminate\Support\Facades\Storage::disk(config('filesystems.media_disk'))->size($file->background_music);
                }
            } catch (\Exception $e) {
                // Ignore existence/size check errors (e.g., missing IAM permissions or deleted files on S3)
            }
        }

        $scheduledSends = [];
        $activeSessions = [];
        $searchQuery = $request->query('q');
        $searchResults = [];
        
        if (in_array($section, ['dashboard', 'page-settings', 'control-center-security', 'search'])) {
            // Provide all wish messages with receiving dates for the calendar
            $scheduledSends = WishMessages::where('user_id', $userId)->whereNotNull('receiving_date')->get()->map(function($msg) {
                return [
                    'wish_message_id' => $msg->id,
                    'wish_message' => [
                        'title' => $msg->title,
                        'recipient_name' => $msg->recipient_name
                    ],
                    'scheduled_at' => $msg->receiving_date,
                    'recipient_masked' => $msg->recipient_name
                ];
            });
            
            if ($searchQuery) {
                $searchResults['messages'] = WishMessages::where('user_id', $userId)
                    ->where(function($q) use ($searchQuery) {
                        $q->where('title', 'like', "%{$searchQuery}%")
                          ->orWhere('recipient_name', 'like', "%{$searchQuery}%")
                          ->orWhere('message', 'like', "%{$searchQuery}%");
                    })->get();
            }
            // Active sessions
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('sessions')) {
                    $activeSessions = \Illuminate\Support\Facades\DB::table('sessions')
                        ->where('user_id', $userId)
                        ->orderBy('last_activity', 'desc')
                        ->get();
                }
            } catch (\Exception $e) {}
        }

        $systemPages = [
            [
                'title' => 'Dashboard',
                'url' => route('user.page'),
                'icon' => 'fas fa-home',
                'description' => 'View message statistics, analytics, and calendar.',
                'keywords' => ['dashboard', 'home', 'overview', 'statistics', 'views', 'analytics']
            ],
            [
                'title' => 'My Messages',
                'url' => route('user.my-messages.page'),
                'icon' => 'fas fa-envelope',
                'description' => 'Manage and share your created messages.',
                'keywords' => ['messages', 'my messages', 'inbox', 'list', 'saved', 'drafts', 'letters']
            ],
            [
                'title' => 'Create New Message',
                'url' => route('user.create.page'),
                'icon' => 'fas fa-plus-circle',
                'description' => 'Write and design a new scheduled or instant message.',
                'keywords' => ['create', 'new', 'write', 'send', 'add', 'compose']
            ],
            [
                'title' => 'Media Library',
                'url' => route('user.media.page'),
                'icon' => 'fas fa-images',
                'description' => 'Upload and manage images for your messages.',
                'keywords' => ['media', 'library', 'images', 'photos', 'uploads', 'gallery']
            ],
            [
                'title' => 'Music & Audio',
                'url' => route('user.music.page'),
                'icon' => 'fas fa-music',
                'description' => 'Configure background audio and Spotify integration.',
                'keywords' => ['music', 'audio', 'songs', 'spotify', 'background music', 'sound']
            ],
            [
                'title' => 'Message Templates',
                'url' => route('user.template.page'),
                'icon' => 'fas fa-layer-group',
                'description' => 'Browse and select custom styled message templates.',
                'keywords' => ['template', 'templates', 'layout', 'design', 'styles']
            ],
            [
                'title' => 'Generated Links',
                'url' => route('user.links.page'),
                'icon' => 'fas fa-link',
                'description' => 'View active, expired, and custom domain sharing links.',
                'keywords' => ['links', 'generated', 'urls', 'domains', 'tracking']
            ],
            [
                'title' => 'Trash & Recovery',
                'url' => route('user.trash.page'),
                'icon' => 'fas fa-trash-alt',
                'description' => 'Restore recently deleted messages or delete permanently.',
                'keywords' => ['trash', 'deleted', 'recovery', 'restore', 'removed', 'bin']
            ],
            [
                'title' => 'Secure Vault',
                'url' => route('user.vault.page'),
                'icon' => 'fas fa-lock',
                'description' => 'Protect sensitive messages with a secure passcode.',
                'keywords' => ['vault', 'lock', 'passcode', 'secure', 'private', 'encryption']
            ],
            [
                'title' => 'Account Settings',
                'url' => route('user.settings.user.page'),
                'icon' => 'fas fa-user-cog',
                'description' => 'Update profile picture, passcode, themes, and blur UI.',
                'keywords' => ['account', 'user settings', 'profile', 'theme', 'avatar', 'passcode', 'delete account', 'privacy mode', 'blur ui']
            ],
            [
                'title' => 'Message Expiry & Notifications',
                'url' => route('user.settings.messages.page'),
                'icon' => 'fas fa-history',
                'description' => 'Configure default page expiry, auto-delete, and email/WhatsApp alerts.',
                'keywords' => ['expiry', 'lifecycle', 'auto-delete', 'notifications', 'email', 'alerts', 'whatsapp']
            ],
            [
                'title' => 'Security settings',
                'url' => route('user.control-center.security.page'),
                'icon' => 'fas fa-shield-alt',
                'description' => 'Monitor active logins, parsed browser details, and masked IP logs.',
                'keywords' => ['security', 'sessions', 'logins', 'active sessions', 'browsers', 'ip address']
            ],
            [
                'title' => 'Notifications',
                'url' => route('user.notifications.page'),
                'icon' => 'fas fa-bell',
                'description' => 'View unread page alerts and direct chats.',
                'keywords' => ['notifications', 'bell', 'messages inbox', 'chats']
            ]
        ];

        $matchedSystemPages = [];
        if ($searchQuery) {
            $queryLower = strtolower($searchQuery);
            foreach ($systemPages as $page) {
                $match = false;
                if (str_contains(strtolower($page['title']), $queryLower) || str_contains(strtolower($page['description']), $queryLower)) {
                    $match = true;
                } else {
                    foreach ($page['keywords'] as $keyword) {
                        if (str_contains($keyword, $queryLower)) {
                            $match = true;
                            break;
                        }
                    }
                }
                if ($match) {
                    $matchedSystemPages[] = $page;
                }
            }
        } else {
            $matchedSystemPages = $systemPages;
        }

        if ($request->ajax() && $section === 'search') {
            return response()->json([
                'html' => view('user.pages.general.search-results', compact('searchResults', 'matchedSystemPages', 'searchQuery'))->render()
            ]);
        }

        $activeAds = \App\Models\Ad::active()->whereIn('display_location', ['dashboard', 'all'])->get();

        $isPremium = $user->isPremium();

        $folderMap = [
            'create' => 'messages.create',
            'edit-messages' => 'messages.edit-messages',
            'message-preview' => 'messages.message-preview',
            'my-messages' => 'messages.my-messages',
            'share-messages' => 'messages.share-messages',
            'share-messages-schedule' => 'messages.share-messages-schedule',
            
            'control-center-security' => 'security.control-center-security',
            'vault' => 'security.vault',
            'vault-auth' => 'security.vault-auth',
            
            'message-settings' => 'settings.message-settings',
            'settings' => 'settings.settings',
            'user-settings' => 'settings.user-settings',
            
            'dashboard' => 'general.dashboard',
            'links' => 'general.links',
            'media' => 'general.media',
            'music' => 'general.music',
            'notifications' => 'general.notifications',
            'template' => 'general.template',
            'trash' => 'general.trash',
            'search' => 'general.search',
        ];

        $viewPath = $folderMap[$section] ?? $section;

        // Fetch themes data for template gallery (all themes come from backend)
        $themes = TemplateGalleryController::THEMES;

        return view('user.pages.' . $viewPath, compact(
            'user', 'userSettings', 'message', 'messages', 'allMessagesForSelect', 'allMessagesForEdit', 'lastTwoMessages', 'notifications', 'templateList', 'generatedLinks', 'messageTemplates',
            'dashboardStats', 'recentActivity', 'viewsLast7Days', 'recentShareSends', 'allShareSends',
            'shareMessagesLink', 'shareMessagesRecipientName', 'shareMessagesRecipientPhone', 'shareMessagesMessageText', 'shareMessagesId', 'messagesWithLink',
            'activityTotal', 'activityHasPrev', 'activityHasNext', 'activityPage',
            'imagesSize', 'audioSize',
            'scheduledSends', 'activeSessions', 'searchQuery', 'searchResults', 'matchedSystemPages',
            'activeAds', 'isPremium', 'themes'
        ));
    }

    
    


    
    public function updateProfile(Request $request)
    {
        \Log::info('updateProfile called', $request->all());
        $user = $request->user();
        
        $validated = $request->validate([
            'firstName' => 'nullable|string',
            'lastName' => 'nullable|string',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'profileImage' => 'nullable|string'
        ]);

        $name = trim(($validated['firstName'] ?? '') . ' ' . ($validated['lastName'] ?? ''));
        if (empty($name)) {
            $name = $user->name;
        }

        $user->name = $name;
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        if (isset($validated['profileImage'])) {
            $user->profile_picture = $validated['profileImage'];
        }
        $user->save();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => (string)$user->id,
                'firstName' => explode(' ', $user->name)[0] ?? $user->name,
                'lastName' => implode(' ', array_slice(explode(' ', $user->name), 1)) ?? '',
                'username' => $user->username,
                'email' => $user->email,
                'profileImage' => $user->profile_picture,
            ]
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'password' => 'required|min:8|confirmed'
        ]);

        $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $user->save();

        return response()->json(['success' => true]);
    }

    public function updateProfilePicture(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                try {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete('profile-pictures/' . $user->profile_picture);
                } catch (\Throwable $e) {}
            }
            $file = $request->file('profile_picture');
            $name = 'user-' . $user->id . '-' . time() . '.jpg';
            $path = 'profile-pictures/' . $name;
            $encodedImage = \Intervention\Image\Laravel\Facades\Image::decode($file)->scaleDown(width: 800)->encodeUsingFileExtension('jpg', quality: 80);
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, (string) $encodedImage);
            $user->profile_picture = basename($path);
            $user->save();
        }

        return response()->json([
            'success' => true,
            'profile_picture' => $user->profile_picture,
            'profile_picture_url' => $user->profile_picture_url
        ]);
    }

    public function deleteProfilePicture(Request $request)
    {
        $user = $request->user();
        if ($user->profile_picture) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete('profile-pictures/' . $user->profile_picture);
            $user->profile_picture = null;
        }
        $user->save();

        return response()->json([
            'success' => true,
        ]);
    }

    public function listMessages(Request $request)
    {
        $messages = $request->user()->wishMessages()
            ->withCount('views')
            ->with('generatedLinks')
            ->get()
            ->append(['generated_link', 'is_link_active', 'status']);
            
        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    public function listTrash(Request $request)
    {
        $messages = $request->user()->wishMessages()
            ->onlyTrashed()
            ->withCount('views')
            ->with('generatedLinks')
            ->get()
            ->append(['generated_link', 'is_link_active', 'status']);
            
        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    public function listNotifications(Request $request)
    {
        $userId = $request->user()->id;
        $notifications = \App\Models\UserNotification::where(function($q) use ($userId) { 
                $q->where('user_id', $userId)->orWhere('sender_id', $userId); 
            })
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // Map them to the frontend format if necessary, or just return them directly
        $formatted = $notifications->map(function ($notif) {
            return [
                'id' => (string)$notif->id,
                'title' => $notif->title,
                'body' => $notif->message,
                'type' => $notif->type ?? 'info',
                'read' => (bool)$notif->is_read,
                'createdAt' => $notif->created_at->toIso8601String()
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formatted
        ]);
    }

    public function analytics(Request $request)
    {
        $user = $request->user();
        $messages = $user->wishMessages()->get();

        $savedMessages = $messages->filter(function($m) { return in_array($m->status, ['draft', 'active', 'expiring_soon']); })->count();
        $expiredMessages = $messages->filter(function($m) { return $m->status === 'expired'; })->count();
        
        $messageIds = $messages->pluck('id');
        $linksGenerated = \App\Models\GeneratedLinks::whereIn('wish_message_id', $messageIds)->count();
        $totalViews = \App\Models\MessageViews::whereIn('wish_message_id', $messageIds)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'savedMessages' => $savedMessages,
                'expiredMessages' => $expiredMessages,
                'linksGenerated' => $linksGenerated,
                'totalViews' => $totalViews,
            ]
        ]);
    }
    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        // Verify passcode
        $userSettings = UserSettings::where('user_id', $user->id)->first();
        if (!$userSettings || !\Illuminate\Support\Facades\Hash::check($request->confirm_passcode, $userSettings->login_passcode)) {
            return redirect()->back()->with('error', 'Passcode (2FA) verification failed.');
        }

        try {
            DB::beginTransaction();

            // 1. Delete Media Files (Storage & DB)
            $mediaFiles = \App\Models\MediaFiles::where('user_id', $user->id)->get();
            foreach ($mediaFiles as $file) {
                try {
                    if ($file->recipient_image) \Illuminate\Support\Facades\Storage::disk(config('filesystems.media_disk'))->delete($file->recipient_image);
                    if ($file->background_music) \Illuminate\Support\Facades\Storage::disk(config('filesystems.media_disk'))->delete($file->background_music);
                } catch (\Exception $e) {}
                
                $file->delete();
            }

            // 2. Delete Profile Picture
            if ($user->profile_picture) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('profile-pictures/' . $user->profile_picture);
            }

            // 3. Delete Messages & Associated Data
            $messages = WishMessages::where('user_id', $user->id)->withTrashed()->get();
            foreach ($messages as $msg) {
                MessageViews::where('wish_message_id', $msg->id)->delete();
                GeneratedLinks::where('wish_message_id', $msg->id)->delete();
                $msg->forceDelete();
            }

            // 4. Delete Notifications
            if (Schema::hasTable('notifications')) {
                DB::table('notifications')->where('user_id', $user->id)->delete();
            }
            if (Schema::hasTable('user_notifications')) {
                UserNotification::where('user_id', $user->id)->orWhere('sender_id', $user->id)->delete();
            }

            // 5. Delete Other Data
            if (Schema::hasTable('share_sends')) {
                ShareSend::where('user_id', $user->id)->delete();
            }
            

            // 6. Delete Settings
            if ($userSettings) $userSettings->delete();

            // 7. Delete User
            $user->delete();

            DB::commit();

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('success', 'Your account and all associated data have been permanently deleted.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public static function sectionRoute(string $section): string
    {
        return match ($section) {
            'dashboard' => 'user.page',
            'create' => 'user.create.page',
            'media' => 'user.media.page',
            'template' => 'user.template.page',
            'links' => 'user.links.page',
            'share-messages' => 'user.share-messages.page',
            'my-messages' => 'user.my-messages.page',
            'notifications' => 'user.notifications.page',
            'settings' => 'user.settings.page',
            default => 'user.page',
        };
    }

    private function appLocked(): bool
    {
        if (!Schema::hasTable('system_settings')) {
            return false;
        }
        return (bool) DB::table('system_settings')->where('key', 'app_locked')->value('value');
    }
}


