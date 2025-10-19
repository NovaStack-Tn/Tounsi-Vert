<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->tinyInteger('rate')->unsigned();
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['user_id', 'event_id']);
            $table->index(['event_id']);
            $table->index(['rate']);
        });
        
        DB::statement('ALTER TABLE reviews ADD CONSTRAINT reviews_rate_check CHECK (rate BETWEEN 1 AND 5)');
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
