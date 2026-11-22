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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->string('proposal_number', 32)->unique();
            $table->foreignId('job_request_id')->constrained('job_requests')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->string('delivery_time', 100);
            $table->unsignedInteger('delivery_days')->nullable();
            $table->decimal('estimated_duration_hours', 5, 2)->nullable();
            $table->text('message');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->unique(['job_request_id', 'provider_id']);
            $table->index(['provider_id', 'status']);
            $table->index(['job_request_id', 'price']);
            $table->index(['job_request_id', 'delivery_days']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
