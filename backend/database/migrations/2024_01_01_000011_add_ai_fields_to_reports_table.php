<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->integer('ai_risk_score')->default(0)->after('priority');
            $table->string('ai_suggested_category')->nullable()->after('ai_risk_score');
            $table->decimal('ai_confidence', 5, 2)->default(0)->after('ai_suggested_category');
            $table->boolean('ai_auto_flagged')->default(false)->after('ai_confidence');
            $table->json('ai_analysis')->nullable()->after('ai_auto_flagged');
            
            $table->index('ai_risk_score');
            $table->index('ai_auto_flagged');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex(['ai_risk_score']);
            $table->dropIndex(['ai_auto_flagged']);
            
            $table->dropColumn([
                'ai_risk_score',
                'ai_suggested_category',
                'ai_confidence',
                'ai_auto_flagged',
                'ai_analysis',
            ]);
        });
    }
};
