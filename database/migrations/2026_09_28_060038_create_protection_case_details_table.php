<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('protection_case_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dcfms_case_id')
                ->unique()
                ->constrained('dcfms_cases')
                ->cascadeOnDelete();

            $table->foreignId('protection_officer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('protection_status')
                ->default('Protection Request Received');

            $table->string('threat_type')->nullable();

            $table->date('threat_assessment_requested_date')->nullable();

            $table->string('police_reference')->nullable();

            $table->string('local_police_station')->nullable();

            $table->boolean('interim_protection_required')
                ->default(false);

            $table->date('interim_protection_requested_date')->nullable();

            $table->string('threat_assessment_status')
                ->default('Not Requested');

            $table->date('threat_assessment_received_date')->nullable();

            $table->string('threat_level')->nullable();

            $table->text('threat_assessment_summary')->nullable();

            $table->text('protection_decision')->nullable();

            $table->date('protection_start_date')->nullable();

            $table->date('review_date')->nullable();

            $table->string('protection_outcome')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('protection_case_details');
    }
};