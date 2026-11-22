<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->text('declined_reason')->nullable()->after('provider_notes');
            $table->timestamp('accepted_at')->nullable()->after('completed_at');
            $table->timestamp('declined_at')->nullable()->after('accepted_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['declined_reason', 'accepted_at', 'declined_at']);
        });
    }
};
