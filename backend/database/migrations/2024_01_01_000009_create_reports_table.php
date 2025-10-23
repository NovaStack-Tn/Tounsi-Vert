<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('cascade');
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('cascade');
            $table->string('reason', 200)->nullable();
            $table->text('details')->nullable();
            $table->enum('status', ['open', 'in_review', 'resolved', 'dismissed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('category', ['spam', 'inappropriate', 'fraud', 'harassment', 'violence', 'misinformation', 'copyright', 'other'])->default('other');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['status']);
            $table->index(['priority']);
            $table->index(['category']);
            $table->index(['organization_id']);
            $table->index(['event_id']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
