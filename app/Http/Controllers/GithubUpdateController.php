<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;

class GithubUpdateController extends Controller
{
    /* -------------------------------------------------------------------------
     | HELPERS
     | ---------------------------------------------------------------------- */

    private function setting(string $key, $default = ''): string
    {
        return (string) (DB::table('system_settings')->where('key', $key)->value('value') ?? $default);
    }

    private function saveSetting(string $key, string $value): void
    {
        $exists = DB::table('system_settings')->where('key', $key)->exists();
        if ($exists) {
            DB::table('system_settings')->where('key', $key)->update(['value' => $value, 'updated_at' => now()]);
        } else {
            DB::table('system_settings')->insert(['key' => $key, 'value' => $value, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    /** Parse "owner/repo" from a GitHub URL like https://github.com/owner/repo(.git) */
    private function parseOwnerRepo(string $url): ?array
    {
        // Remove trailing .git
        $url = preg_replace('/\.git$/', '', rtrim($url, '/'));
        if (preg_match('#github\.com[:/]([^/]+)/([^/]+)$#i', $url, $m)) {
            return [$m[1], $m[2]];
        }
        return null;
    }

    /* -------------------------------------------------------------------------
     | 1. SAVE GITHUB SETTINGS (admin only)
     | ---------------------------------------------------------------------- */

    public function saveSettings(Request $request)
    {
        $request->validate([
            'repo_url'  => 'required|url',
            'clone_url' => 'required|url',
            'branch'    => 'required|string|max:100',
            'pat'       => 'nullable|string|max:255',
        ]);

        $this->saveSetting('github_repo_url',  $request->input('repo_url'));
        $this->saveSetting('github_clone_url', $request->input('clone_url'));
        $this->saveSetting('github_branch',    $request->input('branch'));
        if ($request->filled('pat')) {
            $this->saveSetting('github_pat', $request->input('pat'));
        }

        return response()->json(['success' => true, 'message' => 'GitHub settings saved.']);
    }

    /* -------------------------------------------------------------------------
     | 2. CHECK FOR UPDATES (admin)
     | ---------------------------------------------------------------------- */

    public function checkUpdate()
    {
        $repoUrl = $this->setting('github_repo_url');
        $branch  = $this->setting('github_branch', 'main');
        $pat     = $this->setting('github_pat');

        if (!$repoUrl) {
            return response()->json(['error' => 'No GitHub repository configured. Click the + button to set one up.'], 422);
        }

        $parsed = $this->parseOwnerRepo($repoUrl);
        if (!$parsed) {
            return response()->json(['error' => 'Invalid GitHub URL format. Expected: https://github.com/owner/repo'], 422);
        }

        [$owner, $repo] = $parsed;

        // --- Get latest remote commit SHA via GitHub API ---
        $apiUrl  = "https://api.github.com/repos/{$owner}/{$repo}/commits/{$branch}";
        $headers = [
            'Accept'     => 'application/vnd.github+json',
            'User-Agent' => 'WISP-App/1.0',
        ];
        if ($pat) {
            $headers['Authorization'] = "Bearer {$pat}";
        }

        try {
            $response = Http::withHeaders($headers)->timeout(10)->get($apiUrl);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not reach GitHub API: ' . $e->getMessage()], 502);
        }

        if ($response->status() === 401) {
            return response()->json(['error' => 'GitHub authentication failed. Check your Personal Access Token.'], 401);
        }
        if ($response->status() === 404) {
            return response()->json(['error' => "Repository '{$owner}/{$repo}' not found or branch '{$branch}' does not exist."], 404);
        }
        if (!$response->successful()) {
            return response()->json(['error' => 'GitHub API error: ' . $response->body()], $response->status());
        }

        $latestSha = $response->json('sha');
        $latestMsg = $response->json('commit.message');
        $latestDate = $response->json('commit.committer.date');

        // --- Get local commit SHA ---
        $projectRoot = base_path();
        $localSha = null;
        if (function_exists('exec')) {
            exec("cd " . escapeshellarg($projectRoot) . " && git rev-parse HEAD 2>&1", $out, $code);
            if ($code === 0 && !empty($out[0])) {
                $localSha = trim($out[0]);
            }
        }

        $upToDate = $localSha && $latestSha && str_starts_with($latestSha, $localSha) || ($localSha === $latestSha);

        return response()->json([
            'up_to_date'  => $upToDate,
            'latest_sha'  => $latestSha ? substr($latestSha, 0, 8) : null,
            'local_sha'   => $localSha  ? substr($localSha,  0, 8) : null,
            'latest_msg'  => $latestMsg,
            'latest_date' => $latestDate,
            'repo'        => "{$owner}/{$repo}",
            'branch'      => $branch,
        ]);
    }

    /* -------------------------------------------------------------------------
     | 3. RUN UPDATE (admin + user)
     | ---------------------------------------------------------------------- */

    public function runUpdate(Request $request)
    {
        return $this->doUpdate($request, adminOnly: true);
    }

    public function userUpdate(Request $request)
    {
        // Only allowed when admin has enabled system updates
        $enabled = DB::table('system_settings')->where('key', 'enable_system_updates')->value('value') === '1';
        if (!$enabled) {
            return response()->json(['error' => 'System updates are disabled by the administrator.'], 403);
        }
        return $this->doUpdate($request, adminOnly: false);
    }

    private function doUpdate(Request $request, bool $adminOnly): \Illuminate\Http\JsonResponse
    {
        if (!function_exists('exec')) {
            return response()->json([
                'error' => 'exec() is disabled on this server. Enable it in php.ini to allow git pull.',
            ], 500);
        }

        $cloneUrl = $this->setting('github_clone_url');
        $branch   = $this->setting('github_branch', 'main');
        $pat      = $this->setting('github_pat');

        if (!$cloneUrl) {
            return response()->json(['error' => 'No GitHub clone URL configured.'], 422);
        }

        // Inject PAT into clone URL for authentication: https://TOKEN@github.com/user/repo.git
        $authenticatedUrl = $cloneUrl;
        if ($pat) {
            $authenticatedUrl = preg_replace('#^https://#', "https://{$pat}@", $cloneUrl);
        }

        $projectRoot = base_path();
        $log         = [];

        // Ensure we're in a git repo
        exec("cd " . escapeshellarg($projectRoot) . " && git status 2>&1", $statusOut, $statusCode);
        if ($statusCode !== 0) {
            // Not a git repo — try to initialise and add remote
            $log[] = '⚙ Initialising git repository...';
            exec("cd " . escapeshellarg($projectRoot) . " && git init 2>&1", $o, $c);
            $log = array_merge($log, $o);
            exec("cd " . escapeshellarg($projectRoot) . " && git remote add origin " . escapeshellarg($authenticatedUrl) . " 2>&1", $o, $c);
            $log = array_merge($log, $o);
        }

        // Fetch latest from remote
        $log[] = '⬇ Fetching from GitHub...';
        exec("cd " . escapeshellarg($projectRoot) . " && git fetch " . escapeshellarg($authenticatedUrl) . " " . escapeshellarg($branch) . " 2>&1", $fetchOut, $fetchCode);
        $log = array_merge($log, $fetchOut);

        if ($fetchCode !== 0) {
            return response()->json([
                'error' => 'git fetch failed. Check your clone URL, PAT, and network.',
                'log'   => $log,
            ], 500);
        }

        // Pull (merge)
        $log[] = '🔄 Pulling changes...';
        exec("cd " . escapeshellarg($projectRoot) . " && git pull " . escapeshellarg($authenticatedUrl) . " " . escapeshellarg($branch) . " 2>&1", $pullOut, $pullCode);
        $log = array_merge($log, $pullOut);

        if ($pullCode !== 0) {
            return response()->json([
                'error' => 'git pull failed. There may be local conflicts.',
                'log'   => $log,
            ], 500);
        }

        // Clear caches
        $log[] = '🧹 Clearing caches...';
        try {
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            $log[] = '✅ Caches cleared.';
        } catch (\Exception $e) {
            $log[] = '⚠ Cache clear warning: ' . $e->getMessage();
        }

        // Get new local SHA
        $newSha = null;
        exec("cd " . escapeshellarg($projectRoot) . " && git rev-parse HEAD 2>&1", $shaOut, $shaCode);
        if ($shaCode === 0 && !empty($shaOut[0])) {
            $newSha = substr(trim($shaOut[0]), 0, 8);
        }

        $log[] = '✅ Update complete. Running on commit: ' . ($newSha ?? 'unknown');

        return response()->json([
            'success'    => true,
            'message'    => 'Update applied successfully.',
            'new_commit' => $newSha,
            'log'        => $log,
        ]);
    }
}
