<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!\Schema::hasTable('system_settings')) {
            return;
        }
        $now = now()->toDateTimeString();
        $defaults = [
            'github_repo_url'   => '',   // e.g. https://github.com/user/repo
            'github_clone_url'  => '',   // e.g. https://github.com/user/repo.git
            'github_branch'     => 'main',
            'github_pat'        => '',   // Personal Access Token (encrypted at rest in app)
        ];
        foreach ($defaults as $key => $value) {
            $exists = DB::table('system_settings')->where('key', $key)->exists();
            if (!$exists) {
                DB::table('system_settings')->insert([
                    'key'        => $key,
                    'value'      => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (!\Schema::hasTable('system_settings')) {
            return;
        }
        DB::table('system_settings')->whereIn('key', [
            'github_repo_url', 'github_clone_url', 'github_branch', 'github_pat',
        ])->delete();
    }
};
