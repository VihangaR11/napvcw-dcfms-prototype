<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistance_case_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dcfms_case_id')
                ->unique()
                ->constrained('dcfms_cases')
                ->cascadeOnDelete();

            $table->string('assistance_status')
                ->default('Assistance Request Received');

            $table->string('assistance_type')->nullable();

            $table->boolean('referral_required')
                ->default(false);

            $table->string('referred_to')->nullable();

            $table->date('referral_date')->nullable();

            $table->text('current_action')->nullable();

            $table->date('follow_up_date')->nullable();

            $table->text('assistance_outcome')->nullable();

            $table->date('completed_date')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistance_case_details');
    }
};