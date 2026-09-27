<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dcfms_cases', function (Blueprint $table) {
            $table->id();

            // Case identification
            $table->string('case_number')->unique();
            $table->date('received_date');

            // Complaint source
            $table->string('complaint_source');
            $table->string('complaint_mode')->nullable();

            // Party details
            $table->string('complainant_name')->nullable();
            $table->string('victim_witness_type')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();

            // Case details
            $table->text('complaint_summary');
            $table->string('complaint_category');
            $table->string('urgency')->default('Normal');

            // Workflow
            $table->string('current_status')->default('Registered');
            $table->string('primary_division')->nullable();

            // System information
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnUpdate();

            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            $table->index('case_number');
            $table->index('complaint_category');
            $table->index('current_status');
            $table->index('primary_division');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dcfms_cases');
    }
};
