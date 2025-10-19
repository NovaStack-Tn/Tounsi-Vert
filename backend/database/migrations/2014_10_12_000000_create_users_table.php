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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('region', 120)->nullable()->index();
            $table->string('city', 120)->nullable()->index();
            $table->string('zipcode', 20)->nullable();
            $table->string('email', 191)->unique();
            $table->string('password');
            $table->string('address')->nullable();
            $table->string('origin', 120)->nullable();
            $table->string('phone_number', 30)->nullable();
            $table->integer('score')->default(0)->index();
            $table->enum('role', ['member', 'organizer', 'admin'])->default('member');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
