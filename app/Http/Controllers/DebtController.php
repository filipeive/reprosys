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

class DebtController extends Controller
{
    /**
     * Exibir lista de dívidas
     */
    public function index(Request $request)
    {
        $query = Debt::with(['user', 'employee', 'payments', 'items.product'])
            ->latest();

        // Filtros
        if ($request->filled('debt_type')) {
            $query->where('debt_type', $request->debt_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer')) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->customer . '%')
                  ->orWhere('employee_name', 'like', '%' . $request->customer . '%');
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

        $debts = $query->paginate(15);

        // Estatísticas separadas por tipo
        $stats = [
            // Estatísticas gerais
            'total_active' => Debt::where('status', 'active')->sum('remaining_amount'),
            'total_overdue' => Debt::where('status', 'active')
                                  ->where('due_date', '<', now()->toDateString())
                                  ->sum('remaining_amount'),
            'count_active' => Debt::where('status', 'active')->count(),
            'count_paid_this_month' => Debt::where('status', 'paid')
                ->whereMonth('updated_at', now()->month)
                ->count(),
            
            // Estatísticas de produtos
            'product_debts' => [
                'total_active' => Debt::productDebts()->where('status', 'active')->sum('remaining_amount'),
                'count_active' => Debt::productDebts()->where('status', 'active')->count(),
                'total_overdue' => Debt::productDebts()->where('status', 'active')
                                      ->where('due_date', '<', now()->toDateString())
                                      ->sum('remaining_amount'),
            ],
            
            // Estatísticas de dinheiro (funcionários)
            'money_debts' => [
                'total_active' => Debt::moneyDebts()->where('status', 'active')->sum('remaining_amount'),
                'count_active' => Debt::moneyDebts()->where('status', 'active')->count(),
                'total_overdue' => Debt::moneyDebts()->where('status', 'active')
                                     ->where('due_date', '<', now()->toDateString())
                                     ->sum('remaining_amount'),
            ]
        ];

        $products = Product::where('is_active', true)->get();
        $employees = User::where('is_active', true)->orderBy('name')->get();

        return view('debts.index', compact('debts', 'stats', 'products', 'employees'));
    }

    /**
     * Criar nova dívida
     */
    public function store(Request $request)
    {
        // Validação base
        $baseRules = [
            'debt_type' => 'required|in:product,money',
            'debt_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:debt_date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'initial_payment' => 'nullable|numeric|min:0',
        ];

        // Validação específica por tipo
        if ($request->debt_type === 'product') {
            $specificRules = [
                'customer_name' => 'required|string|max:100',
                'customer_phone' => 'nullable|string|max:20',
                'customer_document' => 'nullable|string|max:20',
                'products' => 'required|string', // JSON dos produtos
            ];
        } else {
            $specificRules = [
                'employee_id' => 'required|exists:users,id',
                'employee_name' => 'required|string|max:100',
                'employee_phone' => 'nullable|string|max:20',
                'employee_document' => 'nullable|string|max:20',
                'amount' => 'required|numeric|min:0.01', // Valor da dívida de dinheiro
            ];
        }

        $request->validate(array_merge($baseRules, $specificRules));

        try {
            if ($request->debt_type === 'product') {
                return $this->storeProductDebt($request);
            } else {
                return $this->storeMoneyDebt($request);
            }
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
     * Criar dívida a partir de uma venda (carrinho de vendas)
     */
    public function storeFromSale(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'debt_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:debt_date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
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

            $debt = DB::transaction(function () use ($request, $products) {
                // Validar produtos e estoque
                $totalAmount = 0;
                $validatedProducts = [];
                
                foreach ($products as $productData) {
                    if (!isset($productData['product_id']) || !isset($productData['quantity']) || !isset($productData['unit_price'])) {
                        throw new \Exception('Dados de produto incompletos.');
                    }
                    
                    $product = Product::find($productData['product_id']);
                    if (!$product) {
                        throw new \Exception("Produto não encontrado: ID {$productData['product_id']}");
                    }

                    // Para produtos físicos, verificar estoque
                    if ($product->type === 'product' && $product->stock_quantity < $productData['quantity']) {
                        throw new \Exception("Estoque insuficiente para o produto: {$product->name}. Disponível: {$product->stock_quantity}");
                    }

                    $validatedProducts[] = [
                        'product' => $product,
                        'quantity' => (int)$productData['quantity'],
                        'unit_price' => (float)$productData['unit_price']
                    ];

                    $totalAmount += $productData['quantity'] * $productData['unit_price'];
                }

                // Criar a dívida
                $debt = Debt::create([
                    'debt_type' => 'product',
                    'user_id' => auth()->id(),
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'original_amount' => $totalAmount,
                    'remaining_amount' => $totalAmount,
                    'debt_date' => $request->debt_date,
                    'due_date' => $request->due_date ?: now()->addDays(30)->toDateString(),
                    'status' => 'active',
                    'description' => $request->description,
                    'notes' => $request->notes
                ]);

                // Adicionar itens da dívida e movimentar estoque
                foreach ($validatedProducts as $item) {
                    $subtotal = $item['quantity'] * $item['unit_price'];

                    // Criar item da dívida
                    DebtItem::create([
                        'debt_id' => $debt->id,
                        'product_id' => $item['product']->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $subtotal
                    ]);

                    // Movimentar estoque apenas para produtos físicos
                    if ($item['product']->type === 'product') {
                        $item['product']->decrement('stock_quantity', $item['quantity']);

                        // Registrar movimento de estoque
                        StockMovement::create([
                            'product_id' => $item['product']->id,
                            'user_id' => auth()->id(),
                            'movement_type' => 'out',
                            'quantity' => $item['quantity'],
                            'reason' => 'Venda convertida em dívida',
                            'reference_id' => $debt->id,
                            'movement_date' => $request->debt_date
                        ]);
                    }
                }

                return $debt;
            });

            return response()->json([
                'success' => true,
                'message' => 'Dívida criada com sucesso! Estoque atualizado.',
                'debt_id' => $debt->id,
                'redirect' => route('debts.show', $debt)
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos fornecidos.',
                'errors' => $e->validator->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Erro ao criar dívida a partir de venda: ' . $e->getMessage(), [
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
     * Criar dívida de produtos
     */
    private function storeProductDebt(Request $request)
    {
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
        }

        $debt = DB::transaction(function () use ($request, $products) {
            // Validar estoque disponível
            foreach ($products as $productData) {
                $product = Product::find($productData['product_id']);
                
                if (!$product) {
                    throw new \Exception("Produto não encontrado: ID {$productData['product_id']}");
                }

                if ($product->type === 'product' && $product->stock_quantity < $productData['quantity']) {
                    throw new \Exception("Estoque insuficiente para o produto: {$product->name}");
                }
            }

            // Calcular total
            $totalAmount = 0;
            foreach ($products as $productData) {
                $totalAmount += $productData['quantity'] * $productData['unit_price'];
            }

            // Criar a dívida
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

            // Adicionar itens da dívida e movimentar estoque
            foreach ($products as $productData) {
                $product = Product::find($productData['product_id']);
                $subtotal = $productData['quantity'] * $productData['unit_price'];

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

                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'movement_type' => 'out',
                        'quantity' => $productData['quantity'],
                        'reason' => 'Dívida de produtos',
                        'reference_id' => $debt->id,
                        'movement_date' => $request->debt_date
                    ]);
                }
            }

            // Processar pagamento inicial se houver
            $this->processInitialPayment($debt, $request);

            return $debt;
        });

        return response()->json([
            'success' => true,
            'message' => 'Dívida de produtos criada com sucesso!',
            'redirect' => route('debts.show', $debt)
        ]);
    }

    /**
     * Criar dívida de dinheiro
     */
    private function storeMoneyDebt(Request $request)
    {
        $debt = DB::transaction(function () use ($request) {
            // Buscar dados do funcionário
            $employee = User::find($request->employee_id);
            
            if (!$employee) {
                throw new \Exception('Funcionário não encontrado.');
            }

            $totalAmount = $request->amount;

            // Criar a dívida
            $debt = Debt::create([
                'debt_type' => 'money',
                'user_id' => auth()->id(),
                'employee_id' => $employee->id,
                'employee_name' => $request->employee_name ?: $employee->name,
                'employee_phone' => $request->employee_phone ?: $employee->phone,
                'employee_document' => $request->employee_document,
                'original_amount' => $totalAmount,
                'remaining_amount' => $totalAmount,
                'debt_date' => $request->debt_date,
                'due_date' => $request->due_date ?: now()->addDays(30)->toDateString(),
                'status' => 'active',
                'description' => $request->description,
                'notes' => $request->notes
            ]);

            // Processar pagamento inicial se houver
            $this->processInitialPayment($debt, $request);

            return $debt;
        });

        return response()->json([
            'success' => true,
            'message' => 'Dívida de dinheiro criada com sucesso!',
            'redirect' => route('debts.show', $debt)
        ]);
    }

    /**
     * Processar pagamento inicial
     */
    private function processInitialPayment(Debt $debt, Request $request)
    {
        if ($request->filled('initial_payment') && $request->initial_payment > 0) {
            $initialPayment = min($request->initial_payment, $debt->original_amount);
            
            DebtPayment::create([
                'debt_id' => $debt->id,
                'user_id' => auth()->id(),
                'amount' => $initialPayment,
                'payment_method' => 'cash',
                'payment_date' => $request->debt_date,
                'notes' => 'Pagamento inicial (entrada)'
            ]);

            // Atualizar valor restante
            $debt->remaining_amount = $debt->original_amount - $initialPayment;
            if ($debt->remaining_amount <= 0) {
                $debt->status = 'paid';
                
                // Criar venda se for dívida de produtos
                if ($debt->isProductDebt()) {
                    $this->createSaleFromPaidDebt($debt);
                }
            }
            $debt->save();
        }
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
            'notes' => 'nullable|string|max:500',
            'create_sale' => 'sometimes|boolean' // Opção para criar venda
        ]);

        if (!$debt->canReceivePayment()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta dívida não pode receber pagamentos.'
            ], 400);
        }

