<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wish_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('message_type', 30)->default('Birthday Message');
            $table->string('title');
            $table->string('recipient_special_name');
            $table->string('recipient_name');
            $table->string('greeting')->default('Hello There');
            $table->text('message');
            $table->string('last_note');
            $table->date('receiving_date');
            $table->string('sender_name');
            $table->string('recipient_phone')->nullable();
            $table->string('slug')->unique();
            // $table->unsignedBigInteger('template_id')->nullable();
            $table->integer('expiry_hours')->default(24);
            $table->timestamp('expires_at');
            $table->boolean('is_published')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_vaulted')->default(false);
            $table->string('specific_vault_pin')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        // Drop templates first (it has a foreign key to wish_messages)
        Schema::dropIfExists('templates');
        Schema::dropIfExists('wish_messages');
    }
};