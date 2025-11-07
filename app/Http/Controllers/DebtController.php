<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\DebtItem;
use App\Models\DebtPayment;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class DebtController extends Controller
{
    private const TRANSACTION_TIMEOUT = 30;
    private const MAX_RETRIES = 3;

    /**
     * Exibir lista de dívidas - VERSÃO SIMPLIFICADA E FUNCIONAL
     */
    public function index(Request $request)
    {
        try {
            // Query básica sem eager loading excessivo
            $query = Debt::query()->latest('created_at');

            // Aplicar filtros simples
            if ($request->filled('debt_type')) {
                $query->where('debt_type', $request->debt_type);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('customer')) {
                $search = '%' . $request->customer . '%';
                $query->where(function($q) use ($search) {
                    $q->where('customer_name', 'like', $search)
                      ->orWhere('employee_name', 'like', $search);
                });
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

            // Paginar primeiro, depois carregar relacionamentos
            $debts = $query->paginate(15)->withQueryString();
            
            // Carregar relacionamentos apenas para os itens da página
            $debts->load(['user', 'employee']);

            // Estatísticas diretas (sem cache para debugging)
            $stats = [
                'total_active' => Debt::where('status', 'active')->sum('remaining_amount') ?? 0,
                'total_overdue' => Debt::where('status', 'active')
                    ->where('due_date', '<', now()->toDateString())
                    ->sum('remaining_amount') ?? 0,
                'count_active' => Debt::where('status', 'active')->count(),
                'count_paid_this_month' => Debt::where('status', 'paid')
                    ->whereMonth('updated_at', now()->month)
                    ->count(),
                'product_debts' => [
                    'total_active' => Debt::where('debt_type', 'product')
                        ->where('status', 'active')
                        ->sum('remaining_amount') ?? 0,
                    'count_active' => Debt::where('debt_type', 'product')
                        ->where('status', 'active')
                        ->count(),
                    'total_overdue' => Debt::where('debt_type', 'product')
                        ->where('status', 'active')
                        ->where('due_date', '<', now()->toDateString())
                        ->sum('remaining_amount') ?? 0,
                ],
                'money_debts' => [
                    'total_active' => Debt::where('debt_type', 'money')
                        ->where('status', 'active')
                        ->sum('remaining_amount') ?? 0,
                    'count_active' => Debt::where('debt_type', 'money')
                        ->where('status', 'active')
                        ->count(),
                    'total_overdue' => Debt::where('debt_type', 'money')
                        ->where('status', 'active')
                        ->where('due_date', '<', now()->toDateString())
                        ->sum('remaining_amount') ?? 0,
                ]
            ];

            // Produtos e funcionários (lista simples)
            $products = Product::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'type', 'unit', 'selling_price', 'stock_quantity']);

            $employees = User::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'phone']);

            return view('debts.index', compact('debts', 'stats', 'products', 'employees'));
            
        } catch (\Exception $e) {
            Log::error('Erro ao carregar índice de dívidas: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user_id' => auth()->id()
            ]);
            
            // Retornar com mensagem de erro mas não quebrar a página
            return back()->withErrors(['error' => 'Erro ao carregar dívidas: ' . $e->getMessage()]);
        }
    }

    /**
     * Criar nova dívida
     */
    public function store(Request $request)
    {
        try {
            $validated = $this->validateDebtRequest($request);

            if ($request->debt_type === 'product') {
                return $this->storeProductDebt($request);
            } else {
                return $this->storeMoneyDebt($request);
            }
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Erro ao criar dívida: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar requisição
     */
    private function validateDebtRequest(Request $request)
    {
        $baseRules = [
            'debt_type' => 'required|in:product,money',
            'debt_date' => 'required|date|before_or_equal:today',
            'due_date' => 'nullable|date|after_or_equal:debt_date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'initial_payment' => 'nullable|numeric|min:0',
        ];

        if ($request->debt_type === 'product') {
            $specificRules = [
                'customer_name' => 'required|string|max:100',
                'customer_phone' => 'nullable|string|max:20',
                'customer_document' => 'nullable|string|max:20',
                'products' => 'required|string',
            ];
        } else {
            $specificRules = [
                'employee_id' => 'required|exists:users,id',
                'employee_name' => 'required|string|max:100',
                'employee_phone' => 'nullable|string|max:20',
                'employee_document' => 'nullable|string|max:20',
                'amount' => 'required|numeric|min:0.01',
            ];
        }

        return $request->validate(array_merge($baseRules, $specificRules));
    }

    /**
     * Criar dívida de produtos - SIMPLIFICADO
     */
    private function storeProductDebt(Request $request)
    {
        $products = json_decode($request->products, true);
        
        if (empty($products) || !is_array($products)) {
            throw new \Exception('Nenhum produto válido fornecido.');
        }

        DB::beginTransaction();
        
        try {
            // Calcular total
            $totalAmount = 0;
            foreach ($products as $productData) {
                $totalAmount += $productData['quantity'] * $productData['unit_price'];
            }

            // Criar dívida
            $debt = Debt::create([
                'debt_type' => 'product',
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

            // Adicionar produtos
            foreach ($products as $productData) {
                $product = Product::find($productData['product_id']);
                
                if (!$product) {
                    throw new \Exception("Produto não encontrado: ID {$productData['product_id']}");
                }

                // Verificar estoque
                if ($product->type === 'product' && $product->stock_quantity < $productData['quantity']) {
                    throw new \Exception("Estoque insuficiente para {$product->name}");
                }

                $subtotal = $productData['quantity'] * $productData['unit_price'];

                // Criar item
                DebtItem::create([
                    'debt_id' => $debt->id,
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                    'unit_price' => $productData['unit_price'],
                    'total_price' => $subtotal
                ]);

                // Movimentar estoque
                if ($product->type === 'product') {
                    $product->decrement('stock_quantity', $productData['quantity']);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'movement_type' => 'out',
                        'quantity' => $productData['quantity'],
                        'reason' => 'Dívida de produtos #' . $debt->id,
                        'reference_id' => $debt->id,
                        'movement_date' => $request->debt_date
                    ]);
                }
            }

            // Pagamento inicial
            if ($request->filled('initial_payment') && $request->initial_payment > 0) {
                $this->processInitialPayment($debt, $request);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dívida de produtos criada com sucesso!',
                'debt_id' => $debt->id,
                'redirect' => route('debts.show', $debt)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Criar dívida de dinheiro - SIMPLIFICADO
     */
    private function storeMoneyDebt(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $employee = User::findOrFail($request->employee_id);

            $debt = Debt::create([
                'debt_type' => 'money',
                'user_id' => auth()->id(),
                'employee_id' => $employee->id,
                'employee_name' => $request->employee_name ?: $employee->name,
                'employee_phone' => $request->employee_phone ?: $employee->phone,
                'employee_document' => $request->employee_document,
                'original_amount' => $request->amount,
                'remaining_amount' => $request->amount,
                'debt_date' => $request->debt_date,
                'due_date' => $request->due_date ?: now()->addDays(30)->toDateString(),
                'status' => 'active',
                'description' => $request->description,
                'notes' => $request->notes
            ]);

            if ($request->filled('initial_payment') && $request->initial_payment > 0) {
                $this->processInitialPayment($debt, $request);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dívida de dinheiro criada com sucesso!',
                'debt_id' => $debt->id,
                'redirect' => route('debts.show', $debt)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Processar pagamento inicial
     */
    private function processInitialPayment(Debt $debt, Request $request)
    {
        $initialPayment = min((float)$request->initial_payment, $debt->original_amount);
        
        if ($initialPayment <= 0) {
            return;
        }

        DebtPayment::create([
            'debt_id' => $debt->id,
            'user_id' => auth()->id(),
            'amount' => $initialPayment,
            'payment_method' => 'cash',
            'payment_date' => $request->debt_date,
            'notes' => 'Pagamento inicial (entrada)'
        ]);

        $debt->remaining_amount = $debt->original_amount - $initialPayment;
        
        if ($debt->remaining_amount <= 0) {
            $debt->status = 'paid';
            $debt->remaining_amount = 0;
        }
        
        $debt->save();
    }

    /**
     * Registrar pagamento - SIMPLIFICADO E FUNCIONAL
     */
    public function addPayment(Request $request, Debt $debt)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.01|max:' . $debt->remaining_amount,
                'payment_method' => 'required|in:cash,card,transfer,mpesa,emola',
                'payment_date' => 'required|date|before_or_equal:today',
                'notes' => 'nullable|string|max:500',
                'create_sale' => 'sometimes|boolean'
            ]);

            if (!$debt->canReceivePayment()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta dívida não pode receber pagamentos.'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Criar pagamento
                DebtPayment::create([
                    'debt_id' => $debt->id,
                    'user_id' => auth()->id(),
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method'],
                    'payment_date' => $validated['payment_date'],
                    'notes' => $validated['notes'] ?? null
                ]);

                // Atualizar dívida
                $debt->remaining_amount -= $validated['amount'];
                
                $wasFullyPaid = false;
                if ($debt->remaining_amount <= 0.01) {
                    $debt->status = 'paid';
                    $debt->remaining_amount = 0;
                    $wasFullyPaid = true;
                }
                
                $debt->save();

                // Criar venda se necessário
                $sale = null;
                if ($wasFullyPaid && $debt->isProductDebt() && ($request->create_sale ?? true)) {
                    $sale = $this->createSaleFromDebt($debt);
                }

                DB::commit();

                $message = $wasFullyPaid
                    ? ($sale ? "Dívida quitada! Venda #{$sale->id} criada." : 'Dívida quitada com sucesso!')
                    : 'Pagamento registrado com sucesso.';

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'remaining_amount' => $debt->remaining_amount,
                        'status' => $debt->status,
                        'sale_id' => $sale->id ?? null
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Erro ao registrar pagamento: ' . $e->getMessage(), [
                'debt_id' => $debt->id,
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar pagamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Criar venda a partir de dívida paga
     */
    private function createSaleFromDebt(Debt $debt)
    {
        if (!$debt->isProductDebt()) {
            return null;
        }

        $items = DebtItem::where('debt_id', $debt->id)->with('product')->get();

        if ($items->isEmpty()) {
            return null;
        }

        $sale = Sale::create([
            'user_id' => auth()->id(),
            'customer_name' => $debt->customer_name,
            'customer_phone' => $debt->customer_phone,
            'subtotal' => $debt->original_amount,
            'total_amount' => $debt->original_amount,
            'payment_method' => 'mixed',
            'sale_date' => now(),
            'notes' => "Venda da dívida #{$debt->id} - {$debt->description}",
        ]);

        foreach ($items as $debtItem) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $debtItem->product_id,
                'quantity' => $debtItem->quantity,
                'original_unit_price' => $debtItem->unit_price,
                'unit_price' => $debtItem->unit_price,
                'total_price' => $debtItem->total_price
            ]);
        }

        $debt->update(['generated_sale_id' => $sale->id]);

        return $sale;
    }

    /**
     * Cancelar dívida
     */
    public function cancel(Debt $debt)
    {
        if (!$debt->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta dívida não pode ser cancelada.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            if ($debt->isProductDebt()) {
                $items = DebtItem::where('debt_id', $debt->id)->with('product')->get();

                foreach ($items as $item) {
                    if ($item->product->type === 'product') {
                        $item->product->increment('stock_quantity', $item->quantity);

                        StockMovement::create([
                            'product_id' => $item->product_id,
                            'user_id' => auth()->id(),
                            'movement_type' => 'in',
                            'quantity' => $item->quantity,
                            'reason' => 'Cancelamento de dívida #' . $debt->id,
                            'reference_id' => $debt->id,
                            'movement_date' => now()->toDateString()
                        ]);
                    }
                }
            }

            $debt->update(['status' => 'cancelled']);

            DB::commit();

            $message = $debt->isProductDebt()
                ? 'Dívida cancelada e estoque devolvido!'
                : 'Dívida cancelada com sucesso!';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao cancelar dívida: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar dívida.'
            ], 500);
        }
    }

    /**
     * Mostrar detalhes
     */
    public function show(Debt $debt)
    {
        $debt->load([
            'user',
            'employee',
            'items.product.category',
            'payments.user',
            'generatedSale'
        ]);

        $products = Product::where('is_active', true)->get();
        $employees = User::where('is_active', true)->orderBy('name')->get();

        return view('debts.show', compact('debt', 'products', 'employees'));
    }

    /**
     * Marcar como paga
     */
    public function markAsPaid(Request $request, Debt $debt)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,transfer,mpesa,emola',
            'create_sale' => 'sometimes|boolean'
        ]);

        if (!$debt->canBeMarkedAsPaid()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta dívida não pode ser marcada como paga.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            if ($debt->remaining_amount > 0) {
                DebtPayment::create([
                    'debt_id' => $debt->id,
                    'user_id' => auth()->id(),
                    'amount' => $debt->remaining_amount,
                    'payment_method' => $request->payment_method,
                    'payment_date' => now()->toDateString(),
                    'notes' => 'Pagamento final - quitação completa'
                ]);
            }

            $debt->update([
                'status' => 'paid',
                'remaining_amount' => 0
            ]);

            $sale = null;
            if ($debt->isProductDebt() && ($request->create_sale ?? true)) {
                $sale = $this->createSaleFromDebt($debt);
            }

            DB::commit();

            $message = $sale
                ? "Dívida quitada! Venda #{$sale->id} criada."
                : 'Dívida quitada com sucesso!';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao marcar como paga: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao quitar dívida.'
            ], 500);
        }
    }

    /**
     * Atualizar dívida
     */
    public function update(Request $request, Debt $debt)
    {
        $baseRules = [
            'due_date' => 'nullable|date|after_or_equal:debt_date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ];

        if ($debt->isProductDebt()) {
            $specificRules = [
                'customer_name' => 'required|string|max:100',
                'customer_phone' => 'nullable|string|max:20',
                'customer_document' => 'nullable|string|max:20',
            ];
        } else {
            $specificRules = [
                'employee_name' => 'required|string|max:100',
                'employee_phone' => 'nullable|string|max:20',
                'employee_document' => 'nullable|string|max:20',
            ];
        }

        $validated = $request->validate(array_merge($baseRules, $specificRules));

        if (!$debt->canBeEdited()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta dívida não pode ser editada.'
            ], 400);
        }

        try {
            $debt->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Dívida atualizada com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar dívida.'
            ], 500);
        }
    }

    /**
     * Criar venda manual
     */
    public function createManualSale(Debt $debt)
    {
        if (!$debt->isProductDebt() || $debt->status !== 'paid' || $debt->generated_sale_id) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível criar venda para esta dívida.'
            ], 400);
        }

        try {
            DB::beginTransaction();
            
            $sale = $this->createSaleFromDebt($debt);
            
            DB::commit();

            if ($sale) {
                return response()->json([
                    'success' => true,
                    'message' => "Venda #{$sale->id} criada com sucesso!",
                    'sale_id' => $sale->id,
                    'redirect' => route('sales.show', $sale)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar venda.'
            ], 500);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar venda manual: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Relatório de devedores - SIMPLIFICADO
     */
    public function debtorsReport(Request $request)
    {
        try {
            $query = Debt::whereNotIn('status', ['paid', 'cancelled']);

            if ($request->filled('debt_type')) {
                $query->where('debt_type', $request->debt_type);
            }

            if ($request->filled('customer')) {
                $search = '%' . $request->customer . '%';
                $query->where(function($q) use ($search) {
                    $q->where('customer_name', 'like', $search)
                      ->orWhere('employee_name', 'like', $search);
                });
            }

            // Agrupar por devedor
            $debtors = $query->get()->groupBy(function($debt) {
                return $debt->isProductDebt() ? $debt->customer_name : $debt->employee_name;
            })->map(function($debts) {
                $first = $debts->first();
                return (object)[
                    'debt_type' => $first->debt_type,
                    'debtor_name' => $first->isProductDebt() ? $first->customer_name : $first->employee_name,
                    'debtor_phone' => $first->isProductDebt() ? $first->customer_phone : $first->employee_phone,
                    'total_debt' => $debts->sum('remaining_amount'),
                    'debt_count' => $debts->count(),
                    'oldest_debt' => $debts->min('debt_date'),
                    'status_group' => $debts->contains(fn($d) => $d->is_overdue) ? 'Vencida' : 'Ativa'
                ];
            })->sortByDesc('total_debt')->values();

            // Paginar manualmente
            $page = $request->get('page', 1);
            $perPage = 20;
            $offset = ($page - 1) * $perPage;

            $debtors = new \Illuminate\Pagination\LengthAwarePaginator(
                $debtors->slice($offset, $perPage)->values(),
                $debtors->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return view('debts.debtors-report', compact('debtors'));

        } catch (\Exception $e) {
            Log::error('Erro no relatório: ' . $e->getMessage());
            return back()->with('error', 'Erro ao gerar relatório.');
        }
    }

    /**
     * Visualizar detalhes (AJAX)
     */
    public function showDetails(Debt $debt)
    {
        try {
            $debt->load([
                'user',
                'employee',
                'items.product.category',
                'payments.user',
                'generatedSale'
            ]);

            $html = view('debts.partials.details', compact('debt'))->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao carregar detalhes: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar detalhes.'
            ], 500);
        }
    }

    /**
     * Dados para edição (AJAX)
     */
    public function editData(Debt $debt)
    {
        try {
            $debt->load(['items.product', 'employee']);

            return response()->json([
                'success' => true,
                'data' => $debt
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dados.'
            ], 500);
        }
    }
}