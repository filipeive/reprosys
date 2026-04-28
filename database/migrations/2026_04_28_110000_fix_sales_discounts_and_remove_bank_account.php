<?php

use App\Models\Sale;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !DB::getSchemaBuilder()->hasTable('sales') ||
            !DB::getSchemaBuilder()->hasTable('sale_items') ||
            !DB::getSchemaBuilder()->hasTable('financial_transactions')
        ) {
            return;
        }

        $sales = DB::table('sales')->select('id', 'payment_method', 'sale_date', 'user_id', 'notes')->get();

        foreach ($sales as $sale) {
            $itemTotals = DB::table('sale_items')
                ->where('sale_id', $sale->id)
                ->selectRaw('COALESCE(SUM(original_unit_price * quantity), 0) as subtotal')
                ->selectRaw('COALESCE(SUM(discount_amount), 0) as discount_amount')
                ->first();

            $subtotal = round((float) ($itemTotals->subtotal ?? 0), 2);
            $discountAmount = min($subtotal, round((float) ($itemTotals->discount_amount ?? 0), 2));
            $totalAmount = max(0, round($subtotal - $discountAmount, 2));
            $discountPercentage = $subtotal > 0 ? round(($discountAmount / $subtotal) * 100, 2) : 0;

            DB::table('sales')
                ->where('id', $sale->id)
                ->update([
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'discount_percentage' => $discountPercentage,
                    'total_amount' => $totalAmount,
                    'updated_at' => now(),
                ]);

            DB::table('financial_transactions')
                ->where('reference_type', Sale::class)
                ->where('reference_id', $sale->id)
                ->delete();

            if ($sale->payment_method === 'credit') {
                continue;
            }

            $accountSlug = match ($sale->payment_method) {
                'mpesa', 'emola' => 'carteira-movel',
                'cash', 'card', 'transfer', null => 'caixa-principal',
                default => null,
            };

            if (!$accountSlug) {
                continue;
            }

            $financialAccountId = DB::table('financial_accounts')
                ->where('slug', $accountSlug)
                ->value('id');

            if (!$financialAccountId) {
                continue;
            }

            DB::table('financial_transactions')->insert([
                'financial_account_id' => $financialAccountId,
                'user_id' => $sale->user_id,
                'type' => 'sale_receipt',
                'direction' => 'in',
                'amount' => $totalAmount,
                'transaction_date' => $sale->sale_date ? date('Y-m-d', strtotime((string) $sale->sale_date)) : now()->toDateString(),
                'description' => "Recebimento da venda #{$sale->id}",
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'payment_method' => $sale->payment_method,
                'notes' => $sale->notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
    }
};
