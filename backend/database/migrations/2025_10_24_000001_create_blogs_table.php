<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('image_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('ai_assisted')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id']);
            $table->index(['is_published']);
            $table->index(['created_at']);
            $table->index(['views_count']);
            $table->index(['likes_count']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
