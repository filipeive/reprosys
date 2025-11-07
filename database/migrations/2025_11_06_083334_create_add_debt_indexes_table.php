<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adicionar índices para otimizar queries de dívidas
        Schema::table('debts', function (Blueprint $table) {
            $indexes = DB::select("SHOW INDEX FROM debts");
            $existingIndexes = array_column($indexes, 'Key_name');

            $addIndex = function ($name, $callback) use ($table, $existingIndexes) {
                if (!in_array($name, $existingIndexes)) {
                    $callback();
                }
            };

            $addIndex('idx_status_type', fn() => $table->index(['status', 'debt_type'], 'idx_status_type'));
            $addIndex('idx_status_due_date', fn() => $table->index(['status', 'due_date'], 'idx_status_due_date'));
            $addIndex('idx_customer_name', fn() => $table->index('customer_name', 'idx_customer_name'));
            $addIndex('idx_employee_name', fn() => $table->index('employee_name', 'idx_employee_name'));
            $addIndex('idx_debt_date', fn() => $table->index('debt_date', 'idx_debt_date'));
            $addIndex('idx_user_id', fn() => $table->index('user_id', 'idx_user_id'));
            $addIndex('idx_employee_id', fn() => $table->index('employee_id', 'idx_employee_id'));
            $addIndex('idx_generated_sale_id', fn() => $table->index('generated_sale_id', 'idx_generated_sale_id'));
        });

        // Índices para debt_items
        Schema::table('debt_items', function (Blueprint $table) {
            $indexes = DB::select("SHOW INDEX FROM debt_items");
            $existingIndexes = array_column($indexes, 'Key_name');

            if (!in_array('idx_debt_id', $existingIndexes))
                $table->index('debt_id', 'idx_debt_id');

            if (!in_array('idx_product_id', $existingIndexes))
                $table->index('product_id', 'idx_product_id');
        });

        // Índices para debt_payments
        Schema::table('debt_payments', function (Blueprint $table) {
            $indexes = DB::select("SHOW INDEX FROM debt_payments");
            $existingIndexes = array_column($indexes, 'Key_name');

            if (!in_array('idx_payment_debt_id', $existingIndexes))
                $table->index('debt_id', 'idx_payment_debt_id');

            if (!in_array('idx_debt_payment_date', $existingIndexes))
                $table->index(['debt_id', 'payment_date'], 'idx_debt_payment_date');

            if (!in_array('idx_payment_date', $existingIndexes))
                $table->index('payment_date', 'idx_payment_date');
        });

        // Otimizar tabela de produtos
        Schema::table('products', function (Blueprint $table) {
            $indexes = DB::select("SHOW INDEX FROM products");
            $existingIndexes = array_column($indexes, 'Key_name');

            if (!in_array('idx_is_active', $existingIndexes))
                $table->index('is_active', 'idx_is_active');

            if (!in_array('idx_active_type', $existingIndexes))
                $table->index(['is_active', 'type'], 'idx_active_type');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropIndex('idx_status_type');
            $table->dropIndex('idx_status_due_date');
            $table->dropIndex('idx_customer_name');
            $table->dropIndex('idx_employee_name');
            $table->dropIndex('idx_debt_date');
            $table->dropIndex('idx_user_id');
            $table->dropIndex('idx_employee_id');
            $table->dropIndex('idx_generated_sale_id');
        });

        Schema::table('debt_items', function (Blueprint $table) {
            $table->dropIndex('idx_debt_id');
            $table->dropIndex('idx_product_id');
        });

        Schema::table('debt_payments', function (Blueprint $table) {
            $table->dropIndex('idx_payment_debt_id');
            $table->dropIndex('idx_debt_payment_date');
            $table->dropIndex('idx_payment_date');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_is_active');
            $table->dropIndex('idx_active_type');
        });
    }
};
