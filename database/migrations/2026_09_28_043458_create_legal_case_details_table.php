<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_case_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dcfms_case_id')
                ->unique()
                ->constrained('dcfms_cases')
                ->cascadeOnDelete();

            $table->string('re_number')->nullable();

            $table->foreignId('legal_officer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('investigation_officer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('legal_status')
                ->default('Received by Legal Division');

            $table->date('inquiry_started_date')->nullable();

            $table->date('observation_requested_date')->nullable();
            $table->date('observation_due_date')->nullable();

            $table->date('first_reminder_date')->nullable();
            $table->date('second_reminder_date')->nullable();

            $table->boolean('field_visit_required')
                ->default(false);

            $table->date('field_visit_date')->nullable();

            $table->text('io_findings')->nullable();
            $table->text('legal_recommendation')->nullable();

            $table->boolean('case_conference_required')
                ->default(false);

            $table->date('case_conference_date')->nullable();

            $table->string('case_conference_outcome')->nullable();

            $table->boolean('board_submission_required')
                ->default(false);

            $table->date('board_submission_date')->nullable();

            $table->text('board_decision')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_case_details');
    }
};