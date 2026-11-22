<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('provider_profiles', function (Blueprint $table) {
            $table->index('category');
            $table->index('city');
            $table->index('is_available');
            $table->index('hourly_rate');
        });
    }

   
    public function down(): void
    {
        Schema::table('provider_profiles', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['city']);
            $table->dropIndex(['is_available']);
            $table->dropIndex(['hourly_rate']);
        });
    }
};
