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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('travel_policy_id')->nullable()->constrained('travel_policies')->nullOnDelete();
            $table->enum('modal', ['hotel', 'flight', 'bus', 'car']);
            $table->string('destination_city', 255);
            $table->string('destination_state', 2);
            $table->string('provider_name', 255);
            $table->decimal('original_price', 10, 2);
            $table->decimal('paid_price', 10, 2);
            $table->decimal('savings_total', 10, 2);
            $table->decimal('onhappy_coins_amount', 10, 2);
            $table->decimal('company_savings', 10, 2);
            $table->date('check_in');
            $table->date('check_out')->nullable();
            $table->enum('status', ['confirmed', 'cancelled'])->default('confirmed');
            $table->timestamps();
        });

        // Add FK from wallet_transactions to bookings after bookings is created
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->foreign('booking_id')->references('id')->on('bookings')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
        });
        Schema::dropIfExists('bookings');
    }
};
