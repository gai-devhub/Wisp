<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('user_notifications', 'deleted_by_user')) {
            Schema::table('user_notifications', function (Blueprint $table) {
                $table->boolean('deleted_by_user')->default(false)->after('read_at');
            });
        }
        if (!Schema::hasColumn('user_notifications', 'deleted_by_admin')) {
            Schema::table('user_notifications', function (Blueprint $table) {
                $table->boolean('deleted_by_admin')->default(false)->after('deleted_by_user');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropColumn(['deleted_by_user', 'deleted_by_admin']);
        });
    }
};
