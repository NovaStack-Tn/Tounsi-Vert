<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('event_category_id')->constrained('event_categories')->onDelete('cascade');
            $table->enum('type', ['online', 'onsite', 'hybrid']);
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->dateTime('start_at')->index();
            $table->dateTime('end_at')->nullable();
            $table->integer('max_participants')->nullable();
            $table->string('meeting_url')->nullable();
            $table->string('address')->nullable();
            $table->string('region', 120)->nullable()->index();
            $table->string('city', 120)->nullable()->index();
            $table->string('zipcode', 20)->nullable();
            $table->string('poster_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['region', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
