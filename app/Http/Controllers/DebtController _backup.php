<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DebtController_backup extends Controller
{
    /**
     * Exibir lista de dívidas
     */
    public function index(Request $request)
    {
        $query = Debt::with(['user', 'sale', 'payments'])
            ->latest();

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer')) {
            $query->where('customer_name', 'like', '%' . $request->customer . '%');
        }

        if ($request->filled('overdue_only')) {
            $query->overdue();
        }

        if ($request->filled('date_from')) {
            $query->whereDate('debt_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('debt_date', '<=', $request->date_to);
        }
        $products = Product::where('is_active', true)->get();
         view()->share('products', $products);

        $debts = $query->paginate(15);

        // Estatísticas
        $stats = [
            'total_active' => Debt::where('status', '!=', 'paid')->sum('remaining_amount'),
            'total_overdue' => Debt::overdue()->sum('remaining_amount'),
            'count_active' => Debt::active()->count(),
            'count_overdue' => Debt::overdue()->count(),
            'count_paid_this_month' => Debt::paid()
                ->whereMonth('updated_at', now()->month)
                ->count()
        ];

        return view('debts.index', compact('debts', 'stats', 'products'));
    }

    /**
     * Criar nova dívida (via offcanvas)
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'debt_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:debt_date',
            'description' => 'required|string',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $debt = DB::transaction(function () use ($request) {
                $items = json_decode($request->items, true);
                $totalAmount = 0;

                // Validar estoque e calcular total
                foreach ($items as $item) {
                    $product = Product::findOrFail($item['id']);
                    if ($product->type === 'product' && $product->stock_quantity < $item['quantity']) {
                        throw new \Exception("Estoque insuficiente para {$product->name}. Disponível: {$product->stock_quantity}");
                    }
                    $totalAmount += $product->selling_price * $item['quantity'];
                }

                // Criar venda (rascunho)
                $sale = Sale::create([
                    'user_id' => auth()->id(),
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'sale_date' => $request->debt_date,
                    'due_date' => $request->due_date,
                    'total_amount' => $totalAmount,
                    'status' => 'pending_payment',
                    'notes' => $request->notes,
                ]);

                // Criar itens da venda
                foreach ($items as $item) {
                    $product = Product::findOrFail($item['id']);
                    $unitPrice = $product->selling_price;
                    $totalPrice = $unitPrice * $item['quantity'];

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                    ]);

                    // Reduzir estoque
                    if ($product->type === 'product') {
                        $product->decrement('stock_quantity', $item['quantity']);
                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'movement_type' => 'out',
                            'quantity' => $item['quantity'],
                            'reason' => 'Venda a crédito',
                            'reference_id' => $sale->id,
                            'movement_date' => $request->debt_date,
                        ]);
                    }
                }

                // Criar dívida
                return Debt::create([
                    'user_id' => auth()->id(),
                    'sale_id' => $sale->id,
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'original_amount' => $totalAmount,
                    'remaining_amount' => $totalAmount,
                    'debt_date' => $request->debt_date,
                    'due_date' => $request->due_date,
                    'status' => 'active',
                    'description' => $request->description,
                    'notes' => $request->notes
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Dívida criada com sucesso! Venda registrada.'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao criar dívida: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar dívida (via offcanvas)
     */
    public function update(Request $request, Debt $debt)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'customer_document' => 'nullable|string|max:20',
            'due_date' => 'nullable|date|after_or_equal:debt_date',
            'description' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($debt, $request) {
                $debt->update($request->only([
                    'customer_name', 'customer_phone', 'customer_document',
                    'due_date', 'description', 'notes'
                ]));
            });

            return response()->json([
                'success' => true,
                'message' => 'Dívida atualizada com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar dívida: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar dívida.'
            ], 500);
        }
    }

    /**
     * Mostrar detalhes da dívida (para offcanvas)
     */
    public function showDetails(Debt $debt)
    {
        $debt->load(['user', 'sale.items.product.category', 'payments.user']);

        $html = view('debts.partials.details', compact('debt'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Dados para edição (AJAX)
     */
    public function editData(Debt $debt)
    {
        return response()->json([
            'success' => true,
            'data' => $debt
        ]);
    }

    /**
     * Registrar pagamento
     */
    public function addPayment(Request $request, Debt $debt)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $debt->remaining_amount,
            'payment_method' => 'required|in:cash,card,transfer,pix,mpesa,emola',
            'payment_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:500'
        ]);

        if (!$debt->canAddPayment()) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível adicionar pagamento a esta dívida.'
            ], 400);
        }

        try {
            DB::transaction(function () use ($debt, $request) {
                $payment = $debt->payments()->create([
                    'user_id' => auth()->id(),
                    'amount' => $request->amount,
                    'payment_method' => $request->payment_method,
                    'payment_date' => $request->payment_date,
                    'notes' => $request->notes
                ]);

                $debt->updatePaymentStatus();
            });

            $message = $debt->status === 'paid'
                ? 'Dívida quitada com sucesso! Venda concluída.'
                : 'Pagamento registrado com sucesso.';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao registrar pagamento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar pagamento.'
            ], 500);
        }
    }

    /**
     * Criar dívida a partir de uma venda (opcional)
     */
    public function createFromSale(Sale $sale)
    {
        if ($sale->debt) {
            return redirect()->back()->with('error', 'Esta venda já tem uma dívida associada.');
        }

        $debt = Debt::create([
            'user_id' => $sale->user_id,
            'sale_id' => $sale->id,
            'customer_name' => $sale->customer_name,
            'customer_phone' => $sale->customer_phone,
            'original_amount' => $sale->total_amount,
            'remaining_amount' => $sale->total_amount,
            'debt_date' => $sale->sale_date,
            'due_date' => $sale->due_date ?? now()->addDays(30),
            'status' => 'active',
            'description' => "Parcelamento da venda #{$sale->id}",
            'notes' => $sale->notes
        ]);

        $sale->update(['status' => 'pending_payment']);

        return redirect()->route('debts.show', $debt)
            ->with('success', 'Dívida criada com sucesso a partir da venda!');
    }

    /**
     * Relatório de devedores
     */
    public function debtorsReport(Request $request)
    {
        $query = Debt::with(['payments'])
            ->where('status', '!=', 'paid')
            ->selectRaw('customer_name, customer_phone, SUM(remaining_amount) as total_debt, COUNT(*) as debt_count, MIN(debt_date) as oldest_debt')
            ->groupBy('customer_name', 'customer_phone');

        if ($request->filled('customer')) {
            $query->where('customer_name', 'like', '%' . $request->customer . '%');
        }

        $debtors = $query->orderByDesc('total_debt')->paginate(20);

        return view('debts.debtors-report', compact('debtors'));
    }
}