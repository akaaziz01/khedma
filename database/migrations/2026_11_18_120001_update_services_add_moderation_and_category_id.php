<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Moderation state (admin-controlled)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('is_active');
            $table->text('rejection_reason')->nullable()->after('status');
            $table->timestamp('moderated_at')->nullable()->after('rejection_reason');
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete()->after('moderated_at');

            // Link to the canonical categories table
            $table->foreignId('category_id')->nullable()->constrained('service_categories')->nullOnDelete()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('moderated_by');
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn(['status', 'rejection_reason', 'moderated_at']);
        });
    }
};

