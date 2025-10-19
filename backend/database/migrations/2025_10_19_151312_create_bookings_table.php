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
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('requester_id');
        $table->unsignedBigInteger('owner_id');
        $table->string('pickup_location');
        $table->string('dropoff_location');
        $table->dateTime('scheduled_time')->nullable();
        $table->enum('status', ['pending', 'accepted', 'completed', 'cancelled'])->default('pending');
        $table->unsignedBigInteger('vehicule_id');
        $table->text('notes')->nullable();
        $table->timestamps();

        $table->foreign('vehicule_id')->references('id')->on('vehicules')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
