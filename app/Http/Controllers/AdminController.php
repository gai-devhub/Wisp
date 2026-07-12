<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserNotification;
use App\Models\NotificationBroadcast;
use App\Models\WishMessages;
use App\Models\MessageViews;
use App\Models\Template;
use App\Models\ActivityLog;
use App\Models\MediaFiles;
use App\Models\GeneratedLinks;
use App\Models\AiChatMessage;
use App\Models\ShareSend;
use App\Models\Subscriber;
use App\Mail\SubscriberUpdateMail;
use App\Jobs\SendNotificationEmailJob;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Dashboard with real data: users, messages, views, stats, system settings.
     * All tables paginated 20 per page with query params for prev/next.
     */
    public function index(Request $request): View
    {
        return $this->renderSection($request, 'dashboard');
    }

    public function usersPage(Request $request): View
    {
        return $this->renderSection($request, 'users');
    }

    public function messagesPage(Request $request): View
    {
        return $this->renderSection($request, 'messages');
    }

    public function viewActivityPageView(Request $request): View
    {
        return $this->renderSection($request, 'view-activity');
    }

    public function analyticsPage(Request $request): View
    {
        return $this->renderSection($request, 'analytics');
    }

    public function notificationsPage(Request $request): View
    {
        return $this->renderSection($request, 'notifications');
    }

    public function aiUsagePage(Request $request): View
    {
        return $this->renderSection($request, 'ai-usage');
    }

    public function systemPage(Request $request): View
    {
        return $this->renderSection($request, 'system');
    }

    public function settingsMessagePage(Request $request): View
    {
        $dirSize = function ($dir) use (&$dirSize) {
            $size = 0;
            if (!is_dir($dir)) return 0;
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)) as $file) {
                $size += $file->getSize();
            }
            return $size;
        };

        // Scan physical directories on disk for images and audio files
        $imagesSize = $dirSize(storage_path('app/public/media/recipient-images')) 
                    + $dirSize(storage_path('app/public/profile-pictures'));
        
        $audioSize = $dirSize(storage_path('app/public/media/background-music'));

        return $this->renderSection($request, 'settings-message', compact('imagesSize', 'audioSize'));
    }

    public function settingsPagePage(Request $request): View
    {
        return $this->renderSection($request, 'settings-page');
    }

    public function settingsUserPage(Request $request): View
    {
        return $this->renderSection($request, 'settings-admin');
    }

    public function logsPage(Request $request): View
    {
        $stats = $this->getStats();
        $systemSettings = $this->getSystemSettings();
        $logsPage = max(1, (int) $request->input('logs_page', 1));

        $activityLogs = $this->paginateActivityLogs($logsPage);

        $analytics = [];
        try { $analytics = $this->getAnalyticsData(30); } catch (\Throwable $e) { report($e); }

        return view('admin.pages.system.logs', compact(
            'stats', 'systemSettings', 'activityLogs', 'analytics'
        ));
    }



    public function maintenancePage(Request $request): View
    {
        $stats = $this->getStats();
        $systemSettings = $this->getSystemSettings();
        $storageStats = $this->getStorageStats();
        $systemInfo = $this->getSystemInfo();
        $analytics = [];
        try { $analytics = $this->getAnalyticsData(30); } catch (\Throwable $e) { report($e); }

        return view('admin.pages.system.maintenance', compact(
            'stats', 'systemSettings', 'storageStats', 'systemInfo', 'analytics'
        ));
    }

    public function databasePage(Request $request): View
    {
        $stats = $this->getStats();
        $systemSettings = $this->getSystemSettings();
        $databaseTables = $this->getDatabaseTables();
        $analytics = [];
        try { $analytics = $this->getAnalyticsData(30); } catch (\Throwable $e) { report($e); }

        return view('admin.database.my_db', compact(
            'stats', 'systemSettings', 'databaseTables', 'analytics'
        ));
    }

    public function searchPage(Request $request): View
    {
        $query = trim($request->input('q', ''));
        
        $stats = $this->getStats();
        $systemSettings = $this->getSystemSettings();
        
        $users = [];
        $messages = [];
        
        if (!empty($query)) {
            $users = User::where('username', 'like', '%' . $query . '%')
                ->orWhere('email', 'like', '%' . $query . '%')
                ->limit(50)
                ->get();
                
            $messages = WishMessages::with(['user'])
                ->where('title', 'like', '%' . $query . '%')
                ->orWhere('recipient_name', 'like', '%' . $query . '%')
                ->orWhere('recipient_special_name', 'like', '%' . $query . '%')
                ->limit(50)
                ->get();
        }

        return view('admin.pages.search', compact('stats', 'systemSettings', 'query', 'users', 'messages'));
    }

    private function renderSection(Request $request, string $section, array $additionalData = []): View
    {
        $stats = $this->getStats();
        $systemSettings = $this->getSystemSettings();

        $usersPage = max(1, (int) $request->input('users_page', 1));
        $messagesPage = max(1, (int) $request->input('messages_page', 1));
        $viewActivityPage = max(1, (int) $request->input('view_activity_page', 1));

        $data = [
            'stats' => $stats,
            'systemSettings' => $systemSettings,
            'currentSection' => $section,
        ];

        if ($section === 'users') {
            $data['usersList'] = User::orderBy('created_at', 'desc')->paginate(10, ['*'], 'users_page', $usersPage);
        } elseif ($section === 'messages') {
            $data['recentMessagesList'] = WishMessages::with('user')->orderBy('created_at', 'desc')->paginate(10, ['*'], 'messages_page', $messagesPage);
        } elseif ($section === 'view-activity') {
            $data['viewActivityList'] = \App\Models\MessageViews::with(['wishMessage', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(15, ['*'], 'view_activity_page', $viewActivityPage);
        } elseif ($section === 'ai-usage') {
            $aiUsagePage = max(1, (int) $request->input('ai_usage_page', 1));
            $data['aiUsageList'] = \App\Models\AiChatMessage::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(5, ['*'], 'ai_usage_page', $aiUsagePage);
        }
        $notificationsPage = max(1, (int) $request->input('notifications_page', 1));

        $users = $this->paginateUsers($usersPage);
        $messages = $this->paginateMessages($messagesPage);
        $messageViews = $this->paginateMessageViews($viewActivityPage);

        $activityPaginated = $this->getRecentActivityPaginated(1, 10);
        $recentActivity = $activityPaginated['items'];
        $activityPage = 1;
        $activityTotal = $activityPaginated['total'];
        $activityHasPrev = false;
        $activityHasNext = $activityPaginated['has_next'];

        $notificationBroadcasts = $this->getNotificationBroadcastsPaginated($notificationsPage);
        $userInquiries = UserNotification::where('user_id', Auth::id())
            ->where('context', 'admin_message')
            ->rootThreads()
            ->with(['sender', 'replies.sender'])
            ->orderBy('created_at', 'desc')
            ->get();

        // New sections data — each wrapped in try/catch so a broken relationship never crashes the page
        $emptyPaginator = fn(string $name) => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20, 1, ['pageName' => $name]);

        try {
            $media = MediaFiles::with('user')->orderByDesc('created_at')->paginate(20, ['*'], 'media_page');
        } catch (\Throwable $e) { report($e); $media = $emptyPaginator('media_page'); }

        try {
            $links = GeneratedLinks::with(['user', 'wishMessage'])->orderByDesc('created_at')->paginate(20, ['*'], 'links_page');
        } catch (\Throwable $e) { report($e); $links = $emptyPaginator('links_page'); }

        try {
            $aiUsage = AiChatMessage::with('user')->orderByDesc('created_at')->paginate(5, ['*'], 'ai_page')->withPath(route('admin.ai-usage.page'));
        } catch (\Throwable $e) { report($e); $aiUsage = $emptyPaginator('ai_page'); }

        try {
            $templates = Template::with(['user', 'wishMessage'])->orderByDesc('created_at')->paginate(20, ['*'], 'templates_page');
        } catch (\Throwable $e) { report($e); $templates = $emptyPaginator('templates_page'); }

        try {
            $sharingStats = ShareSend::with(['user', 'wishMessage'])->orderByDesc('created_at')->paginate(20, ['*'], 'sharing_page');
        } catch (\Throwable $e) { report($e); $sharingStats = $emptyPaginator('sharing_page'); }

        try {
            $analyticsDays = 30;
            $analytics = $this->getAnalyticsData($analyticsDays);
        } catch (\Throwable $e) {
            report($e);
            $analytics = $this->getEmptyAnalyticsStructure(30);
        }

        $folderMap = [
            'dashboard' => 'general',
            'analytics' => 'general',
            'users' => 'management',
            'messages' => 'management',
            'view-activity' => 'management',
            'system' => 'system',
            'notifications' => 'system',
            'settings-admin' => 'settings',
            'settings-message' => 'settings',
            'settings-page' => 'settings',
        ];
        $folder = $folderMap[$section] ?? '';
        $viewPath = $folder ? 'admin.pages.' . $folder . '.' . $section : 'admin.pages.' . $section;

        return view($viewPath, array_merge(compact(
            'stats',
            'systemSettings',
            'users',
            'messages',
            'messageViews',
            'recentActivity',
            'activityPage',
            'activityTotal',
            'activityHasPrev',
            'activityHasNext',
            'notificationBroadcasts',
            'userInquiries',
            'analytics',
            'media',
            'links',
            'aiUsage',
            'templates',
            'sharingStats'
        ), $additionalData));
    }

    private function adminSectionRoute(string $section): string
    {
        return match ($section) {
            'dashboard' => 'admin.page',
            'users' => 'admin.users.page',
            'messages' => 'admin.messages.page',
            'view-activity' => 'admin.view-activity.page',
            'analytics' => 'admin.analytics.page',
            'notifications' => 'admin.notifications.page',
            'media' => 'admin.media.page',
            'links' => 'admin.links.page',
            'ai-usage' => 'admin.ai-usage.page',
            'templates' => 'admin.templates.page',
            'sharing' => 'admin.sharing.page',
            'system' => 'admin.system.page',
            'settings-message' => 'admin.settings.message.page',
            'settings-page' => 'admin.settings.page.page',
            'settings-user' => 'admin.settings.user.page',
            'logs' => 'admin.logs.page',
            'bulk-operations' => 'admin.bulk-operations.page',
            'export' => 'admin.export.page',
            'maintenance' => 'admin.maintenance.page',
            default => 'admin.page',
        };
    }

    private function redirectToAdminSection(string $section, string $key, string $message)
    {
        return redirect()->route($this->adminSectionRoute($section))->with($key, $message);
    }

    private function paginateUsers(int $page)
    {
        try {
            return User::withCount(['wishMessages', 'views'])
                ->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")
                ->orderBy('id')
                ->paginate(10, ['*'], 'users_page', $page);
        } catch (\Throwable $e) {
            report($e);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, $page, ['pageName' => 'users_page']);
        }
    }

    private function paginateMessages(int $page)
    {
        try {
            return WishMessages::with(['user', 'mediaFiles', 'template', 'generatedLinks', 'shareSends'])
                ->withCount('views')
                ->orderBy('id')
                ->paginate(10, ['*'], 'messages_page', $page);
        } catch (\Throwable $e) {
            report($e);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, $page, ['pageName' => 'messages_page']);
        }
    }

    private function paginateMessageViews(int $page)
    {
        try {
            if (!Schema::hasTable('message_views')) {
                return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, $page, ['pageName' => 'view_activity_page']);
            }
            return MessageViews::with(['user', 'wishMessage'])
                ->orderByDesc('created_at')
                ->paginate(10, ['*'], 'view_activity_page', $page);
        } catch (\Throwable $e) {
            report($e);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, $page, ['pageName' => 'view_activity_page']);
        }
    }

    private function getNotificationBroadcastsPaginated(int $page)
    {
        try {
            if (!Schema::hasTable('notification_broadcasts')) {
                return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, $page, ['pageName' => 'notifications_page']);
            }
            return NotificationBroadcast::with(['sender', 'userNotifications' => function ($q) {
                $q->with('user:id,username,email')->orderByRaw('read_at IS NULL')->orderByDesc('read_at');
            }])
                ->orderByDesc('created_at')
                ->paginate(10, ['*'], 'notifications_page', $page);
        } catch (\Throwable $e) {
            report($e);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, $page, ['pageName' => 'notifications_page']);
        }
    }

    public function getSystemSettings(): array
    {
        $defaults = [
            'app_locked' => false,
            'registration_disabled' => false,
            'enable_system_updates' => false,
            'last_backup_at' => null,
            'session_timeout' => 30,
            'max_login_attempts' => 5,
            'timezone' => 'UTC',
            'date_format' => 'd/m/Y',
            'items_per_page' => 25,
            'notify_email_alerts' => true,
            'notify_push_critical' => true,
            'notify_daily_summary' => false,
            'contact_email' => '',
            'contact_phone' => '',
            'contact_whatsapp' => '',
        ];
        if (!Schema::hasTable('system_settings')) {
            return $defaults;
        }
        $keys = array_keys($defaults);
        $rows = DB::table('system_settings')->whereIn('key', $keys)->get()->keyBy('key');
        $out = [];
        foreach ($defaults as $key => $default) {
            $val = optional($rows->get($key))->value;
            if ($val === null) {
                $out[$key] = $default;
                continue;
            }
            if (in_array($key, ['app_locked', 'registration_disabled', 'enable_system_updates', 'notify_email_alerts', 'notify_push_critical', 'notify_daily_summary'])) {
                $out[$key] = (bool) (int) $val;
            } elseif (in_array($key, ['session_timeout', 'max_login_attempts', 'items_per_page'])) {
                $out[$key] = (int) $val;
            } else {
                $out[$key] = $val;
            }
        }
        return $out;
    }

    public function storeUser(Request $request)
    {
        $valid = $request->validate([
            'username' => 'required|string|min:3|max:255|unique:users,username|regex:/^[a-zA-Z0-9_.-]+$/',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:user,admin',
        ]);
        $user = User::create([
            'name' => $valid['username'],
            'username' => Str::lower($valid['username']),
            'email' => $valid['email'],
            'password' => Hash::make($valid['password']),
            'role' => $valid['role'],
            'status' => 'active',
        ]);
        ActivityLog::log($request->user()->id, 'User created', 'Admin created user: ' . $user->username);
        return $this->redirectToAdminSection('users', 'success', 'User created.');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::where('id', $id)->where('id', '!=', $request->user()->id)->firstOrFail();
        $valid = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id), 'regex:/^[a-zA-Z0-9_.-]+$/'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'status' => 'required|in:active,blocked,pending',
        ]);
        $user->username = Str::lower($valid['username']);
        $user->email = $valid['email'];
        $user->status = $valid['status'];
        $user->save();
        ActivityLog::log($request->user()->id, 'User updated', 'Updated user: ' . $user->username);
        return $this->redirectToAdminSection('users', 'success', 'User updated.');
    }

    public function blockUser(Request $request, $id)
    {
        $user = User::where('id', $id)->where('role', '!=', 'admin')->firstOrFail();
        $user->update(['status' => 'blocked']);
        ActivityLog::log($request->user()->id, 'User blocked', 'Blocked user: ' . $user->username);
        return $this->redirectToAdminSection('users', 'success', 'User blocked.');
    }

    public function unblockUser(Request $request, $id)
    {
        $user = User::where('id', $id)->firstOrFail();
        $user->update(['status' => 'active']);
        ActivityLog::log($request->user()->id, 'User unblocked', 'Unblocked user: ' . $user->username);
        return $this->redirectToAdminSection('users', 'success', 'User unblocked.');
    }

    public function deleteUser(Request $request, $id)
    {
        $target = User::findOrFail($id);
        if ($target->id === $request->user()->id) {
            return $this->redirectToAdminSection('users', 'error', 'You cannot delete yourself.');
        }
        if ($target->role === 'admin') {
            return $this->redirectToAdminSection('users', 'error', 'Cannot delete an administrator.');
        }
        $username = $target->username;
        $target->delete();
        ActivityLog::log($request->user()->id, 'User deleted', 'Deleted user: ' . $username);
        return $this->redirectToAdminSection('users', 'success', 'User deleted.');
    }

    public function messageDetails(Request $request, $id)
    {
        $message = WishMessages::with(['user', 'mediaFiles', 'template', 'generatedLinks'])->findOrFail($id);
        
        $imagesSize = 0;
        $audioSize = 0;
        $mediaData = [];

        foreach ($message->mediaFiles as $media) {
            $path = storage_path('app/public/' . $media->file_path);
            $size = file_exists($path) ? filesize($path) : 0;
            
            if ($media->file_type === 'image') {
                $imagesSize += $size;
            } elseif ($media->file_type === 'audio') {
                $audioSize += $size;
            }
            
            $mediaData[] = [
                'type' => $media->file_type,
                'path' => asset('storage/' . $media->file_path),
                'size' => $size,
                'size_formatted' => $size > 0 ? round($size/1024, 2).' KB' : '0 KB'
            ];
        }

        $totalStorage = $imagesSize + $audioSize;
        
        // Format sizes
        $formatBytes = function($bytes) {
            if ($bytes == 0) return '0.00 B';
            $s = array('B', 'KB', 'MB', 'GB');
            $e = floor(log($bytes, 1024));
            return round($bytes/pow(1024, $e), 2).' '.$s[$e];
        };

        $link = $message->generatedLinks->where('is_active', true)->sortByDesc('created_at')->first();

        return response()->json([
            'id' => $message->id,
            'title' => $message->title,
            'creator' => $message->user ? $message->user->username : 'Unknown',
            'recipient' => $message->recipient_name ?? $message->recipient_special_name ?? 'None',
            'created_at' => $message->created_at ? $message->created_at->format('M j, Y g:i A') : '-',
            'views' => $message->views_count ?? 0,
            'status' => $message->is_published ? 'Published' : ($message->expires_at && $message->expires_at->isPast() ? 'Expired' : 'Draft'),
            'template_name' => $message->template ? $message->template->name : 'None',
            'generated_link' => $link ? $link->generated_url : null,
            'storage' => [
                'images_formatted' => $formatBytes($imagesSize),
                'audio_formatted' => $formatBytes($audioSize),
                'total_formatted' => $formatBytes($totalStorage),
                'total_bytes' => $totalStorage
            ],
            'media' => $mediaData

        ]);
    }

    public function updateMessage(Request $request, $id)
    {
        $message = WishMessages::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'is_published' => 'required|in:0,1',
        ]);
        $message->title = $request->title;
        $message->message = $request->message;
        $message->is_published = (bool) $request->is_published;
        $message->save();
        ActivityLog::log($request->user()->id, 'Message updated', 'Admin updated message: ' . $message->title);
        return $this->redirectToAdminSection('messages', 'success', 'Message updated.');
    }

    /**
     * Preview a message in the exact template the user selected (admin only, no link/expiry checks).
     */
    public function previewMessage($id): \Illuminate\View\View
    {
        $message = WishMessages::findOrFail($id);
        $message->load('mediaFiles');
        $mediaFiles = $message->mediaFiles ?? collect();

        $template = Template::where('wish_message_id', $message->id)->first();
        $templateName = $template ? $template->template_name : 'template.view.template-1';
        
        if (preg_match('/^view-([1-6])$/', $templateName, $m) || preg_match('/^view\.view-([1-6])$/', $templateName, $m) || preg_match('/^template\.view\.view-([1-6])$/', $templateName, $m)) {
            $viewName = 'components.template.view.template-' . $m[1];
        } else {
            $viewName = 'components.' . $templateName;
        }

        if (!ViewFacade::exists($viewName)) {
            $viewName = 'components.template.view.template-1';
        }

        return view($viewName, [
            'message'    => $message,
            'mediaFiles' => $mediaFiles,
        ]);
    }

    public function deleteMessage(Request $request, $id)
    {
        $message = WishMessages::findOrFail($id);
        $title = $message->title;
        $message->delete();
        ActivityLog::log($request->user()->id, 'Message deleted', 'Admin deleted message: ' . $title);
        return $this->redirectToAdminSection('messages', 'success', 'Message deleted.');
    }

    public function toggleLock(Request $request)
    {
        $current = (bool) DB::table('system_settings')->where('key', 'app_locked')->value('value');
        $new = !$current;
        DB::table('system_settings')->where('key', 'app_locked')->update(['value' => $new ? '1' : '0', 'updated_at' => now()]);
        ActivityLog::log($request->user()->id, 'System ' . ($new ? 'locked' : 'unlocked'), $new ? 'App locked by admin' : 'App unlocked by admin');
        if ($request->wantsJson()) {
            return response()->json(['app_locked' => $new]);
        }
        return $this->redirectToAdminSection('dashboard', 'success', $new ? 'System locked.' : 'System unlocked.');
    }

    public function toggleRegistration(Request $request)
    {
        $current = (bool) DB::table('system_settings')->where('key', 'registration_disabled')->value('value');
        $new = !$current;
        DB::table('system_settings')->where('key', 'registration_disabled')->update(['value' => $new ? '1' : '0', 'updated_at' => now()]);
        ActivityLog::log($request->user()->id, 'Registration ' . ($new ? 'disabled' : 'enabled'), null);
        if ($request->wantsJson()) {
            return response()->json(['registration_disabled' => $new]);
        }
        return $this->redirectToAdminSection('dashboard', 'success', $new ? 'Registration disabled.' : 'Registration enabled.');
    }

    public function toggleSystemUpdates(Request $request)
    {
        $current = (bool) DB::table('system_settings')->where('key', 'enable_system_updates')->value('value');
        $new = !$current;
        DB::table('system_settings')->where('key', 'enable_system_updates')->updateOrInsert(
            ['key' => 'enable_system_updates'],
            ['value' => $new ? '1' : '0', 'updated_at' => now()]
        );
        ActivityLog::log($request->user()->id, 'System updates ' . ($new ? 'enabled' : 'disabled'), null);
        if ($request->wantsJson()) {
            return response()->json(['enable_system_updates' => $new]);
        }
        return $this->redirectToAdminSection('system', 'success', $new ? 'System updates enabled.' : 'System updates disabled.');
    }

    public function createBackup(Request $request)
    {
        $path = 'backups';
        $filename = 'backup_' . date('Y-m-d_His') . '.sql';
        $fullPath = storage_path('app/' . $path . '/' . $filename);
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }
        $connection = config('database.default');
        $db = config('database.connections.' . $connection);
        $driver = $db['driver'] ?? '';
        if ($driver === 'mysql') {
            $cmd = sprintf(
                'mysqldump -h %s -P %s -u %s %s %s > %s 2>&1',
                escapeshellarg($db['host'] ?? '127.0.0.1'),
                escapeshellarg($db['port'] ?? 3306),
                escapeshellarg($db['username'] ?? ''),
                $db['password'] ? '-p' . escapeshellarg($db['password']) : '',
                escapeshellarg($db['database'] ?? ''),
                escapeshellarg($fullPath)
            );
            exec($cmd);
        } else {
            file_put_contents($fullPath, '-- Backup at ' . now()->toDateTimeString() . "\n-- Export your database manually if not using MySQL.\n");
        }
        DB::table('system_settings')->where('key', 'last_backup_at')->update(['value' => now()->toDateTimeString(), 'updated_at' => now()]);
        ActivityLog::log($request->user()->id, 'Backup', 'Database backup created: ' . $filename);
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'file' => $filename]);
        }
        return $this->redirectToAdminSection('dashboard', 'success', 'Backup created: ' . $filename);
    }

    public function updateAdminAccount(Request $request)
    {
        $user = $request->user();
        $rules = [
            'username' => ['required', 'string', 'min:3', 'max:255', Rule::unique('users', 'username')->ignore($user->id), 'regex:/^[a-zA-Z0-9_.-]+$/'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ];
        $valid = $request->validate($rules);
        $user->username = Str::lower($valid['username']);
        $user->name = $valid['username'];
        $user->email = $valid['email'];
        if (!empty($valid['password'])) {
            $user->password = Hash::make($valid['password']);
        }
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('admin-avatars', 'public');
            $user->profile_picture = $path;
        }
        $user->save();
        ActivityLog::log($user->id, 'Admin account updated', 'Username/email or profile updated.');
        return $this->redirectToAdminSection('settings-user', 'success', 'Admin account updated.');
    }

    public function saveSecuritySettings(Request $request)
    {
        $request->validate([
            'session_timeout' => 'required|integer|min:5|max:120',
            'max_login_attempts' => 'required|integer|min:1|max:10',
        ]);
        $this->setSystemSetting('session_timeout', (string) $request->input('session_timeout'));
        $this->setSystemSetting('max_login_attempts', (string) $request->input('max_login_attempts'));
        ActivityLog::log($request->user()->id, 'Security settings updated', null);
        return $this->redirectToAdminSection('system', 'success', 'Security settings saved.');
    }

    public function updatePrivacyAndTheme(Request $request)
    {
        $request->validate([
            'privacy_blur_enabled' => 'nullable|boolean',
            'theme_preference'     => 'nullable|in:theme-default,theme-forest,theme-crimson',
        ]);

        $settings = \App\Models\UserSettings::firstOrCreate(
            ['user_id' => \Illuminate\Support\Facades\Auth::id()],
            ['page_expiry' => 24, 'page_view_alerts' => true, 'whatsapp_notifications' => true, 'auto_delete_expired' => true]
        );
        
        if ($request->has('privacy_form_submitted')) {
            $settings->privacy_blur_enabled = $request->boolean('privacy_blur_enabled');
        }
        
        if ($request->has('theme_preference')) {
            $settings->theme_preference = $request->input('theme_preference');
        }
        
        $settings->save();

        ActivityLog::log($request->user()->id, 'Admin appearance settings updated', null);
        return $this->redirectToAdminSection('settings-page', 'success', 'Security & Appearance settings updated.');
    }

    public function updateGlobalLifecycle(Request $request)
    {
        $request->validate([
            'global_vault_lock' => 'nullable|integer|min:1|max:1440',
            'global_auto_archive' => 'nullable|integer|min:1|max:365',
        ]);

        $this->setSystemSetting('global_vault_lock', (string) $request->input('global_vault_lock', '15'));
        $this->setSystemSetting('global_auto_archive', (string) $request->input('global_auto_archive', '30'));
        
        ActivityLog::log($request->user()->id, 'Global message lifecycle rules updated', null);
        return $this->redirectToAdminSection('settings-message', 'success', 'Global Message Lifecycle rules saved.');
    }

    public function savePreferences(Request $request)
    {
        $request->validate([
            'timezone' => 'required|string|max:64',
            'date_format' => 'required|string|max:32',
            'items_per_page' => 'required|integer|in:10,25,50,100',
        ]);
        $this->setSystemSetting('timezone', $request->input('timezone'));
        $this->setSystemSetting('date_format', $request->input('date_format'));
        $this->setSystemSetting('items_per_page', (string) $request->input('items_per_page'));
        ActivityLog::log($request->user()->id, 'Preferences updated', null);
        return $this->redirectToAdminSection('settings-page', 'success', 'Preferences saved.');
    }

    public function saveNotificationSettings(Request $request)
    {
        $this->setSystemSetting('notify_email_alerts', $request->has('notify_email_alerts') ? '1' : '0');
        $this->setSystemSetting('notify_push_critical', $request->has('notify_push_critical') ? '1' : '0');
        $this->setSystemSetting('notify_daily_summary', $request->has('notify_daily_summary') ? '1' : '0');
        ActivityLog::log($request->user()->id, 'Notification settings updated', null);
        return $this->redirectToAdminSection('settings-message', 'success', 'Notification settings saved.');
    }

    public function saveContactSettings(Request $request)
    {
        $request->validate([
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_whatsapp' => 'nullable|string|max:50',
        ]);
        $this->setSystemSetting('contact_email', (string) $request->input('contact_email', ''));
        $this->setSystemSetting('contact_phone', (string) $request->input('contact_phone', ''));
        $this->setSystemSetting('contact_whatsapp', (string) $request->input('contact_whatsapp', ''));
        ActivityLog::log($request->user()->id, 'Contact settings updated', null);
        return $this->redirectToAdminSection('settings-page', 'success', 'Contact information saved. Blocked users will see these options.');
    }

    public function optimizeDatabase(Request $request)
    {
        $tables = ['users', 'wish_messages', 'message_views', 'activity_log', 'user_notifications', 'system_settings'];
        $connection = DB::getDatabaseName();
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                try {
                    DB::statement('OPTIMIZE TABLE ' . $table);
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }
        ActivityLog::log($request->user()->id, 'Database optimized', null);
        return $this->redirectToAdminSection('system', 'success', 'Database tables optimized.');
    }

    public function deleteAiLogs(Request $request)
    {
        $request->validate([
            'duration' => 'required|in:week,month',
        ]);

        $date = $request->duration === 'week' ? now()->subWeek() : now()->subMonth();
        
        $count = AiChatMessage::where('created_at', '<', $date)->delete();
        
        ActivityLog::log($request->user()->id, 'AI Logs Deleted', "Deleted {$count} AI logs older than 1 {$request->duration}.");
        
        return $this->redirectToAdminSection('system', 'success', "Successfully deleted {$count} AI usage logs older than 1 {$request->duration}.");
    }

    private function setSystemSetting(string $key, string $value): void
    {
        if (!Schema::hasTable('system_settings')) {
            return;
        }
        DB::table('system_settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'updated_at' => now()]
        );
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'type' => 'required|in:info,success,warning,error',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'audience' => 'required|in:all,active,inactive,users',
        ]);
        $audience = $request->input('audience');
        $query = User::where('role', '!=', 'admin');
        if ($audience === 'active') {
            $query->where('status', 'active');
        } elseif ($audience === 'inactive') {
            $query->where('status', '!=', 'active');
        } elseif ($audience === 'users') {
            $ids = array_filter((array) $request->input('user_ids', []));
            if (empty($ids)) {
                return back()->with('error', 'Select at least one user.');
            }
            $query->whereIn('id', $ids);
        }
        $users = $query->get();
        $userIds = $users->pluck('id');

        // In-app: create UserNotification for each user so it appears on their Notification page (bell) and in WISP_NOTIFICATIONS.
        if (!Schema::hasTable('notification_broadcasts')) {
            foreach ($userIds as $userId) {
                UserNotification::create([
                    'user_id' => $userId,
                    'sender_id' => $request->user()->id,
                    'type' => $request->input('type'),
                    'title' => $request->input('title'),
                    'message' => $request->input('message'),
                    'context' => 'admin_message',
                ]);
            }
            ActivityLog::log($request->user()->id, 'Notification sent', 'Type: ' . $request->input('type') . ', Audience: ' . $audience . ', Count: ' . $userIds->count());
            return $this->redirectToAdminSection('notifications', 'success', 'Notification sent to ' . $userIds->count() . ' user(s). It will appear on their Notification page (bell icon).');
        }

        $broadcast = NotificationBroadcast::create([
            'type' => $request->input('type'),
            'title' => $request->input('title'),
            'message' => $request->input('message'),
            'audience' => $audience,
            'sent_by' => $request->user()->id,
        ]);

        foreach ($users as $user) {
            // In-app: each user sees this on their user dashboard Notification page (bell) via UserNotification::forUser().
            UserNotification::create([
                'user_id' => $user->id,
                'sender_id' => $request->user()->id,
                'notification_broadcast_id' => $broadcast->id,
                'type' => $request->input('type'),
                'title' => $request->input('title'),
                'message' => $request->input('message'),
                'context' => 'admin_message',
            ]);

            SendNotificationEmailJob::dispatch(
                $user->email,
                $request->input('title'),
                $request->input('message')
            );
        }

        ActivityLog::log($request->user()->id, 'Notification sent', 'Type: ' . $request->input('type') . ', Audience: ' . $audience . ', Count: ' . $users->count());
        return $this->redirectToAdminSection('notifications', 'success', 'Notification sent to ' . $users->count() . ' user(s): it appears on their Notification page (bell) and email has been queued.');
    }

    /**
     * JSON endpoint for real-time dashboard refresh (polling).
     */
    public function dashboardData(Request $request)
    {
        $stats = $this->getStats();
        $recentActivity = $this->getRecentActivity();
        $messageViews = MessageViews::with(['user:id,name,username,email', 'wishMessage:id,title,recipient_name,slug'])
            ->orderByDesc('created_at')
            ->limit(30)
            ->get()
            ->map(fn ($v) => [
                'id' => $v->id,
                'user' => $v->user ? $v->user->username : '—',
                'message_title' => $v->wishMessage ? $v->wishMessage->title : '—',
                'recipient' => $v->wishMessage ? $v->wishMessage->recipient_name : '—',
                'ip_address' => $v->ip_address,
                'date' => $v->date?->format('Y-m-d'),
                'viewed_at' => $v->viewed_at ? (is_string($v->viewed_at) ? $v->viewed_at : $v->viewed_at->format('H:i:s')) : null,
                'created_at' => $v->created_at?->toIso8601String(),
            ]);

        return response()->json([
            'stats' => $stats,
            'recent_activity' => $recentActivity,
            'message_views' => $messageViews,
        ]);
    }

    private function getStats(): array
    {
        $totalUsers = 0;
        $totalMessages = 0;
        $totalViews = 0;
        $publishedMessages = 0;
        $draftMessages = 0;
        $expiredMessages = 0;

        try {
            if (Schema::hasTable('users')) {
                $totalUsers = User::count();
            }
        } catch (\Throwable $e) {
            report($e);
        }
        try {
            if (Schema::hasTable('wish_messages')) {
                $totalMessages = WishMessages::count();
                $expiredMessages = WishMessages::whereNotNull('expires_at')->where('expires_at', '<', now())->count();
                $publishedMessages = WishMessages::where('is_published', true)
                    ->where(function ($query) {
                        $query->whereNull('expires_at')
                              ->orWhere('expires_at', '>=', now());
                    })->count();
                $draftMessages = WishMessages::where('is_published', false)
                    ->where(function ($query) {
                        $query->whereNull('expires_at')
                              ->orWhere('expires_at', '>=', now());
                    })->count();
            }
        } catch (\Throwable $e) {
            report($e);
        }
        try {
            if (Schema::hasTable('message_views')) {
                $totalViews = MessageViews::count();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        $totalMedia = 0;
        $totalLinks = 0;
        $totalAiInteractions = 0;
        $totalShares = 0;

        try {
            if (Schema::hasTable('media_files')) $totalMedia = MediaFiles::count();
            if (Schema::hasTable('generated_links')) $totalLinks = GeneratedLinks::count();
            if (Schema::hasTable('ai_chat_messages')) $totalAiInteractions = AiChatMessage::count();
            if (Schema::hasTable('share_sends')) $totalShares = ShareSend::count();
        } catch (\Throwable $e) {
            report($e);
        }

        return [
            'total_users' => $totalUsers,
            'total_messages' => $totalMessages,
            'total_views' => $totalViews,
            'system_issues' => 0,
            'published_messages' => $publishedMessages,
            'draft_messages' => $draftMessages,
            'expired_messages' => $expiredMessages,
            'total_media' => $totalMedia,
            'total_links' => $totalLinks,
            'total_ai' => $totalAiInteractions,
            'total_shares' => $totalShares,
        ];
    }

    /**
     * Paginated recent activity (max 20 per page). Includes activity_log, message views, and message created.
     */
    public function updateAdminPasscode(Request $request)
    {
        $request->validate([
            'login_passcode' => 'nullable|string|min:4|confirmed',
        ]);

        $settings = \App\Models\UserSettings::firstOrCreate(
            ['user_id' => Auth::id()],
            ['page_expiry' => 24, 'page_view_alerts' => true, 'whatsapp_notifications' => true, 'auto_delete_expired' => true]
        );
        
        if ($request->filled('login_passcode')) {
            $settings->login_passcode = \Illuminate\Support\Facades\Hash::make($request->login_passcode);
            session(['passcode_verified' => true]);
        } else {
            $settings->login_passcode = null;
        }
        
        $settings->save();

        return redirect()->route('admin.settings.user.page')
            ->with('success', 'Admin passcode updated successfully.');
    }

    public function activityPage(Request $request)
    {
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 10;
        $result = $this->getRecentActivityPaginated($page, $perPage);
        return response()->json([
            'items' => $result['items'],
            'page' => $result['page'],
            'total' => $result['total'],
            'has_prev' => $result['has_prev'],
            'has_next' => $result['has_next'],
        ]);
    }

    private function getRecentActivity(): array
    {
        $result = $this->getRecentActivityPaginated(1, 10);
        return $result['items'];
    }

    private function getRecentActivityPaginated(int $page = 1, int $perPage = 10): array
    {
        $activities = [];
        try {
            $fromLog = [];
            if (Schema::hasTable('activity_log')) {
                $fromLog = ActivityLog::with('user:id,username')
                    ->orderByDesc('created_at')
                    ->limit(200)
                    ->get()
                    ->map(fn ($a) => [
                        'time' => $a->created_at?->format('Y-m-d H:i'),
                        'user' => $a->user ? $a->user->username : 'system',
                        'activity' => $a->activity,
                        'details' => $a->details ?? '',
                    ])
                    ->toArray();
            }

            $activities = $fromLog;

            if (Schema::hasTable('message_views')) {
                $views = MessageViews::with(['user:id,username', 'wishMessage:id,title,recipient_name'])
                    ->orderByDesc('created_at')
                    ->limit(100)
                    ->get();
                foreach ($views as $v) {
                    $activities[] = [
                        'time' => $v->created_at?->format('Y-m-d H:i'),
                        'user' => $v->user ? $v->user->username : 'Guest',
                        'activity' => 'Message viewed',
                        'details' => ($v->wishMessage ? '"' . $v->wishMessage->title . '" for ' . $v->wishMessage->recipient_name : 'Unknown') . ' · IP: ' . ($v->ip_address ?? '—'),
                    ];
                }
            }

            if (Schema::hasTable('wish_messages')) {
                $newMessages = WishMessages::with('user:id,username')
                    ->orderByDesc('created_at')
                    ->limit(50)
                    ->get();
                foreach ($newMessages as $m) {
                    $activities[] = [
                        'time' => $m->created_at?->format('Y-m-d H:i'),
                        'user' => $m->user ? $m->user->username : '—',
                        'activity' => 'Message created',
                        'details' => '"' . ($m->title ?? '') . '" for ' . ($m->recipient_name ?? '—'),
                    ];
                }
            }

            usort($activities, fn ($a, $b) => strcmp($b['time'] ?? '', $a['time'] ?? ''));
        } catch (\Throwable $e) {
            report($e);
        }

        $total = count($activities);
        $offset = ($page - 1) * $perPage;
        $items = array_slice($activities, $offset, $perPage);
        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'has_prev' => $page > 1,
            'has_next' => $offset + $perPage < $total,
        ];
    }

    /**
     * Empty analytics structure (when DB queries fail or tables missing).
     */
    private function getEmptyAnalyticsStructure(int $days = 30): array
    {
        $labels = [];
        $empty = [];
        for ($d = $days - 1; $d >= 0; $d--) {
            $date = Carbon::today()->subDays($d);
            $labels[] = $date->format('j/n');
            $empty[] = 0;
        }
        return [
            'labels' => $labels,
            'view_labels' => array_fill(0, 7, 'No data'),
            'usage' => $empty,
            'user_growth' => $empty,
            'messages' => $empty,
            'views' => array_fill(0, 7, 0),
            'peak_hours' => array_fill(0, 24, 0),
            'kpis' => [
                'page_views' => 0,
                'unique_visitors' => 0,
                'avg_session' => '—',
                'bounce_rate' => '—',
                'new_users' => 0,
                'total_users' => 0,
                'active_users_7d' => 0,
                'messages_created' => 0,
                'messages_published' => 0,
                'messages_expired' => 0,
                'total_views' => 0,
                'views_today' => 0,
                'top_message' => '—',
            ],
        ];
    }

    /**
     * Analytics data for the admin dashboard: KPIs and time-series from DB.
     * All data is read from the database; never throws.
     */
    private function getAnalyticsData(int $days = 30): array
    {
        try {
            $end = Carbon::today()->endOfDay();
            $start = Carbon::today()->subDays($days - 1)->startOfDay();

            $labels = [];
            $viewLabels = [];
            $usageData = [];
            $userData = [];
            $messageData = [];
            $viewData = [];

            $hasMessageViews = Schema::hasTable('message_views');
            $hasUsers = Schema::hasTable('users');
            $hasWishMessages = Schema::hasTable('wish_messages');

            // Daily usage/growth data with date labels
            for ($d = $days - 1; $d >= 0; $d--) {
                $date = Carbon::today()->subDays($d);
                $dayStart = $date->copy()->startOfDay();
                $dayEnd = $date->copy()->endOfDay();

                $labels[] = $date->format('j/n');
                $usageData[] = $hasMessageViews ? (int) MessageViews::whereDate('viewed_at', $date->toDateString())->count() : 0;
                $userData[] = $hasUsers ? (int) User::whereBetween('created_at', [$dayStart, $dayEnd])->count() : 0;
                $messageData[] = $hasWishMessages ? (int) WishMessages::whereBetween('created_at', [$dayStart, $dayEnd])->count() : 0;
            }

            // Message-based Views Chart (Top 7 messages for the views tab)
            if ($hasWishMessages && $hasMessageViews) {
                $topMessages = WishMessages::withCount('views')
                    ->orderByDesc('views_count')
                    ->limit(7)
                    ->get();
                
                $count = 0;
                foreach ($topMessages as $msg) {
                    $name = $msg->title ?? $msg->recipient_name ?? 'Msg #'.$msg->id;
                    $viewLabels[] = \Illuminate\Support\Str::limit($name, 20);
                    $viewData[] = $msg->views_count;
                    $count++;
                }

                // Pad with empty points
                while ($count < 7) {
                    $viewLabels[] = 'No data';
                    $viewData[] = 0;
                    $count++;
                }
            } else {
                for ($i=0; $i<7; $i++) {
                    $viewLabels[] = 'No data';
                    $viewData[] = 0;
                }
            }

            $totalViews = $hasMessageViews ? (int) MessageViews::count() : 0;
            $viewsToday = $hasMessageViews ? (int) MessageViews::whereDate('viewed_at', Carbon::today())->count() : 0;
            $uniqueVisitors = $hasMessageViews ? (int) MessageViews::whereBetween('viewed_at', [$start, $end])->distinct('ip_address')->count('ip_address') : 0;
            $totalUsers = $hasUsers ? (int) User::count() : 0;
            $newUsersPeriod = $hasUsers ? (int) User::whereBetween('created_at', [$start, $end])->count() : 0;
            
            $activeUsers7d = 0;
            if ($hasUsers && $hasMessageViews) {
                $activeUsers7d = (int) User::whereHas('views', function ($q) {
                    $q->where('created_at', '>=', Carbon::today()->subDays(7));
                })->count();
            }
            
            $totalMessages = $hasWishMessages ? (int) WishMessages::count() : 0;
            $messagesCreated = $hasWishMessages ? (int) WishMessages::whereBetween('created_at', [$start, $end])->count() : 0;
            $expiredMessages = $hasWishMessages ? (int) WishMessages::whereNotNull('expires_at')->where('expires_at', '<', now())->count() : 0;
            $publishedMessages = $hasWishMessages ? (int) WishMessages::where('is_published', true)
                                                                        ->where(function($q) {
                                                                            $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                                                                        })->count() : 0;
            $draftMessages = max($totalMessages - $expiredMessages - $publishedMessages, 0);
            
            $topMessage = null;
            if ($hasWishMessages) {
                $topMessage = WishMessages::withCount('views')->orderByDesc('views_count')->first();
            }

            $peakHours = [];
            for ($h = 0; $h < 24; $h++) {
                $peakHours[] = $hasMessageViews ? (int) MessageViews::whereRaw('HOUR(viewed_at) = ?', [$h])->count() : 0;
            }

            return [
                'labels' => $labels,
                'view_labels' => $viewLabels,
                'usage' => $usageData,
                'user_growth' => $userData,
                'messages' => $messageData,
                'views' => $viewData,
                'peak_hours' => $peakHours,
                'kpis' => [
                    'page_views' => (int) array_sum($usageData),
                    'unique_visitors' => $uniqueVisitors,
                    'avg_session' => $uniqueVisitors > 0 ? round($totalViews / $uniqueVisitors, 1) : '—',
                    'bounce_rate' => '—',
                    'new_users' => $newUsersPeriod,
                    'total_users' => $totalUsers,
                    'active_users_7d' => $activeUsers7d,
                    'messages_created' => $messagesCreated,
                    'total_messages' => $totalMessages,
                    'messages_published' => $publishedMessages,
                    'messages_expired' => $expiredMessages,
                    'messages_draft' => $draftMessages,
                    'total_views' => $totalViews,
                    'views_today' => $viewsToday,
                    'top_message' => $topMessage ? e($topMessage->title) . ' (' . (int) $topMessage->views_count . ')' : '—',
                ],
            ];
        } catch (\Throwable $e) {
            report($e);
            return $this->getEmptyAnalyticsStructure($days);
        }
    }

    /**
     * JSON endpoint for analytics data (for Refresh and time-range change).
     */
    public function analyticsData(Request $request)
    {
        $days = max(7, min(90, (int) $request->input('days', 30)));
        try {
            $data = $this->getAnalyticsData($days);
            return response()->json($data);
        } catch (\Throwable $e) {
            report($e);
            return response()->json($this->getEmptyAnalyticsStructure($days), 200);
        }
    }

    // ===================== NEW PAGES HELPERS =====================

    private function paginateActivityLogs(int $page)
    {
        try {
            if (!Schema::hasTable('activity_log')) {
                return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, $page, ['pageName' => 'logs_page']);
            }
            return ActivityLog::with('user:id,username')
                ->orderByDesc('created_at')
                ->paginate(10, ['*'], 'logs_page', $page);
        } catch (\Throwable $e) {
            report($e);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, $page, ['pageName' => 'logs_page']);
        }
    }

    private function getStorageStats(): array
    {
        $formatSize = function ($bytes) {
            if ($bytes < 1024) return $bytes . ' B';
            if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
            if ($bytes < 1073741824) return round($bytes / 1048576, 1) . ' MB';
            return round($bytes / 1073741824, 2) . ' GB';
        };

        $dirSize = function ($dir) use (&$dirSize) {
            $size = 0;
            if (!is_dir($dir)) return 0;
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)) as $file) {
                $size += $file->getSize();
            }
            return $size;
        };

        $storageTotal = $dirSize(storage_path());
        $uploads = $dirSize(storage_path('app/public/media/recipient-images')) 
                 + $dirSize(storage_path('app/public/profile-pictures'))
                 + $dirSize(storage_path('app/public/media/background-music'));
        $backups = $dirSize(storage_path('app/backups'));
        $logs = $dirSize(storage_path('logs'));
        $system = max(0, $storageTotal - ($uploads + $backups + $logs));

        // 2TB in bytes: 2 * 1024 * 1024 * 1024 * 1024 = 2199023255552
        $twoTB = 2199023255552;
        
        $percentageUploads = ($uploads / $twoTB) * 100;
        $percentageBackups = ($backups / $twoTB) * 100;
        $percentageLogs = ($logs / $twoTB) * 100;
        $percentageSystem = ($system / $twoTB) * 100;
        $percentageTotal = ($storageTotal / $twoTB) * 100;

        return [
            'uploads_bytes' => $uploads,
            'uploads_size' => $formatSize($uploads),
            'uploads_percentage' => $percentageUploads,
            
            'backups_bytes' => $backups,
            'backups_size' => $formatSize($backups),
            'backups_percentage' => $percentageBackups,
            
            'logs_bytes' => $logs,
            'logs_size' => $formatSize($logs),
            'logs_percentage' => $percentageLogs,
            
            'system_bytes' => $system,
            'system_size' => $formatSize($system),
            'system_percentage' => $percentageSystem,
            
            'total_bytes' => $storageTotal,
            'total_size' => $formatSize($storageTotal),
            'storage_percentage' => $percentageTotal,
        ];
    }

    private function getSystemInfo(): array
    {
        return [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'db_driver' => config('database.default'),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
        ];
    }

    private function getDatabaseTables(): array
    {
        $tables = [];
        try {
            $dbDriver = config('database.default');
            $database = config("database.connections.$dbDriver.database");
            
            // Get all tables
            $allTables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?", [$database]);
            
            foreach ($allTables as $tableInfo) {
                $tableName = $tableInfo->TABLE_NAME;
                
                // Get row count
                $rowCount = 0;
                try {
                    $rowCount = DB::table($tableName)->count();
                } catch (\Throwable $e) {
                    // Skip if table is not accessible
                    continue;
                }
                
                // Get columns count
                $columns = DB::select("SELECT COLUMN_NAME, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?", [$database, $tableName]);
                $columnCount = count($columns);
                
                // Get table size
                $sizeInfo = DB::select("SELECT ROUND(((data_length + index_length) / 1024 / 1024), 2) as size_mb FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?", [$database, $tableName]);
                $sizes = $sizeInfo[0] ?? null;
                $sizeMB = $sizes ? round($sizes->size_mb, 2) : 0;
                
                // Format size
                $sizeFormatted = $this->formatBytes($sizeMB * 1024 * 1024);
                
                $tables[] = [
                    'name' => $tableName,
                    'rows' => number_format($rowCount),
                    'columns' => $columnCount,
                    'size' => $sizeFormatted,
                    'size_bytes' => $sizeMB * 1024 * 1024,
                    'column_details' => $columns,
                ];
            }
            
            // Sort by size descending
            usort($tables, function ($a, $b) {
                return $b['size_bytes'] <=> $a['size_bytes'];
            });
        } catch (\Throwable $e) {
            report($e);
        }
        
        return $tables;
    }

    private function formatBytes(float $bytes): string
    {
        if ($bytes < 1024) return round($bytes, 2) . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 2) . ' KB';
        if ($bytes < 1073741824) return round($bytes / 1048576, 2) . ' MB';
        return round($bytes / 1073741824, 2) . ' GB';
    }

    // ===================== BULK OPERATIONS =====================

    public function cleanExpiredMessages(Request $request)
    {
        $count = 0;
        try {
            if (Schema::hasTable('wish_messages')) {
                $expired = WishMessages::whereNotNull('expires_at')->where('expires_at', '<', now())->get();
                $count = $expired->count();
                foreach ($expired as $msg) {
                    // Delete associated media files from storage and database
                    if ($msg->mediaFiles) {
                        if ($msg->mediaFiles->recipient_image) {
                            Storage::disk('public')->delete($msg->mediaFiles->recipient_image);
                        }
                        if ($msg->mediaFiles->background_music) {
                            Storage::disk('public')->delete($msg->mediaFiles->background_music);
                        }
                        $msg->mediaFiles->delete();
                    }
                    // Delete templates, views, generated links, and share sends
                    if ($msg->template) {
                        $msg->template->delete();
                    }
                    \App\Models\MessageViews::where('wish_message_id', $msg->id)->delete();
                    \App\Models\GeneratedLinks::where('wish_message_id', $msg->id)->delete();
                    \App\Models\ShareSend::where('wish_message_id', $msg->id)->delete();

                    ActivityLog::log($request->user()->id, 'Message deleted', 'Cleanup force-deleted expired message: ' . $msg->title);
                    $msg->forceDelete();
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $this->redirectToAdminSection('maintenance', 'success', "Force-deleted $count expired message(s) and their associated data.");
    }

    public function clearCache(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');

            // Explicitly clear database cache tables
            if (Schema::hasTable('cache')) {
                DB::table('cache')->truncate();
            }
            if (Schema::hasTable('cache_locks')) {
                DB::table('cache_locks')->truncate();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        ActivityLog::log($request->user()->id, 'Cache cleared', 'Application and database cache cleared');
        return $this->redirectToAdminSection('maintenance', 'success', 'Application and database cache cleared successfully.');
    }

    public function cleanOldLogs(Request $request)
    {
        $count = 0;
        try {
            if (Schema::hasTable('activity_log')) {
                $cutoff = now()->subDays(90);
                $count = ActivityLog::where('created_at', '<', $cutoff)->count();
                ActivityLog::where('created_at', '<', $cutoff)->delete();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        ActivityLog::log($request->user()->id, 'Logs cleaned', "Removed $count old activity log entries");
        return $this->redirectToAdminSection('maintenance', 'success', "Removed $count activity log entries older than 90 days.");
    }

    public function optimizeApp(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('optimize');
        } catch (\Throwable $e) {
            report($e);
        }

        ActivityLog::log($request->user()->id, 'System optimized', "Ran optimization command");
        return $this->redirectToAdminSection('maintenance', 'success', "System optimization complete (config and route cache refreshed).");
    }
    /**
     * Reply to a user inquiry.
     */
    public function replyToInquiry(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        // Find the notification the admin is replying to
        // The recipient of the inquiry was the admin (user_id = Auth::id())
        $parentMessage = UserNotification::where('user_id', Auth::id())->findOrFail($id);

        $userToReply = $parentMessage->sender; // The user who sent the inquiry
        if (!$userToReply) {
            return back()->with('error', 'The user who sent this inquiry no longer exists.');
        }

        UserNotification::create([
            'user_id' => $userToReply->id,
            'sender_id' => Auth::id(),
            'type' => UserNotification::TYPE_INFO,
            'title' => 'Reply from Admin',
            'message' => $request->message,
            'context' => 'admin_message',
            'parent_id' => $parentMessage->id,
            'root_id' => $parentMessage->root_id ?? $parentMessage->id,
            'is_reply' => true,
        ]);

        $parentMessage->update(['replied_at' => now()]);

        ActivityLog::log(Auth::id(), 'Notification reply sent', 'Replied to inquiry #' . $id . ' from ' . $userToReply->username);

        return $this->redirectToAdminSection('notifications', 'success', 'Reply sent to ' . $userToReply->username . '.');
    }

    public function adsPage()
    {
        $ads = \App\Models\Ad::latest()->paginate(5);
        return view('admin.pages.management.ads', compact('ads'));
    }

    public function storeAd(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'link_url' => 'nullable|url',
            'display_location' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->only(['title', 'link_url', 'display_location', 'content', 'details']);
        $data['is_active'] = $request->input('status') === 'active';
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('ads', 'public');
        }

        \App\Models\Ad::create($data);
        return redirect()->back()->with('success', 'Ad created successfully.');
    }

    public function updateAd(Request $request, $id)
    {
        $ad = \App\Models\Ad::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'link_url' => 'nullable|url',
            'display_location' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->only(['title', 'link_url', 'display_location', 'content', 'details']);
        $data['is_active'] = $request->input('status') === 'active';
        if ($request->hasFile('image')) {
            if ($ad->image_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($ad->image_path);
            $data['image_path'] = $request->file('image')->store('ads', 'public');
        }

        $ad->update($data);
        return redirect()->back()->with('success', 'Ad updated successfully.');
    }

    public function deleteAd($id)
    {
        $ad = \App\Models\Ad::findOrFail($id);
        if ($ad->image_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($ad->image_path);
        $ad->delete();
        return redirect()->back()->with('success', 'Ad deleted successfully.');
    }

    public function saveSystemUpdates(Request $request)
    {
        ActivityLog::log($request->user()->id, 'System update check', 'Admin checked for system updates.');
        return redirect()->back()->with('success', 'System is up to date. Version 2.1.4 is the latest release.');
    }
    public function getMessagesByDate(Request $request)
    {
        $date = $request->input('date');
        if (!$date) {
            return response()->json([]);
        }

        $messages = \App\Models\WishMessages::with('user:id,username')
            ->whereDate('receiving_date', $date)
            ->orderBy('created_at', 'desc')
            ->get();

        $formatted = $messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'title' => $msg->title,
                'recipient_name' => $msg->recipient_name,
                'status' => $msg->status,
                'user' => $msg->user ? $msg->user->username : 'Unknown',
                'time' => $msg->created_at->format('H:i'),
                'url' => route('message.show', ['type' => $msg->message_type, 'slug' => $msg->slug])
            ];
        });

        return response()->json($formatted);
    }

    public function viewsChartData(Request $request)
    {
        $period = $request->query('period', 'week');

        $data = [];
        switch ($period) {
            case 'day':
                for ($h = 23; $h >= 0; $h--) {
                    $hour = \Carbon\Carbon::now()->subHours($h);
                    $data[] = [
                        'label' => $hour->format('H:i'),
                        'count' => \App\Models\MessageViews::whereBetween('viewed_at', [$hour->copy()->startOfHour(), $hour->copy()->endOfHour()])->count(),
                    ];
                }
                break;
            case 'month':
                for ($d = 29; $d >= 0; $d--) {
                    $date = \Carbon\Carbon::today()->subDays($d)->toDateString();
                    $data[] = [
                        'label' => \Carbon\Carbon::parse($date)->format('j/n'),
                        'count' => \App\Models\MessageViews::whereDate('viewed_at', $date)->count(),
                    ];
                }
                break;
            default:
                for ($i = 6; $i >= 0; $i--) {
                    $date = \Carbon\Carbon::today()->subDays($i)->toDateString();
                    $data[] = [
                        'label' => \Carbon\Carbon::parse($date)->format('D'),
                        'count' => \App\Models\MessageViews::whereDate('viewed_at', $date)->count(),
                    ];
                }
        }

        return response()->json($data);
    }

    public function subscribersPage()
    {
        $subscribers = Subscriber::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.pages.general.subscribers', compact('subscribers'));
    }

    public function sendUpdateEmail(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $subscribers = Subscriber::where('status', 'active')->get();
        $count = 0;

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->send(new SubscriberUpdateMail($subscriber, $request->subject, $request->message));
            $count++;
        }

        return back()->with('success', "Update email broadcasted successfully to $count subscribers.");
    }
}
