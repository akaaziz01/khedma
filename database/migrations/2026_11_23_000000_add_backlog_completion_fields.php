<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dateTime('client_confirmed_at')->nullable()->after('completed_at');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->string('status', 20)->default('approved')->after('comment');
            $table->text('moderation_reason')->nullable()->after('status');
            $table->foreignId('moderated_by')->nullable()->after('moderation_reason')
                ->constrained('users')->nullOnDelete();
            $table->dateTime('moderated_at')->nullable()->after('moderated_by');
        });

        Schema::table('incident_reports', function (Blueprint $table) {
            $table->foreignId('reported_provider_id')->nullable()->after('reported_client_id')
                ->constrained('users')->nullOnDelete();
        });

    }

    public function down(): void
    {
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reported_provider_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('moderated_by');
            $table->dropColumn(['status', 'moderation_reason', 'moderated_at']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('client_confirmed_at');
        });
    }
};
