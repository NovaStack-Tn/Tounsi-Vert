<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->enum('action_type', ['reviewed', 'investigating', 'resolved', 'dismissed', 'warning_sent', 'content_removed', 'account_suspended'])->default('reviewed');
            $table->text('action_note')->nullable();
            $table->text('internal_note')->nullable();
            $table->timestamp('action_taken_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['report_id']);
            $table->index(['admin_id']);
            $table->index(['action_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_actions');
    }
};
