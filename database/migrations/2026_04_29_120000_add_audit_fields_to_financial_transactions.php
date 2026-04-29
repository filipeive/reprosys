<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            // Status field: confirmed, pending, cancelled, reversed
            if (!Schema::hasColumn('financial_transactions', 'status')) {
                $table->string('status', 20)->default('confirmed')->after('notes');
            }

            // Balance snapshot at time of transaction for audit trail
            if (!Schema::hasColumn('financial_transactions', 'balance_after')) {
                $table->decimal('balance_after', 15, 2)->nullable()->after('status');
            }

            // Soft deletes for immutability (never truly delete)
            if (!Schema::hasColumn('financial_transactions', 'deleted_at')) {
                $table->softDeletes();
            }

            // Reversal reference: links a reversal transaction to the original
            if (!Schema::hasColumn('financial_transactions', 'reversed_by')) {
                $table->unsignedBigInteger('reversed_by')->nullable()->after('balance_after');
            }
            if (!Schema::hasColumn('financial_transactions', 'reversal_of')) {
                $table->unsignedBigInteger('reversal_of')->nullable()->after('reversed_by');
            }
        });

        // Add composite index for common query patterns
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->index(['financial_account_id', 'direction', 'status'], 'ft_account_direction_status');
            $table->index(['transaction_date', 'direction', 'status'], 'ft_date_direction_status');
        });
    }

    public function down(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropIndex('ft_account_direction_status');
            $table->dropIndex('ft_date_direction_status');

            $columns = ['status', 'balance_after', 'deleted_at', 'reversed_by', 'reversal_of'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('financial_transactions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
