<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_payments', function (Blueprint $table) {
            $table->decimal('base_amount', 10, 2)->nullable()->after('amount')
                ->comment('Valor base do salário');
            $table->decimal('variable_amount', 10, 2)->default(0)->after('base_amount')
                ->comment('Valor variável/comissão (máx 1500 MT)');
            $table->string('signed_receipt_path')->nullable()->after('notes')
                ->comment('Caminho do recibo assinado (foto/scan)');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->string('receipt_file_path')->nullable()->after('notes')
                ->comment('Caminho do comprovante/recibo assinado (foto/scan)');
        });
    }

    public function down(): void
    {
        Schema::table('salary_payments', function (Blueprint $table) {
            $table->dropColumn(['base_amount', 'variable_amount', 'signed_receipt_path']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('receipt_file_path');
        });
    }
};
