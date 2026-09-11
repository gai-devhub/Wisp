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
        $user = $request->user();
        $userId = $user ? $user->id : Auth::id();
        $period = strtolower($request->query('period', 'week'));

        $messageIds = \App\Models\WishMessages::where('user_id', $userId)->pluck('id');

        $data = [];
        switch ($period) {
            case 'day':
                $startOfDay = \Carbon\Carbon::today();
                for ($h = 0; $h <= 22; $h += 2) {
                    $startHour = $startOfDay->copy()->addHours($h);
                    $endHour = $startHour->copy()->addHours(1)->endOfHour();
                    $count = \App\Models\MessageViews::where(function($q) use ($userId, $messageIds) {
                            $q->where('user_id', $userId)->orWhereIn('wish_message_id', $messageIds);
                        })
                        ->where(function($q) use ($startHour, $endHour) {
                            $q->whereBetween('viewed_at', [$startHour, $endHour])
                              ->orWhereBetween('created_at', [$startHour, $endHour]);
                        })
                        ->count();

                    $data[] = [
                        'label' => $startHour->format('H:i'),
                        'count' => $count,
                    ];
                }
                break;
            case 'month':
                $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
                $endOfMonth = \Carbon\Carbon::now()->endOfMonth();
                $daysInMonth = $endOfMonth->day;

                for ($d = 1; $d <= $daysInMonth; $d += 2) {
                    $startDate = $startOfMonth->copy()->addDays($d - 1);
                    $endDate = $startDate->copy()->addDay()->endOfDay();
                    
                    if ($endDate->gt($endOfMonth)) {
                        $endDate = $endOfMonth->copy();
                    }

                    $count = \App\Models\MessageViews::where(function($q) use ($userId, $messageIds) {
                            $q->where('user_id', $userId)->orWhereIn('wish_message_id', $messageIds);
                        })
                        ->where(function($q) use ($startDate, $endDate) {
                            $q->whereBetween('viewed_at', [$startDate->copy()->startOfDay(), $endDate])
                              ->orWhereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate])
                              ->orWhereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                        })
                        ->count();

                    $data[] = [
                        'label' => $startDate->format('j/n'),
                        'count' => $count,
                    ];
                }
                break;
            default:
                $startOfWeek = \Carbon\Carbon::today()->startOfWeek(\Carbon\Carbon::SUNDAY);
                for ($i = 0; $i < 7; $i++) {
                    $date = $startOfWeek->copy()->addDays($i)->toDateString();
                    $count = \App\Models\MessageViews::where(function($q) use ($userId, $messageIds) {
                            $q->where('user_id', $userId)->orWhereIn('wish_message_id', $messageIds);
                        })
                        ->where(function($q) use ($date) {
                            $q->whereDate('viewed_at', $date)
                              ->orWhereDate('created_at', $date)
                              ->orWhere('date', $date);
                        })
                        ->count();

                    $data[] = [
                        'label' => \Carbon\Carbon::parse($date)->format('D'),
                        'count' => $count,
                    ];
                }
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
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

    public function userSettingsPage(Request $request): View|RedirectResponse
    {
        return $this->renderSection($request, 'user-settings');
    }

    public function helpSupportPage(Request $request): View|RedirectResponse
    {
        return $this->renderSection($request, 'help-support');
    }

    public function helpAssistancePage(Request $request): View|RedirectResponse
    {
        return $this->renderSection($request, 'help-assistance');
    }

    public function helpGrowthPage(Request $request): View|RedirectResponse
    {
        return $this->renderSection($request, 'help-growth');
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
        $activityItems = $activityItems->sortByDesc(function ($item) {
            return $item->time instanceof \DateTimeInterface ? $item->time->getTimestamp() : strtotime((string) $item->time);
        })->values();
        $activityTotal = $activityItems->count();
        $recentActivity = $activityItems->forPage($activityPage, 20)->values();
        $activityHasPrev = $activityPage > 1;
        $activityHasNext = ($activityPage * 20) < $activityTotal;

        $viewsLast7Days = [];
        $startOfWeek = Carbon::today()->startOfWeek(Carbon::SUNDAY);
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i)->toDateString();
            $viewsLast7Days[] = [
                'date' => $date,
                'label' => Carbon::parse($date)->format('D'),
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

        $isPremium = true; // PREMIUM GATING DISABLED — $user->isPremium();

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
            'user-settings' => 'settings.user-settings',
            'help-support' => 'settings.help-support',
            'help-assistance' => 'doc.assistance',
            'help-growth' => 'doc.growth',
            
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
        $fullViewName = str_starts_with($viewPath, 'doc.') ? $viewPath : 'user.pages.' . $viewPath;

        // Fetch themes data for template gallery (all themes come from backend)
        $themes = TemplateGalleryController::THEMES;
        $activeAds = collect();

        return view($fullViewName, compact(
            'user', 'userSettings', 'message', 'messages', 'allMessagesForSelect', 'allMessagesForEdit', 'lastTwoMessages', 'notifications', 'templateList', 'generatedLinks', 'messageTemplates',
            'dashboardStats', 'recentActivity', 'viewsLast7Days', 'recentShareSends', 'allShareSends',
            'shareMessagesLink', 'shareMessagesRecipientName', 'shareMessagesRecipientPhone', 'shareMessagesMessageText', 'shareMessagesId', 'messagesWithLink',
            'activityTotal', 'activityHasPrev', 'activityHasNext', 'activityPage',
            'imagesSize', 'audioSize',
            'scheduledSends', 'activeSessions', 'searchQuery', 'searchResults', 'matchedSystemPages',
            'themes'
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
            ->with(['generatedLinks', 'template', 'mediaFiles'])
            ->get()
            ->append(['generated_link', 'is_link_active', 'status', 'template_name', 'media_image', 'media_music', 'apple_music_url']);
            
        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    public function showMessage(Request $request, $id)
    {
        $message = $request->user()->wishMessages()
            ->withCount('views')
            ->with(['generatedLinks', 'template', 'mediaFiles'])
            ->findOrFail($id)
            ->append(['generated_link', 'is_link_active', 'status', 'template_name', 'media_image', 'media_music', 'apple_music_url']);
            
        return response()->json([
            'success' => true,
            'data' => $message
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

        $savedMessages = $messages->filter(function($m) { return is_null($m->expires_at) || $m->expires_at > now(); })->count();
        $expiredMessages = $messages->filter(function($m) { return !is_null($m->expires_at) && $m->expires_at < now(); })->count();
        
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
                'user_id' => $user->id,
                'message_count' => $messages->count(),
            ]
        ]);
    }
    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        // Verify username
        if (strtolower($request->confirm_username) !== strtolower($user->username)) {
            return redirect()->back()->with('error', 'Username verification failed. The entered username does not match.');
        }

        $userSettings = UserSettings::where('user_id', $user->id)->first();
        $userId = $user->id;

        try {
            DB::beginTransaction();

            // 1. Delete Media Files (Storage & DB)
            $mediaFiles = \App\Models\MediaFiles::where('user_id', $userId)->get();
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
            $messages = WishMessages::where('user_id', $userId)->withTrashed()->get();
            foreach ($messages as $msg) {
                MessageViews::where('wish_message_id', $msg->id)->delete();
                GeneratedLinks::where('wish_message_id', $msg->id)->delete();
                $msg->forceDelete();
            }

            // 4. Delete Notifications
            if (Schema::hasTable('notifications')) {
                DB::table('notifications')->where('user_id', $userId)->delete();
            }
            if (Schema::hasTable('user_notifications')) {
                UserNotification::where('user_id', $userId)->orWhere('sender_id', $userId)->delete();
            }

            // 5. Delete Other Data
            if (Schema::hasTable('share_sends')) {
                ShareSend::where('user_id', $userId)->delete();
            }

            // 6. Delete Settings
            if ($userSettings) $userSettings->delete();

            // 7. Delete Sanctum API Tokens (prevents FK constraint on personal_access_tokens)
            DB::table('personal_access_tokens')
                ->where('tokenable_type', 'App\\Models\\User')
                ->where('tokenable_id', $userId)
                ->delete();

            // 8. Delete any password reset tokens
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();

            // 9. Delete the user (disable FK checks to avoid any remaining constraint issues)
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $user->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            DB::commit();

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('success', 'Your account and all associated data have been permanently deleted.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Account deletion failed for user ' . $userId . ': ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Account deletion failed: ' . $e->getMessage());
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
            'settings' => 'user.settings.messages.page',
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

    public function deleteAccountApi(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $userId = $user->id;
        $userSettings = \App\Models\UserSettings::where('user_id', $userId)->first();

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $mediaFiles = \App\Models\MediaFiles::where('user_id', $userId)->get();
            foreach ($mediaFiles as $file) {
                try {
                    if ($file->recipient_image) \Illuminate\Support\Facades\Storage::disk(config('filesystems.media_disk'))->delete($file->recipient_image);
                    if ($file->background_music) \Illuminate\Support\Facades\Storage::disk(config('filesystems.media_disk'))->delete($file->background_music);
                } catch (\Exception $e) {}
                $file->delete();
            }

            if ($user->profile_picture) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('profile-pictures/' . $user->profile_picture);
            }

            $messages = \App\Models\WishMessages::where('user_id', $userId)->withTrashed()->get();
            foreach ($messages as $msg) {
                \App\Models\MessageViews::where('wish_message_id', $msg->id)->delete();
                \App\Models\GeneratedLinks::where('wish_message_id', $msg->id)->delete();
                $msg->forceDelete();
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                \Illuminate\Support\Facades\DB::table('notifications')->where('user_id', $userId)->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('user_notifications')) {
                \App\Models\UserNotification::where('user_id', $userId)->orWhere('sender_id', $userId)->delete();
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('share_sends')) {
                \App\Models\ShareSend::where('user_id', $userId)->delete();
            }

            if ($userSettings) $userSettings->delete();

            \Illuminate\Support\Facades\DB::table('personal_access_tokens')
                ->where('tokenable_type', 'App\Models\User')
                ->where('tokenable_id', $userId)
                ->delete();

            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $user->email)->delete();

            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $user->delete();
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Your account and all associated data have been permanently deleted.'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('API Account deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Account deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

}
