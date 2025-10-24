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
            if (!$this->indexExists('events', 'idx_event_category')) {
                $table->index(['event_category_id'], 'idx_event_category');
            }
            if (!$this->indexExists('events', 'idx_event_location')) {
                $table->index(['region', 'city'], 'idx_event_location');
            }
            if (!$this->indexExists('events', 'idx_event_dates')) {
                $table->index(['start_at', 'end_at'], 'idx_event_dates');
            }
            if (!$this->indexExists('events', 'idx_event_status')) {
                $table->index(['is_published', 'deleted_at'], 'idx_event_status');
            }
            if (!$this->indexExists('events', 'idx_event_organization')) {
                $table->index(['organization_id'], 'idx_event_organization');
            }
        });

        // Add indexes to organizations table for better query performance
        Schema::table('organizations', function (Blueprint $table) {
            if (!$this->indexExists('organizations', 'idx_org_category')) {
                $table->index(['org_category_id'], 'idx_org_category');
            }
            if (!$this->indexExists('organizations', 'idx_org_location')) {
                $table->index(['region', 'city'], 'idx_org_location');
            }
            if (!$this->indexExists('organizations', 'idx_org_status')) {
                $table->index(['is_verified', 'is_blocked'], 'idx_org_status');
            }
            if (!$this->indexExists('organizations', 'idx_org_owner')) {
                $table->index(['owner_id'], 'idx_org_owner');
            }
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();
        
        $result = $connection->select(
            "SELECT COUNT(*) as count FROM information_schema.statistics 
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$database, $table, $index]
        );
        
        return $result[0]->count > 0;
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
