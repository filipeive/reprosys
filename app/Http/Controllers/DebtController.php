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
        $query = Debt::with(['user', 'payments', 'items.product'])
            ->latest();

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer')) {
            $query->where('customer_name', 'like', '%' . $request->customer . '%');
        }

        if ($request->filled('overdue_only')) {
            $query->where('status', 'active')
                  ->where('due_date', '<', now()->toDateString());
        }

        if ($request->filled('date_from')) {
            $query->whereDate('debt_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('debt_date', '<=', $request->date_to);
        }

        $debts = $query->paginate(15);

        // Estatísticas
        $stats = [
            'total_active' => Debt::where('status', 'active')->sum('remaining_amount'),
            'total_overdue' => Debt::where('status', 'active')
                                  ->where('due_date', '<', now()->toDateString())
                                  ->sum('remaining_amount'),
            'count_active' => Debt::where('status', 'active')->count(),
            'count_overdue' => Debt::where('status', 'active')
                                  ->where('due_date', '<', now()->toDateString())
                                  ->count(),
            'count_paid_this_month' => Debt::where('status', 'paid')
                ->whereMonth('updated_at', now()->month)
                ->count()
        ];

        $products = Product::where('is_active', true)->get();

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
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'initial_payment' => 'nullable|numeric|min:0',
            'products' => 'required|string' // JSON string dos produtos
        ]);

        try {
            // Decodificar produtos
            $products = json_decode($request->products, true);
            
            if (!$products || !is_array($products) || empty($products)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum produto foi fornecido para a dívida.'
                ], 400);
            }

            // Validar estrutura dos produtos
            foreach ($products as $index => $productData) {
                if (!isset($productData['product_id']) || !isset($productData['quantity']) || !isset($productData['unit_price'])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Produto #{$index} com dados incompletos."
                    ], 400);
                }

                if ($productData['quantity'] <= 0 || $productData['unit_price'] < 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Quantidade e preço devem ser valores positivos."
                    ], 400);
                }
            }

            $debt = DB::transaction(function () use ($request, $products) {
                // Validar estoque disponível
                foreach ($products as $productData) {
                    $product = Product::find($productData['product_id']);
                    
                    if (!$product) {
                        throw new \Exception("Produto não encontrado: ID {$productData['product_id']}");
                    }

                    if ($product->type === 'product' && $product->stock_quantity < $productData['quantity']) {
                        throw new \Exception("Estoque insuficiente para o produto: {$product->name}. Disponível: {$product->stock_quantity}");
                    }
                }

                // Calcular total
                $totalAmount = 0;
                foreach ($products as $productData) {
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
                    'due_date' => $request->due_date ?: now()->addDays(30)->toDateString(),
                    'status' => 'active',
                    'description' => $request->description,
                    'notes' => $request->notes
                ]);

                // Adicionar itens da dívida e movimentar estoque
                foreach ($products as $productData) {
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

                    // Movimentar estoque apenas para produtos físicos
                    if ($product->type === 'product') {
                        $product->decrement('stock_quantity', $productData['quantity']);

                        // Registrar movimento de estoque
                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'movement_type' => 'out',
                            'quantity' => $productData['quantity'],
                            'reason' => 'Dívida',
                            'reference_id' => $debt->id,
                            'movement_date' => $request->debt_date
                        ]);
                    }
                }

                // Registrar pagamento inicial se houver
                if ($request->filled('initial_payment') && $request->initial_payment > 0) {
                    $initialPayment = min($request->initial_payment, $totalAmount);
                    
                    DebtPayment::create([
                        'debt_id' => $debt->id,
                        'user_id' => auth()->id(),
                        'amount' => $initialPayment,
                        'payment_method' => 'cash',
                        'payment_date' => $request->debt_date,
                        'notes' => 'Pagamento inicial (entrada)'
                    ]);

                    // Atualizar valor restante
                    $debt->remaining_amount = $totalAmount - $initialPayment;
                    if ($debt->remaining_amount <= 0) {
                        $debt->status = 'paid';
                    }
                    $debt->save();
                }

                return $debt;
            });

            return response()->json([
                'success' => true,
                'message' => 'Dívida criada com sucesso! Estoque atualizado.',
                'redirect' => route('debts.show', $debt)
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos: ' . implode(' ', $e->validator->errors()->all())
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Erro ao criar dívida: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar detalhes da dívida
     */
    public function show(Debt $debt)
    {   
        $products = Product::where('is_active', true)->get();
        $debt->load(['user', 'items.product.category', 'payments.user']);
        return view('debts.show', compact('debt', 'products'));
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
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($debt, $request) {
                $debt->update($request->only([
                    'customer_name', 'customer_phone', 'customer_document',
                    'due_date', 'description', 'notes'
                ]));
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dívida atualizada com sucesso!'
                ]);
            }

            return redirect()->route('debts.show', $debt)
                ->with('success', 'Dívida atualizada com sucesso!');
                
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar dívida: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar dívida.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Erro ao atualizar dívida.')
                ->withInput();
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

        if ($debt->status !== 'active') {
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

                // Atualizar valor restante
                $debt->remaining_amount -= $request->amount;
                
                if ($debt->remaining_amount <= 0) {
                    $debt->status = 'paid';
                    $debt->remaining_amount = 0;
                    
                    // Criar venda automática quando totalmente pago
                    $this->createSaleFromPaidDebt($debt);
                }
                
                $debt->save();
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
     * Marcar como paga
     */
    public function markAsPaid(Request $request, Debt $debt)
    {
        if ($debt->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Esta dívida não pode ser marcada como paga.'
            ], 400);
        }

        try {
            DB::transaction(function () use ($debt, $request) {
                // Criar pagamento final
                if ($debt->remaining_amount > 0) {
                    $debt->payments()->create([
                        'user_id' => auth()->id(),
                        'amount' => $debt->remaining_amount,
                        'payment_method' => $request->payment_method ?? 'cash',
                        'payment_date' => now()->toDateString(),
                        'notes' => 'Pagamento final - quitação completa'
                    ]);
                }

                $debt->update([
                    'status' => 'paid',
                    'remaining_amount' => 0
                ]);

                // Criar venda automática
                $this->createSaleFromPaidDebt($debt);
            });

            return response()->json([
                'success' => true,
                'message' => 'Dívida quitada com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao marcar dívida como paga: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao quitar dívida.'
            ], 500);
        }
    }

    /**
     * Criar venda automática quando dívida é totalmente paga
     */
    private function createSaleFromPaidDebt(Debt $debt)
    {
        try {
            $sale = Sale::create([
                'user_id' => $debt->user_id,
                'customer_name' => $debt->customer_name,
                'customer_phone' => $debt->customer_phone,
                'total_amount' => $debt->original_amount,
                'payment_method' => 'mixed',
                'sale_date' => now(),
                'notes' => "Venda gerada automaticamente da dívida #{$debt->id}",
                'payment_status' => 'paid'
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

            Log::info("Venda #{$sale->id} criada automaticamente da dívida #{$debt->id}");
            return $sale;
            
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
                    if ($item->product->type === 'product') {
                        $item->product->increment('stock_quantity', $item->quantity);

                        // Registrar movimento de estoque
                        StockMovement::create([
                            'product_id' => $item->product_id,
                            'user_id' => auth()->id(),
                            'movement_type' => 'in',
                            'quantity' => $item->quantity,
                            'reason' => 'Cancelamento de dívida',
                            'reference_id' => $debt->id,
                            'movement_date' => now()->toDateString()
                        ]);
                    }
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
            ->where('is_active', true);

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
     * API para buscar clientes
     */
    public function searchCustomers(Request $request)
    {
        $search = $request->get('q', '');
        
        $customers = Debt::select('customer_name', 'customer_phone')
            ->where('customer_name', 'like', "%{$search}%")
            ->orWhere('customer_phone', 'like', "%{$search}%")
            ->groupBy('customer_name', 'customer_phone')
            ->limit(10)
            ->get();

        return response()->json($customers);
    }

    /**
     * Relatório de devedores
     */
    /**
 * Relatório de devedores (apenas dívidas ativas ou vencidas)
 */
    public function debtorsReport(Request $request)
    {
        $query = Debt::with(['payments'])
            ->whereNotIn('status', ['paid', 'cancelled']) // ✅ Corrigido: exclui paid e cancelled
            ->selectRaw('customer_name, customer_phone, SUM(remaining_amount) as total_debt, COUNT(*) as debt_count, MIN(debt_date) as oldest_debt')
            ->groupBy('customer_name', 'customer_phone');

        if ($request->filled('customer')) {
            $query->where('customer_name', 'like', '%' . $request->customer . '%');
        }
        $query->addSelect(DB::raw("
            CASE 
                WHEN MAX(due_date) < CURDATE() THEN 'Vencida'
                ELSE 'Ativa'
            END as status_group
        "));
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