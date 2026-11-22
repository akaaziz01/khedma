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
        Schema::create('job_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number', 32)->unique();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('category');
            $table->text('description');
            $table->decimal('budget', 10, 2)->nullable();
            $table->string('city');
            $table->string('address')->nullable();
            $table->enum('urgency', ['low', 'medium', 'urgent', 'emergency'])->default('medium');
            $table->dateTime('preferred_date')->nullable();
            $table->enum('status', ['open', 'in_review', 'awarded', 'completed', 'cancelled'])->default('open');
            $table->unsignedBigInteger('accepted_proposal_id')->nullable();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status']);
            $table->index(['category', 'city', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_requests');
    }
};
