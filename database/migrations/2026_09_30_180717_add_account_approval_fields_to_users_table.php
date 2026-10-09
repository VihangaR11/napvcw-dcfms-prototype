<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('account_status')
                ->default('active')
                ->after('is_active');

            $table->foreignId('approved_by')
                ->nullable()
                ->after('account_status')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable()
                ->after('approved_by');

            $table->timestamp('rejected_at')
                ->nullable()
                ->after('approved_at');

            $table->text('rejection_reason')
                ->nullable()
                ->after('rejected_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropForeign([
                'approved_by'
            ]);

            $table->dropColumn([
                'account_status',
                'approved_by',
                'approved_at',
                'rejected_at',
                'rejection_reason',
            ]);
        });
    }
};