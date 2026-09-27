<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dcfms_case_id')
                ->constrained('dcfms_cases')
                ->cascadeOnDelete();

            $table->string('status');
            $table->string('action_taken');
            $table->text('remarks')->nullable();

            $table->string('division')->nullable();

            $table->foreignId('updated_by')
                ->constrained('users')
                ->cascadeOnUpdate();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_status_histories');
    }
};