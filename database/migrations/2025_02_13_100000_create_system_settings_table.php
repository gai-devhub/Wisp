<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->string('key', 64)->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $now = now()->toDateTimeString();
        \DB::table('system_settings')->insert([
            ['key' => 'app_locked', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'registration_disabled', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'last_backup_at', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
