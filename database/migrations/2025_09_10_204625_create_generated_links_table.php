<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('generated_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wish_message_id')->constrained()->onDelete('cascade');
            $table->string('generated_url'); // Changed from 'link' to match your controller
            $table->string('unique_code')->unique(); // Added for the unique identifier
            $table->integer('view_count')->default(0); // Added to track views
            $table->boolean('is_active')->default(true);
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('generated_links');
    }
};