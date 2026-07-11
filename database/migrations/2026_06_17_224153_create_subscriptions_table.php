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

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'MTN Mobile Money', 'Visa', 'Bank of America'
            $table->string('type'); // 'momo', 'bank', 'card'
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 8, 2);
            $table->string('payment_method'); // 'paystack', 'manual_momo', 'manual_bank'
            $table->unsignedBigInteger('payment_method_id')->nullable(); // the ID from payment_methods table if manual
            $table->string('status')->default('pending'); // 'pending', 'active', 'rejected', 'expired'
            $table->string('reference_code')->nullable(); // Transaction ID or Paystack reference
            $table->string('proof_image')->nullable();
            $table->boolean('admin_granted')->default(false);
            $table->unsignedBigInteger('admin_granted_by')->nullable();
            $table->foreign('admin_granted_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('subscriptions');
    }
};
