<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('title', 60);
            $table->string('url');
            $table->timestamps();
            
            $table->unique(['organization_id', 'title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_social_links');
    }
};
