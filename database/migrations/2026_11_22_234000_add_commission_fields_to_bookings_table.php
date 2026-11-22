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
            $table->decimal('commission_rate', 5, 2)->default(10.00)->after('total_price');
            $table->decimal('commission_amount', 10, 2)->default(0.00)->after('commission_rate');
            $table->decimal('provider_amount', 10, 2)->default(0.00)->after('commission_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'commission_rate',
                'commission_amount',
                'provider_amount',
            ]);
        });
    }
};