        try {
            DB::transaction(function () use ($debt, $request) {
                // Criar pagamento
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
                    
                    // Criar venda automática se for dívida de produtos
                    if ($debt->isProductDebt() && ($request->create_sale ?? true)) {
                        $this->createSaleFromPaidDebt($debt);
                    }
                }
                
                $debt->save();
            });

            $message = $debt->status === 'paid'
                ? ($debt->isProductDebt() 
                   ? 'Dívida quitada com sucesso! Venda registrada automaticamente.'
                   : 'Dívida quitada com sucesso!')
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

    /* *
     * Criar venda automática quando dívida de produtos é totalmente paga
     */
    /*private function createSaleFromPaidDebt(Debt $debt)
    {
        if (!$debt->isProductDebt() || !$debt->hasItems()) {
            return null;
        }

        try {
            $sale = DB::transaction(function () use ($debt) {
                $sale = Sale::create([
                    'user_id' => $debt->user_id,
                    'customer_name' => $debt->customer_name,
                    'customer_phone' => $debt->customer_phone,
                    'subtotal' => $debt->original_amount,
                    'total_amount' => $debt->original_amount,
                    'payment_method' => 'mixed', // Múltiplas formas de pagamento
                    'sale_date' => now(),
                    'notes' => "Venda gerada automaticamente da dívida #{$debt->id} - {$debt->description}",
                ]);

                // Copiar itens da dívida para a venda
                foreach ($debt->items as $debtItem) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $debtItem->product_id,
                        'quantity' => $debtItem->quantity,
                        'original_unit_price' => $debtItem->unit_price,
                        'unit_price' => $debtItem->unit_price,
                        'total_price' => $debtItem->total_price
                    ]);
                }

                // Atualizar referência na dívida
                $debt->update(['generated_sale_id' => $sale->id]);

                return $sale;
            });

            Log::info("Venda #{$sale->id} criada automaticamente da dívida #{$debt->id}");
            return $sale;
            
        } catch (\Exception $e) {
            Log::error("Erro ao criar venda da dívida #{$debt->id}: " . $e->getMessage());
            return null;
        }
    }*/

