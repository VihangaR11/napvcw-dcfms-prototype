<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dcfms_cases', function (Blueprint $table) {

            $table->string('threat_assessment_status')
                ->nullable();

            $table->foreignId('threat_assessment_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('threat_assessment_at')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('dcfms_cases', function (Blueprint $table) {

            $table->dropForeign([
                'threat_assessment_by'
            ]);

            $table->dropColumn([
                'threat_assessment_status',
                'threat_assessment_by',
                'threat_assessment_at',
            ]);
        });
    }
};