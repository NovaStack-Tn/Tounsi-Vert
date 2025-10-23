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
        // Add indexes to events table for better query performance
        Schema::table('events', function (Blueprint $table) {
            $table->index(['event_category_id'], 'idx_event_category');
            $table->index(['region', 'city'], 'idx_event_location');
            $table->index(['start_at', 'end_at'], 'idx_event_dates');
            $table->index(['is_published', 'deleted_at'], 'idx_event_status');
            $table->index(['organization_id'], 'idx_event_organization');
        });

        // Add indexes to organizations table for better query performance
        Schema::table('organizations', function (Blueprint $table) {
            $table->index(['org_category_id'], 'idx_org_category');
            $table->index(['region', 'city'], 'idx_org_location');
            $table->index(['is_verified', 'is_blocked'], 'idx_org_status');
            $table->index(['user_id'], 'idx_org_owner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('idx_event_category');
            $table->dropIndex('idx_event_location');
            $table->dropIndex('idx_event_dates');
            $table->dropIndex('idx_event_status');
            $table->dropIndex('idx_event_organization');
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->dropIndex('idx_org_category');
            $table->dropIndex('idx_org_location');
            $table->dropIndex('idx_org_status');
            $table->dropIndex('idx_org_owner');
        });
    }
};