    public function cancel(Debt $debt)
    {
        if (!$debt->canBeCancelled()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta dívida não pode ser cancelada.'
                ], 400);
            }

            return redirect()->back()->with('error', 'Esta dívida não pode ser cancelada.');
        }

        try {
            DB::transaction(function () use ($debt) {
                // Para dívidas de produtos, devolver estoque
                if ($debt->isProductDebt()) {
                    foreach ($debt->items as $item) {
                        if ($item->product->type === 'product') {
                            $item->product->increment('stock_quantity', $item->quantity);

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
                }

                $debt->update(['status' => 'cancelled']);
            });

            $message = $debt->isProductDebt()
                ? 'Dívida cancelada e estoque devolvido com sucesso!'
                : 'Dívida cancelada com sucesso!';

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erro ao cancelar dívida: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao cancelar dívida.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Erro ao cancelar dívida.');
        }
    }


    /**
     * Mostrar detalhes da dívida
     */
    public function show(Debt $debt)
    {   
        $products = Product::where('is_active', true)->get();
        $employees = User::where('is_active', true)->orderBy('name')->get();
        $debt->load(['user', 'employee', 'items.product.category', 'payments.user', 'generatedSale']);
        
        return view('debts.show', compact('debt', 'products', 'employees'));
    }

    /**
     * Mostrar detalhes da dívida (para offcanvas)
     */
    public function showDetails(Debt $debt)
    {
        $debt->load(['user', 'employee', 'items.product.category', 'payments.user', 'generatedSale']);

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
        $debt->load(['items.product', 'employee']);
        
        return response()->json([
            'success' => true,
            'data' => $debt
        ]);
    }

    /**
     * Atualizar dívida
     */
    public function update(Request $request, Debt $debt)
    {
        // Validação base
        $baseRules = [
            'due_date' => 'nullable|date|after_or_equal:debt_date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ];

        // Validação específica por tipo
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

        $request->validate(array_merge($baseRules, $specificRules));

        if (!$debt->canBeEdited()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta dívida não pode ser editada.'
                ], 400);
            }

            return redirect()->back()->with('error', 'Esta dívida não pode ser editada.');
        }

        try {
            DB::transaction(function () use ($debt, $request) {
                $updateData = [
                    'due_date' => $request->due_date,
                    'description' => $request->description,
                    'notes' => $request->notes
                ];

                if ($debt->isProductDebt()) {
                    $updateData = array_merge($updateData, [
                        'customer_name' => $request->customer_name,
                        'customer_phone' => $request->customer_phone,
                        'customer_document' => $request->customer_document,
                    ]);
                } else {
                    $updateData = array_merge($updateData, [
                        'employee_name' => $request->employee_name,
                        'employee_phone' => $request->employee_phone,
                        'employee_document' => $request->employee_document,
                    ]);
                }

                $debt->update($updateData);
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
     * Marcar como paga (com opção de criar venda)
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
            DB::transaction(function () use ($debt, $request) {
                // Criar pagamento final se necessário
                if ($debt->remaining_amount > 0) {
                    $debt->payments()->create([
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

                // Criar venda automática se solicitado e for dívida de produtos
                if ($debt->isProductDebt() && ($request->create_sale ?? true)) {
                    $this->createSaleFromPaidDebt($debt);
                }
            });

            $message = $debt->isProductDebt() && ($request->create_sale ?? true)
                ? 'Dívida quitada e venda registrada com sucesso!'
                : 'Dívida quitada com sucesso!';

            return response()->json([
                'success' => true,
                'message' => $message
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
     * Relatório de devedores (corrigido)
     */
    public function debtorsReport(Request $request)
    {
        try {
            $query = Debt::whereNotIn('status', ['paid', 'cancelled']);

            // Filtro por tipo de dívida
            if ($request->filled('debt_type')) {
                $query->where('debt_type', $request->debt_type);
            }

            // Filtro por nome (busca tanto em customer_name quanto employee_name)
            if ($request->filled('customer')) {
                $search = '%' . $request->customer . '%';
                $query->where(function($q) use ($search) {
                    $q->where('customer_name', 'like', $search)
                    ->orWhere('employee_name', 'like', $search);
                });
            }

            // Filtro por status
            if ($request->filled('status')) {
                if ($request->status === 'overdue') {
                    $query->where('status', 'active')
                        ->where('due_date', '<', now()->toDateString());
                } else {
                    $query->where('status', $request->status);
                }
            }

            // Construir query otimizada baseada no tipo de dívida
            if ($request->debt_type === 'money') {
                // Apenas dívidas de funcionários
                $debtors = $query->selectRaw('
                    debt_type,
                    employee_name as debtor_name, 
                    employee_phone as debtor_phone, 
                    SUM(remaining_amount) as total_debt, 
                    COUNT(*) as debt_count, 
                    MIN(debt_date) as oldest_debt,
                    CASE 
                        WHEN MAX(CASE WHEN due_date < CURDATE() THEN 1 ELSE 0 END) = 1 THEN "Vencida"
                        ELSE "Ativa"
                    END as status_group
                ')
                ->where('debt_type', 'money')
                ->groupBy('debt_type', 'employee_name', 'employee_phone')
                ->orderByDesc('total_debt')
                ->paginate(20);

            } elseif ($request->debt_type === 'product') {
                // Apenas dívidas de clientes
                $debtors = $query->selectRaw('
                    debt_type,
                    customer_name as debtor_name, 
                    customer_phone as debtor_phone, 
                    SUM(remaining_amount) as total_debt, 
                    COUNT(*) as debt_count, 
                    MIN(debt_date) as oldest_debt,
                    CASE 
                        WHEN MAX(CASE WHEN due_date < CURDATE() THEN 1 ELSE 0 END) = 1 THEN "Vencida"
                        ELSE "Ativa"
                    END as status_group
                ')
                ->where('debt_type', 'product')
                ->groupBy('debt_type', 'customer_name', 'customer_phone')
                ->orderByDesc('total_debt')
                ->paginate(20);

            } else {
                // Query combinada (mais simples para evitar timeout)
                $productDebts = $query->where('debt_type', 'product')
                    ->selectRaw('
                        debt_type,
                        customer_name as debtor_name, 
                        customer_phone as debtor_phone, 
                        SUM(remaining_amount) as total_debt, 
                        COUNT(*) as debt_count, 
                        MIN(debt_date) as oldest_debt,
                        CASE 
                            WHEN MAX(CASE WHEN due_date < CURDATE() THEN 1 ELSE 0 END) = 1 THEN "Vencida"
                            ELSE "Ativa"
                        END as status_group
                    ')
                    ->groupBy('debt_type', 'customer_name', 'customer_phone')
                    ->get();

                $moneyDebts = $query->where('debt_type', 'money')
                    ->selectRaw('
                        debt_type,
                        employee_name as debtor_name, 
                        employee_phone as debtor_phone, 
                        SUM(remaining_amount) as total_debt, 
                        COUNT(*) as debt_count, 
                        MIN(debt_date) as oldest_debt,
                        CASE 
                            WHEN MAX(CASE WHEN due_date < CURDATE() THEN 1 ELSE 0 END) = 1 THEN "Vencida"
                            ELSE "Ativa"
                        END as status_group
                    ')
                    ->groupBy('debt_type', 'employee_name', 'employee_phone')
                    ->get();

                // Combinar resultados
                $allDebtors = $productDebts->concat($moneyDebts)
                    ->sortByDesc('total_debt')
                    ->values();

                // Paginar manualmente
                $page = $request->get('page', 1);
                $perPage = 20;
                $offset = ($page - 1) * $perPage;
                
                $debtors = new \Illuminate\Pagination\LengthAwarePaginator(
                    $allDebtors->slice($offset, $perPage)->values(),
                    $allDebtors->count(),
                    $perPage,
                    $page,
                    ['path' => request()->url(), 'query' => request()->query()]
                );
            }

            return view('debts.debtors-report', compact('debtors'));

        } catch (\Exception $e) {
            Log::error('Erro no relatório de devedores: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 
                'Erro ao gerar relatório. Tente filtrar por tipo específico ou contate o suporte.'
            );
        }
    }

    /**
     * Exportar relatório de devedores
     */
    public function exportDebtorsReport(Request $request)
    {
        try {
            // Reutilizar mesma lógica do relatório mas sem paginação
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

            $debtors = collect();

            if ($request->debt_type === 'money') {
                $debtors = $query->selectRaw('
                    debt_type,
                    employee_name as debtor_name, 
                    employee_phone as debtor_phone, 
                    SUM(remaining_amount) as total_debt, 
                    COUNT(*) as debt_count, 
                    MIN(debt_date) as oldest_debt
                ')
                ->where('debt_type', 'money')
                ->groupBy('debt_type', 'employee_name', 'employee_phone')
                ->orderByDesc('total_debt')
                ->get();

            } elseif ($request->debt_type === 'product') {
                $debtors = $query->selectRaw('
                    debt_type,
                    customer_name as debtor_name, 
                    customer_phone as debtor_phone, 
                    SUM(remaining_amount) as total_debt, 
                    COUNT(*) as debt_count, 
                    MIN(debt_date) as oldest_debt
                ')
                ->where('debt_type', 'product')
                ->groupBy('debt_type', 'customer_name', 'customer_phone')
                ->orderByDesc('total_debt')
                ->get();
            }

            // Criar CSV
            $filename = 'relatorio-devedores-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($debtors) {
                $file = fopen('php://output', 'w');
                
                // Header do CSV
                fputcsv($file, [
                    'Tipo', 'Nome do Devedor', 'Telefone', 
                    'Número de Dívidas', 'Valor Total', 'Dívida Mais Antiga'
                ]);

                // Dados
                foreach ($debtors as $debtor) {
                    fputcsv($file, [
                        $debtor->debt_type === 'product' ? 'Produtos' : 'Dinheiro',
                        $debtor->debtor_name,
                        $debtor->debtor_phone ?: 'Não informado',
                        $debtor->debt_count,
                        'MT ' . number_format($debtor->total_debt, 2, ',', '.'),
                        \Carbon\Carbon::parse($debtor->oldest_debt)->format('d/m/Y')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Erro ao exportar relatório: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao exportar relatório.'
            ], 500);
        }
    }
    /**
     * API para buscar funcionários
     */
    public function searchEmployees(Request $request)
    {
        $search = $request->get('q', '');
        
        $employees = User::where('is_active', true)
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->select('id', 'name', 'email', 'phone')
            ->limit(10)
            ->get();

        return response()->json($employees);
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

    /**
     * Criar venda manual a partir de dívida paga
     */
    public function createManualSale(Debt $debt)
    {
        try {
            // Verificar se pode criar venda
            if (!$debt->isProductDebt()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Apenas dívidas de produtos podem gerar vendas.'
                ], 400);
            }

            if ($debt->status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Apenas dívidas pagas podem gerar vendas.'
                ], 400);
            }

            if ($debt->generated_sale_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta dívida já possui uma venda associada.'
                ], 400);
            }

            // Verificar se tem itens
            $debt->load(['items.product']);
            if (!$debt->items || $debt->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta dívida não possui itens para criar uma venda.'
                ], 400);
            }

            // Criar a venda
            $sale = $this->createSaleFromPaidDebt($debt);
            
            if ($sale) {
                return response()->json([
                    'success' => true,
                    'message' => 'Venda criada com sucesso!',
                    'sale_id' => $sale->id,
                    'redirect' => route('sales.show', $sale)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar venda. Tente novamente.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Erro ao criar venda manual da dívida: ' . $e->getMessage(), [
                'debt_id' => $debt->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método helper melhorado para criar venda a partir de dívida paga
     */
    private function createSaleFromPaidDebt(Debt $debt)
    {
        if (!$debt->isProductDebt() || !$debt->items || $debt->items->isEmpty()) {
            return null;
        }

        try {
            return DB::transaction(function () use ($debt) {
                // Criar a venda
                $sale = Sale::create([
                    'user_id' => auth()->id(), // Usuário atual, não o original
                    'customer_name' => $debt->customer_name,
                    'customer_phone' => $debt->customer_phone,
                    'subtotal' => $debt->original_amount,
                    'total_amount' => $debt->original_amount,
                    'payment_method' => 'mixed', // Múltiplas formas de pagamento
                    'sale_date' => now(),
                    'notes' => "Venda gerada manualmente da dívida #{$debt->id} - {$debt->description}",
                ]);

                // Copiar itens da dívida para a venda
                foreach ($debt->items as $debtItem) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $debtItem->product_id,
                        'quantity' => $debtItem->quantity,
                        'original_unit_price' => $debtItem->unit_price,
                        'unit_price' => $debtItem->unit_price,
                        'total_price' => $debtItem->total_price
                    ]);
                }

                // Atualizar referência na dívida
                $debt->update(['generated_sale_id' => $sale->id]);

                Log::info("Venda #{$sale->id} criada manualmente da dívida #{$debt->id} pelo usuário " . auth()->id());
                
                return $sale;
            });
            
        } catch (\Exception $e) {
            Log::error("Erro ao criar venda da dívida #{$debt->id}: " . $e->getMessage(), [
                'debt_id' => $debt->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}