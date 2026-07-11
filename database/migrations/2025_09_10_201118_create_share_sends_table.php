<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('share_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wish_message_id')->constrained('wish_messages')->onDelete('cascade');
            $table->string('channel', 20); // email, sms
            $table->string('recipient_hash', 64); // hashed email or phone
            $table->string('recipient_masked', 64); // e.g. ***x@***.com or ***1234
            $table->text('recipient_contact')->nullable(); // actual email or phone (encrypted, needs to be text or long string)
            $table->text('custom_message')->nullable(); // custom message override
            $table->string('status', 20)->default('sending'); // sending, sent, failed
            $table->timestamp('scheduled_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_sends');
    }
};
