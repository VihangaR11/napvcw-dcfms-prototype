<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staging_case_imports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('migration_batch_id')
                ->constrained('migration_batches')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Source Provenance
            |--------------------------------------------------------------------------
            */

            $table->string('source_dataset');

            $table->string('source_file');

            $table->string('source_sheet')->nullable();

            $table->unsignedInteger('source_row_number')->nullable();

            $table->string('legacy_case_number')->nullable();

            $table->unsignedSmallInteger('legacy_year')->nullable();


            /*
            |--------------------------------------------------------------------------
            | Raw Source Values
            |--------------------------------------------------------------------------
            */

            $table->text('received_date_raw')->nullable();

            $table->text('complainant_name_raw')->nullable();

            $table->text('complainant_address_raw')->nullable();

            $table->text('district_raw')->nullable();

            $table->text('province_raw')->nullable();

            $table->text('gender_raw')->nullable();

            $table->text('legacy_status_raw')->nullable();

            $table->text('closing_date_raw')->nullable();

            $table->text('assistance_type_raw')->nullable();

            $table->text('assistance_required_raw')->nullable();

            $table->text('assistance_provided_raw')->nullable();

            $table->text('referral_details_raw')->nullable();

            $table->text('follow_up_actions_raw')->nullable();

            $table->text('court_reference_raw')->nullable();

            $table->text('handling_officer_raw')->nullable();


            /*
            |--------------------------------------------------------------------------
            | Normalized Values
            |--------------------------------------------------------------------------
            */

            $table->date('received_date_normalized')->nullable();

            $table->string('complainant_name_normalized')->nullable();

            $table->string('district_normalized')->nullable();

            $table->string('province_normalized')->nullable();

            $table->string('gender_normalized')->nullable();

            $table->string('complaint_category_normalized')->nullable();

            $table->string('primary_division_normalized')->nullable();

            $table->string('legal_status_normalized')->nullable();

            $table->string('assistance_status_normalized')->nullable();

            $table->string('assistance_type_normalized')->nullable();

            $table->date('closing_date_normalized')->nullable();


            /*
            |--------------------------------------------------------------------------
            | Validation / Review
            |--------------------------------------------------------------------------
            */

            $table->string('migration_status')
                ->default('pending');

            $table->text('validation_notes')->nullable();

            $table->boolean('requires_manual_review')
                ->default(false);

            $table->boolean('is_duplicate')
                ->default(false);

            $table->boolean('is_valid')
                ->default(false);


            /*
            |--------------------------------------------------------------------------
            | Operational Migration Result
            |--------------------------------------------------------------------------
            */

            $table->foreignId('dcfms_case_id')
                ->nullable()
                ->constrained('dcfms_cases')
                ->nullOnDelete();

            $table->foreignId('migrated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('migrated_at')->nullable();

            $table->text('migration_error')->nullable();

            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('legacy_case_number');

            $table->index('source_dataset');

            $table->index('source_sheet');

            $table->index('migration_status');

            $table->index('requires_manual_review');

            $table->index('is_duplicate');

            $table->index([
                'migration_batch_id',
                'migration_status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staging_case_imports');
    }
};