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
    Schema::create('vehicules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('owner_id')->nullable();
        $table->string('type');
        $table->text('description')->nullable();
        $table->string('capacity');
        $table->dateTime('availability_start');
        $table->dateTime('availability_end');
        $table->string('location');
        $table->string('status')->default('Active');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicules');
    }
};
