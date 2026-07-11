<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('notification_broadcast_id')->nullable()->constrained('notification_broadcasts')->nullOnDelete();

            $table->unsignedBigInteger('sender_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('root_id')->nullable();
            $table->string('recipient_group')->nullable();
            $table->boolean('is_reply')->default(false);
            $table->timestamp('replied_at')->nullable();

            $table->string('type', 20); // info, success, warning, error
            $table->string('title');
            $table->text('message');
            $table->string('context', 100)->nullable(); // e.g. media.store, messages.store
            $table->json('meta')->nullable(); // action, related_id, errors, etc.
            
            // Add index for performance
            $table->index('sender_id');
            $table->index('parent_id');
            $table->index('root_id');

            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_notifications');
    }
};
