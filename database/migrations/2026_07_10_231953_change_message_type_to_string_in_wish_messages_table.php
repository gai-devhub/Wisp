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
        Schema::table('wish_messages', function (Blueprint $table) {
            $table->string('message_type', 30)->default('Birthday Message')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wish_messages', function (Blueprint $table) {
            // Reverting to enum requires redefining all values, which can be problematic in some DB engines.
            // For now, we will just leave it as string or change the length back if needed.
        });
    }
};
