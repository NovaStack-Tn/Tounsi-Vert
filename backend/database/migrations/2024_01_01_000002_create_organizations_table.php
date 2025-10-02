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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('org_category_id')->constrained('org_categories')->onDelete('cascade');
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('region', 120)->nullable()->index();
            $table->string('city', 120)->nullable()->index();
            $table->string('zipcode', 20)->nullable();
            $table->string('phone_number', 30)->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
