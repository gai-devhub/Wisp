<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('page_expiry')->default(24);
            $table->boolean('page_view_alerts')->default(true);
            $table->boolean('whatsapp_notifications')->default(false);
            $table->string('auto_delete_expired', 50)->default('never');
            $table->string('vault_pin')->nullable();
            $table->integer('auto_archive_days')->nullable();
            $table->boolean('privacy_blur_enabled')->default(false);
            $table->string('theme_preference')->default('theme-default');
            $table->boolean('theme_bg_enabled')->default(true);
            $table->string('login_passcode')->nullable();
            $table->timestamps();
        });

        // Convert old boolean values to new string values
        \Illuminate\Support\Facades\DB::table('user_settings')
            ->where('auto_delete_expired', '1')
            ->update(['auto_delete_expired' => '1_week']);
            
        \Illuminate\Support\Facades\DB::table('user_settings')
            ->where('auto_delete_expired', '0')
            ->update(['auto_delete_expired' => 'never']);
    }

    public function down()
    {
        Schema::dropIfExists('user_settings');
    }
};