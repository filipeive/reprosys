<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->boolean('include_in_metrics')->default(true)->after('reversal_of');
        });

        // Set default to false for existing adjustments
        DB::table('financial_transactions')
            ->whereIn('type', ['cash_adjustment_in', 'cash_adjustment_out'])
            ->update(['include_in_metrics' => false]);
    }

    public function down(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropColumn('include_in_metrics');
        });
    }
};
