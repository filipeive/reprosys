<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\DebtItem;
use App\Models\DebtPayment;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DebtController extends Controller
{
    /**
     * Exibir lista de dívidas
     */
    public function index(Request $request)
    {
        $query = Debt::with(['user', 'sale', 'payments', 'items.product'])
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
     * Criar nova dívida com produtos
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'customer_document' => 'nullable|string|max:20',
            'debt_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:debt_date',
            'notes' => 'nullable|string',
            'initial_payment' => 'nullable|numeric|min:0',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0.01',
        ]);

        try {
            $debt = DB::transaction(function () use ($request) {
                // Validar estoque disponível
                foreach ($request->products as $productData) {
                    $product = Product::find($productData['product_id']);
                    if ($product->stock_quantity < $productData['quantity']) {
                        throw new \Exception("Estoque insuficiente para o produto: {$product->name}. Disponível: {$product->stock_quantity}");
                    }
                }

                // Calcular total
                $totalAmount = 0;
                foreach ($request->products as $productData) {
                    $totalAmount += $productData['quantity'] * $productData['unit_price'];
                }

                // Criar a dívida
                $debt = Debt::create([
                    'user_id' => auth()->id(),
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'customer_document' => $request->customer_document,
                    'original_amount' => $totalAmount,
                    'remaining_amount' => $totalAmount,
                    'debt_date' => $request->debt_date,
                    'due_date' => $request->due_date,
                    'status' => 'active',
                    'notes' => $request->notes
                ]);

                // Adicionar itens da dívida
                foreach ($request->products as $productData) {
                    $product = Product::find($productData['product_id']);
                    $subtotal = $productData['quantity'] * $productData['unit_price'];

                    // Criar item da dívida
                    DebtItem::create([
                        'debt_id' => $debt->id,
                        'product_id' => $product->id,
                        'quantity' => $productData['quantity'],
                        'unit_price' => $productData['unit_price'],
                        'total_price' => $subtotal
                    ]);

                    // Movimentar estoque (reduzir)
                    $product->decrement('stock_quantity', $productData['quantity']);

                    // Registrar movimento de estoque
                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'type' => 'out',
                        'quantity' => $productData['quantity'],
                        'reason' => 'debt_creation',
                        'reference_id' => $debt->id,
                        'notes' => "Dívida #{$debt->id} - {$debt->customer_name}"
                    ]);
                }

                // Registrar pagamento inicial se houver
                if ($request->filled('initial_payment') && $request->initial_payment > 0) {
                    $initialPayment = min($request->initial_payment, $totalAmount);
                    
                    DebtPayment::create([
                        'debt_id' => $debt->id,
                        'user_id' => auth()->id(),
                        'amount' => $initialPayment,
                        'payment_method' => 'cash', // Default, pode ser ajustado
                        'payment_date' => $request->debt_date,
                        'notes' => 'Pagamento inicial (entrada)'
                    ]);

                    $debt->updatePaymentStatus();
                }

                return $debt;
            });

            return response()->json([
                'success' => true,
                'message' => 'Dívida criada com sucesso! Estoque atualizado.',
                'debt' => $debt
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
     * Atualizar dívida (apenas dados básicos - produtos não editáveis após criação)
     */
    public function update(Request $request, Debt $debt)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'customer_document' => 'nullable|string|max:20',
            'due_date' => 'nullable|date|after_or_equal:debt_date',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($debt, $request) {
                $debt->update($request->only([
                    'customer_name', 'customer_phone', 'customer_document',
                    'due_date', 'notes'
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
        $debt->load(['user', 'items.product.category', 'payments.user']);

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
        $debt->load('items.product');
        
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
            'payment_method' => 'required|in:cash,card,transfer,mpesa,emola',
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

                // Se a dívida foi totalmente paga, criar venda automática
                if ($debt->status === 'paid') {
                    $this->createSaleFromPaidDebt($debt);
                }
            });

            $message = $debt->status === 'paid'
                ? 'Dívida quitada com sucesso! Venda registrada automaticamente.'
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
     * Criar venda automática quando dívida é totalmente paga
     */
    private function createSaleFromPaidDebt(Debt $debt)
    {
        if ($debt->sale_id) {
            return; // Já tem venda associada
        }

        try {
            $sale = Sale::create([
                'user_id' => $debt->user_id,
                'customer_name' => $debt->customer_name,
                'customer_phone' => $debt->customer_phone,
                'total_amount' => $debt->original_amount,
                'payment_method' => 'mixed', // Método misto para parcelado
                'sale_date' => $debt->payments()->latest()->first()->payment_date,
                'notes' => "Venda gerada automaticamente da dívida #{$debt->id}",
                'status' => 'completed'
            ]);

            // Copiar itens da dívida para a venda
            foreach ($debt->items as $debtItem) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $debtItem->product_id,
                    'quantity' => $debtItem->quantity,
                    'unit_price' => $debtItem->unit_price,
                    'total_price' => $debtItem->total_price
                ]);
            }

            // Associar venda à dívida
            $debt->update(['sale_id' => $sale->id]);

            Log::info("Venda #{$sale->id} criada automaticamente da dívida #{$debt->id}");
        } catch (\Exception $e) {
            Log::error("Erro ao criar venda da dívida #{$debt->id}: " . $e->getMessage());
        }
    }

    /**
     * Cancelar dívida (devolver estoque)
     */
    public function cancel(Debt $debt)
    {
        if ($debt->status === 'paid' || $debt->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Esta dívida não pode ser cancelada.'
            ], 400);
        }

        try {
            DB::transaction(function () use ($debt) {
                // Devolver estoque
                foreach ($debt->items as $item) {
                    $item->product->increment('stock_quantity', $item->quantity);

                    // Registrar movimento de estoque
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'user_id' => auth()->id(),
                        'type' => 'in',
                        'quantity' => $item->quantity,
                        'reason' => 'debt_cancellation',
                        'reference_id' => $debt->id,
                        'notes' => "Cancelamento da dívida #{$debt->id} - {$debt->customer_name}"
                    ]);
                }

                $debt->update(['status' => 'cancelled']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Dívida cancelada e estoque devolvido com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar dívida: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar dívida.'
            ], 500);
        }
    }

    /**
     * API para buscar produtos disponíveis
     */
    public function getAvailableProducts(Request $request)
    {
        $query = Product::with('category')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
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

    /**
     * Atualizar status de dívidas vencidas
     */
    public function updateOverdueStatus()
    {
        try {
            $updatedCount = Debt::where('status', 'active')
                ->where('due_date', '<', now()->toDateString())
                ->update(['status' => 'overdue']);

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} dívidas foram marcadas como vencidas."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status das dívidas.'
            ], 500);
        }
    }
}