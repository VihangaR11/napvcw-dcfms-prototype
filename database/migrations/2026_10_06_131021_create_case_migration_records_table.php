<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_migration_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dcfms_case_id')
                ->constrained('dcfms_cases')
                ->cascadeOnDelete();

            $table->foreignId('migration_batch_id')
                ->nullable()
                ->constrained('migration_batches')
                ->nullOnDelete();

            $table->foreignId('staging_case_import_id')
                ->nullable()
                ->constrained('staging_case_imports')
                ->nullOnDelete();

            $table->string('source_dataset');

            $table->string('source_file');

            $table->string('source_sheet')->nullable();

            $table->unsignedInteger('source_row_number')->nullable();

            $table->string('legacy_case_number')->nullable();

            $table->text('legacy_status_raw')->nullable();

            $table->text('migration_notes')->nullable();

            $table->foreignId('migrated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('migrated_at');

            $table->timestamps();

            $table->index('legacy_case_number');

            $table->index('source_dataset');

            $table->index('migrated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_migration_records');
    }
};