<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('migration_batches', function (Blueprint $table) {
            $table->id();

            $table->string('batch_code')->unique();

            $table->string('source_division')
                ->default('Law and Law Enforcement');

            $table->string('source_dataset');

            $table->string('source_file');

            $table->string('status')
                ->default('pending');

            $table->unsignedInteger('total_records')
                ->default(0);

            $table->unsignedInteger('validated_records')
                ->default(0);

            $table->unsignedInteger('manual_review_records')
                ->default(0);

            $table->unsignedInteger('duplicate_records')
                ->default(0);

            $table->unsignedInteger('invalid_records')
                ->default(0);

            $table->unsignedInteger('imported_records')
                ->default(0);

            $table->unsignedInteger('failed_records')
                ->default(0);

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('validated_at')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index('source_dataset');

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('migration_batches');
    }
};