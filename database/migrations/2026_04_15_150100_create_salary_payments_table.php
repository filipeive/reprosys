<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $userIdDefinition = $this->resolveUserIdColumnDefinition();

        if (!Schema::hasTable('salary_payments')) {
            Schema::create('salary_payments', function (Blueprint $table) use ($userIdDefinition) {
                $table->id();

                $this->addUserReferenceColumns($table, $userIdDefinition);

                $table->foreignId('financial_account_id')->constrained('financial_accounts')->cascadeOnDelete();
                $table->foreignId('financial_transaction_id')->nullable()->constrained('financial_transactions')->nullOnDelete();
                $table->decimal('amount', 12, 2);
                $table->date('payment_date');
                $table->date('reference_month')->nullable();
                $table->string('description');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'payment_date']);
                $table->index('reference_month');
            });
        }

        $this->syncUserReferenceColumns('salary_payments', $userIdDefinition);
        $this->ensureForeignKey('salary_payments', 'salary_payments_user_id_foreign', 'user_id', 'users', 'id', 'cascade');
        $this->ensureForeignKey('salary_payments', 'salary_payments_paid_by_foreign', 'paid_by', 'users', 'id', 'set null');
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }

    private function addUserReferenceColumns(Blueprint $table, array $userIdDefinition): void
    {
        $userIdMethod = $userIdDefinition['type'] === 'bigint'
            ? ($userIdDefinition['unsigned'] ? 'unsignedBigInteger' : 'bigInteger')
            : ($userIdDefinition['unsigned'] ? 'unsignedInteger' : 'integer');

        $table->{$userIdMethod}('user_id');
        $table->{$userIdMethod}('paid_by')->nullable();
    }

    private function resolveUserIdColumnDefinition(): array
    {
        $column = DB::selectOne("SHOW COLUMNS FROM users LIKE 'id'");
        $type = strtolower($column->Type ?? 'int');

        return [
            'type' => str_contains($type, 'bigint') ? 'bigint' : 'int',
            'unsigned' => str_contains($type, 'unsigned'),
        ];
    }

    private function syncUserReferenceColumns(string $table, array $userIdDefinition): void
    {
        $baseType = $userIdDefinition['type'] === 'bigint' ? 'BIGINT' : 'INT';
        $signedSuffix = $userIdDefinition['unsigned'] ? ' UNSIGNED' : '';

        DB::statement("ALTER TABLE {$table} MODIFY user_id {$baseType}{$signedSuffix} NOT NULL");
        DB::statement("ALTER TABLE {$table} MODIFY paid_by {$baseType}{$signedSuffix} NULL");
    }

    private function ensureForeignKey(
        string $table,
        string $constraintName,
        string $column,
        string $referencesTable,
        string $referencesColumn,
        string $onDelete
    ): void {
        $database = DB::getDatabaseName();

        $exists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraintName)
            ->exists();

        if ($exists) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($column, $referencesTable, $referencesColumn, $onDelete) {
            $foreign = $tableBlueprint->foreign($column)->references($referencesColumn)->on($referencesTable);

            if ($onDelete === 'cascade') {
                $foreign->cascadeOnDelete();
            }

            if ($onDelete === 'set null') {
                $foreign->nullOnDelete();
            }
        });
    }
};
