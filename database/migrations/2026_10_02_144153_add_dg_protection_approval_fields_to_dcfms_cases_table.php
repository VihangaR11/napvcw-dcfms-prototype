<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dcfms_cases', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Interim Protection
            |--------------------------------------------------------------------------
            */

            $table->boolean('interim_protection_approved')
                ->default(false);

            $table->foreignId('interim_protection_approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('interim_protection_approved_at')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Post-Threat-Assessment Protection
            |--------------------------------------------------------------------------
            */

            $table->json('approved_protection_types')
                ->nullable();

            $table->foreignId('final_protection_approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('final_protection_approved_at')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('dcfms_cases', function (Blueprint $table) {

            $table->dropForeign([
                'interim_protection_approved_by'
            ]);

            $table->dropForeign([
                'final_protection_approved_by'
            ]);

            $table->dropColumn([
                'interim_protection_approved',
                'interim_protection_approved_by',
                'interim_protection_approved_at',
                'approved_protection_types',
                'final_protection_approved_by',
                'final_protection_approved_at',
            ]);
        });
    }
};