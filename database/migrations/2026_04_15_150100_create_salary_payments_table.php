<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('salary_payments')) {
            return;
        }

        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreignId('financial_account_id')->constrained('financial_accounts')->cascadeOnDelete();
            $table->foreignId('financial_transaction_id')->nullable()->constrained('financial_transactions')->nullOnDelete();
            $table->unsignedInteger('paid_by')->nullable();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->date('reference_month')->nullable();
            $table->string('description');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('paid_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['user_id', 'payment_date']);
            $table->index('reference_month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};
