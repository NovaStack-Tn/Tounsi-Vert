<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            // Change media_type to support multiple types
            $table->dropColumn('media_type');
        });
        
        Schema::table('blogs', function (Blueprint $table) {
            $table->json('images_paths')->nullable()->after('image_path');
            $table->boolean('has_images')->default(false)->after('images_paths');
            $table->boolean('has_video')->default(false)->after('has_images');
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['images_paths', 'has_images', 'has_video']);
            $table->enum('media_type', ['none', 'image', 'video'])->default('none');
        });
    }
};
