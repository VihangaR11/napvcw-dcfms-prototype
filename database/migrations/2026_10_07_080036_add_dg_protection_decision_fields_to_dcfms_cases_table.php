<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dcfms_cases', function (Blueprint $table) {
            $table->string('dg_protection_type')
                ->nullable()
                ->after('threat_assessment_at');

            $table->foreignId('dg_protection_decided_by')
                ->nullable()
                ->after('dg_protection_type')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('dg_protection_decided_at')
                ->nullable()
                ->after('dg_protection_decided_by');

            $table->index('dg_protection_type');
            $table->index('dg_protection_decided_at');
        });
    }

    public function down(): void
    {
        Schema::table('dcfms_cases', function (Blueprint $table) {
            $table->dropForeign([
                'dg_protection_decided_by',
            ]);

            $table->dropIndex([
                'dg_protection_type',
            ]);

            $table->dropIndex([
                'dg_protection_decided_at',
            ]);

            $table->dropColumn([
                'dg_protection_type',
                'dg_protection_decided_by',
                'dg_protection_decided_at',
            ]);
        });
    }
};
