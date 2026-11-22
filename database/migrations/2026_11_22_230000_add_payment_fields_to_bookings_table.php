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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status'); // unpaid, pending, paid, refunded, failed
            $table->string('payment_method')->nullable()->after('payment_status'); // stripe, cash, etc.
            $table->string('stripe_session_id')->nullable()->after('payment_method');
            $table->string('stripe_payment_intent_id')->nullable()->after('stripe_session_id');
            $table->dateTime('paid_at')->nullable()->after('stripe_payment_intent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_method',
                'stripe_session_id',
                'stripe_payment_intent_id',
                'paid_at',
            ]);
        });
    }
};
