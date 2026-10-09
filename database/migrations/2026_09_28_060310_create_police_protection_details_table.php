<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('police_protection_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dcfms_case_id')
                ->unique()
                ->constrained('dcfms_cases')
                ->cascadeOnDelete();

            $table->foreignId('police_protection_officer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('assessment_status')
                ->default('Awaiting Request');

            $table->date('request_received_date')->nullable();

            $table->date('assessment_started_date')->nullable();

            $table->date('assessment_completed_date')->nullable();

            $table->string('threat_level')->nullable();

            $table->text('assessment_summary')->nullable();

            $table->text('recommendation')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('police_protection_details');
    }
};