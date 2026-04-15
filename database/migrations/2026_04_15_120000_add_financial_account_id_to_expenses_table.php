<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'financial_account_id')) {
                $table->unsignedBigInteger('financial_account_id')
                    ->nullable()
                    ->after('expense_category_id');
            }
        });

        $defaultAccountId = DB::table('financial_accounts')
            ->where('slug', 'caixa-principal')
            ->value('id');

        if ($defaultAccountId) {
            DB::table('expenses')
                ->whereNull('financial_account_id')
                ->update(['financial_account_id' => $defaultAccountId]);
        }

        $databaseName = DB::connection()->getDatabaseName();
        $foreignExists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $databaseName)
            ->where('TABLE_NAME', 'expenses')
            ->where('CONSTRAINT_NAME', 'expenses_financial_account_id_foreign')
            ->exists();

        $indexExists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $databaseName)
            ->where('TABLE_NAME', 'expenses')
            ->where('INDEX_NAME', 'expenses_financial_account_id_index')
            ->exists();

        Schema::table('expenses', function (Blueprint $table) use ($indexExists, $foreignExists) {
            if (!$indexExists) {
                $table->index('financial_account_id');
            }

            if (!$foreignExists) {
                $table->foreign('financial_account_id')
                    ->references('id')
                    ->on('financial_accounts')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        $databaseName = DB::connection()->getDatabaseName();
        $foreignExists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $databaseName)
            ->where('TABLE_NAME', 'expenses')
            ->where('CONSTRAINT_NAME', 'expenses_financial_account_id_foreign')
            ->exists();

        $indexExists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $databaseName)
            ->where('TABLE_NAME', 'expenses')
            ->where('INDEX_NAME', 'expenses_financial_account_id_index')
            ->exists();

        Schema::table('expenses', function (Blueprint $table) use ($indexExists, $foreignExists) {
            if ($foreignExists) {
                $table->dropForeign('expenses_financial_account_id_foreign');
            }

            if ($indexExists) {
                $table->dropIndex('expenses_financial_account_id_index');
            }

            if (Schema::hasColumn('expenses', 'financial_account_id')) {
                $table->dropColumn('financial_account_id');
            }
        });
    }
};
