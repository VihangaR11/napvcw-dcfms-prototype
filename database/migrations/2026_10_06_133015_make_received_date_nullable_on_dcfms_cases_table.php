<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dcfms_cases', function (Blueprint $table) {
            $table->date('received_date')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('dcfms_cases', function (Blueprint $table) {
            $table->date('received_date')
                ->nullable(false)
                ->change();
        });
    }
};