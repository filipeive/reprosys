<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SaleController extends Controller
{
    protected $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    public function index(Request $request)
    {
        $query = Sale::with(['user', 'items.product']);
        
        // Filtros existentes mantidos...
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', '%' . $search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }
        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Novo filtro: vendas com desconto
        if ($request->filled('has_discount')) {
            if ($request->has_discount === '1') {
                $query->where('discount_amount', '>', 0);
            } elseif ($request->has_discount === '0') {
                $query->where('discount_amount', '=', 0);
            }
        }
        
        $sales = $query->latest('sale_date')->paginate(8);
        
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
                          ->with('category')
                          ->orderBy('name')
                          ->get();
        
        return view('sales.create', compact('products'));
    }

    public function manualCreate()
    {
        $products = Product::where('is_active', true)
                          ->with('category')
                          ->orderBy('name')
                          ->get();
        
        return view('sales.manual-create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|in:cash,card,transfer,credit',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|string',
            'sale_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'general_discount' => 'nullable|numeric|min:0',
            'general_discount_type' => 'nullable|in:fixed,percentage',
            'general_discount_reason' => 'nullable|string|max:255',
        ]);

        try {
            $items = json_decode($request->items, true);

            if (!$items || !is_array($items) || empty($items)) {
                return redirect()->back()
                    ->with('error', 'Nenhum item foi adicionado ao carrinho.')
                    ->withInput();
            }

            // Validar estrutura dos items
            foreach ($items as $index => $item) {
                if (!isset($item['product_id']) || !isset($item['quantity']) || !isset($item['unit_price'])) {
                    return redirect()->back()
                        ->with('error', "Dados do item #{$index} inválidos.")
                        ->withInput();
                }
                
                if ($item['quantity'] <= 0) {
                    return redirect()->back()
                        ->with('error', 'Quantidade deve ser maior que zero.')
                        ->withInput();
                }

                if ($item['unit_price'] < 0) {
                    return redirect()->back()
                        ->with('error', 'Preço unitário não pode ser negativo.')
                        ->withInput();
                }
            }

            DB::transaction(function () use ($validated, $items, $request) {
                // Determinar data da venda
                $saleDate = array_key_exists('sale_date', $validated) && $validated['sale_date']
                    ? Carbon::parse($validated['sale_date'])
                    : now();

                // Determinar usuário
                $userId = $validated['user_id'] ?? auth()->id();

                // Criar venda com campos de desconto
                $sale = Sale::create([
                    'user_id' => $userId,
                    'customer_name' => $validated['customer_name'] ?: 'Cliente Avulso',
                    'customer_phone' => $validated['customer_phone'],
                    'payment_method' => $validated['payment_method'],
                    'notes' => $validated['notes'],
                    'sale_date' => $saleDate,
                    'subtotal' => 0,
                    'discount_amount' => 0,
                    'total_amount' => 0,
                    'discount_type' => $validated['general_discount_type'] ?? null,
                    'discount_reason' => $validated['general_discount_reason'] ?? null,
                ]);

                $subtotal = 0;
                $totalDiscountAmount = 0;

                foreach ($items as $item) {
                    $product = Product::find($item['product_id']);

                    if (!$product) {
                        throw new \Exception("Produto não encontrado: {$item['product_id']}");
                    }

                    // Verificar stock apenas para produtos físicos
                    if ($product->type === 'product') {
                        if ($product->stock_quantity < $item['quantity']) {
                            throw new \Exception("Stock insuficiente para {$product->name}. Disponível: {$product->stock_quantity}");
                        }
                    }

                    // Preço original e preço de venda
                    $originalUnitPrice = $product->selling_price;
                    $saleUnitPrice = (float) $item['unit_price'];
                    $quantity = $item['quantity'];

                    // Calcular desconto por item
                    $itemDiscountAmount = ($originalUnitPrice - $saleUnitPrice) * $quantity;
                    $itemDiscountPercentage = $originalUnitPrice > 0 ? 
                        (($originalUnitPrice - $saleUnitPrice) / $originalUnitPrice) * 100 : 0;

                    // Criar item da venda com informações completas de desconto
                    $saleItem = SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $quantity,
                        'original_unit_price' => $originalUnitPrice,
                        'unit_price' => $saleUnitPrice,
                        'total_price' => $saleUnitPrice * $quantity,
                        'discount_amount' => max(0, $itemDiscountAmount),
                        'discount_percentage' => $itemDiscountAmount > 0 ? $itemDiscountPercentage : null,
                        'discount_type' => $itemDiscountAmount > 0 ? 'item_level' : null,
                        'discount_reason' => $itemDiscountAmount > 0 ? 'Desconto aplicado na venda' : null,
                    ]);

                    // Atualizar stock apenas para produtos físicos
                    if ($product->type === 'product') {
                        $product->decrement('stock_quantity', $quantity);

                        // Registrar movimento de stock
                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => $userId,
                            'movement_type' => 'out',
                            'quantity' => $quantity,
                            'reason' => 'Venda',
                            'reference_id' => $sale->id,
                            'movement_date' => $saleDate->toDateString(),
                        ]);
                    }

                    $subtotal += $originalUnitPrice * $quantity;
                    $totalDiscountAmount += max(0, $itemDiscountAmount);
                }

                // Aplicar desconto geral se fornecido
                $generalDiscount = 0;
                if (isset($validated['general_discount']) && $validated['general_discount'] > 0) {
                    if ($validated['general_discount_type'] === 'percentage') {
                        $generalDiscount = ($subtotal * $validated['general_discount']) / 100;
                    } else {
                        $generalDiscount = $validated['general_discount'];
                    }
                    $totalDiscountAmount += $generalDiscount;
                }

                // Calcular totais finais
                $finalTotal = $subtotal - $totalDiscountAmount;

                // Atualizar venda com totais calculados
                $sale->update([
                    'subtotal' => $subtotal,
                    'discount_amount' => $totalDiscountAmount,
                    'total_amount' => $finalTotal,
                    'discount_percentage' => $subtotal > 0 ? ($totalDiscountAmount / $subtotal) * 100 : null,
                ]);

                // Log detalhado
                if ($totalDiscountAmount > 0) {
                    Log::info("Venda #{$sale->id} - Descontos aplicados", [
                        'subtotal' => $subtotal,
                        'desconto_total' => $totalDiscountAmount,
                        'total_final' => $finalTotal,
                        'percentual_desconto' => $sale->getTotalDiscountPercentage(),
                    ]);
                }
            });

            $isManualSale = $request->has('sale_date') && $request->sale_date !== now()->format('Y-m-d\TH:i');
            $successMessage = $isManualSale 
                ? 'Venda manual registrada com sucesso.' 
                : 'Venda registrada com sucesso.';

            return redirect()->route('sales.index')
                ->with('success', $successMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                        ->withErrors($e->errors())
                        ->withInput()
                        ->with('error', 'Erro de validação. Verifique os dados informados.');
        } catch (\Exception $e) {
            Log::error('Erro ao criar venda: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao processar venda: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Aplicar desconto a uma venda existente
     */
    public function applyDiscount(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'discount_value' => 'required|numeric|min:0',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_reason' => 'nullable|string|max:255',
            'item_id' => 'nullable|exists:sale_items,id',
        ]);

        try {
            if ($request->item_id) {
                // Aplicar desconto a item específico
                $item = SaleItem::findOrFail($request->item_id);
                $this->discountService->applyItemDiscount(
                    $item,
                    $validated['discount_value'],
                    $validated['discount_type'],
                    $validated['discount_reason']
                );
                $message = 'Desconto aplicado ao item com sucesso.';
            } else {
                // Aplicar desconto geral à venda
                $this->discountService->applyGeneralDiscount(
                    $sale,
                    $validated['discount_value'],
                    $validated['discount_type'],
                    $validated['discount_reason']
                );
                $message = 'Desconto geral aplicado à venda com sucesso.';
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erro ao aplicar desconto: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao aplicar desconto.');
        }
    }

    /**
     * Remover desconto de uma venda ou item
     */
    public function removeDiscount(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'item_id' => 'nullable|exists:sale_items,id',
        ]);

        try {
            if ($request->item_id) {
                // Remover desconto de item específico
                $item = SaleItem::findOrFail($request->item_id);
                $this->discountService->removeItemDiscount($item);
                $message = 'Desconto removido do item com sucesso.';
            } else {
                // Remover desconto geral da venda
                $sale->update([
                    'discount_amount' => $sale->items->sum('discount_amount'),
                    'discount_percentage' => null,
                    'discount_type' => null,
                    'discount_reason' => null,
                ]);
                $sale->calculateTotals();
                $message = 'Desconto geral removido da venda com sucesso.';
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erro ao remover desconto: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao remover desconto.');
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.product.category']);
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $sale->load(['items.product']);
        $products = Product::where('is_active', true)
                          ->with('category')
                          ->orderBy('name')
                          ->get();
        
        return view('sales.edit', compact('sale', 'products'));
    }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|in:cash,card,transfer,credit',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $sale->update($validated);

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Venda atualizada com sucesso.');
                
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar venda: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erro ao atualizar venda.')
                ->withInput();
        }
    }

    public function destroy(Sale $sale)
    {
        try {
            DB::transaction(function () use ($sale) {
                // Reverter movimentações de stock
                foreach ($sale->items as $item) {
                    if ($item->product->type === 'product') {
                        $item->product->increment('stock_quantity', $item->quantity);
                        
                        // Registrar movimento de reversão
                        StockMovement::create([
                            'product_id' => $item->product_id,
                            'user_id' => auth()->id(),
                            'movement_type' => 'in',
                            'quantity' => $item->quantity,
                            'reason' => 'Reversão de venda cancelada',
                            'reference_id' => $sale->id,
                            'movement_date' => now()->toDateString(),
                        ]);
                    }
                }
                
                // Deletar itens da venda
                $sale->items()->delete();
                
                // Deletar a venda
                $sale->delete();
            });

            return redirect()->route('sales.index')
                ->with('success', 'Venda cancelada com sucesso. Stock restaurado.');
                
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar venda: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erro ao cancelar venda.');
        }
    }

    public function quickView(Sale $sale)
    {
        try {
            // Verificar permissões
            if (!userCan('view_sales') && (!userCan('view_own_sales') || $sale->user_id !== auth()->id())) {
                return response()->json(['error' => 'Acesso negado.'], 403);
            }

            // Carregar relações
            $sale->load([
                'user',
                'items.product.category',
                'items' => function ($query) {
                    $query->orderBy('created_at', 'asc');
                }
            ]);

            // Preparar dados com informações de desconto
            $saleData = [
                'id' => $sale->id,
                'customer_name' => $sale->customer_name,
                'customer_phone' => $sale->customer_phone,
                'sale_date' => $sale->sale_date->format('d/m/Y H:i'),
                'payment_method' => $sale->payment_method,
                'subtotal' => $sale->subtotal,
                'discount_amount' => $sale->discount_amount,
                'discount_percentage' => $sale->discount_percentage,
                'discount_type' => $sale->discount_type,
                'discount_reason' => $sale->discount_reason,
                'total_amount' => $sale->total_amount,
                'notes' => $sale->notes,
                'user_name' => $sale->user ? $sale->user->name : 'Sistema',
                'has_discount' => $sale->hasDiscount(),
                'items' => $sale->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product->name ?? $item->description ?? 'Produto',
                        'category' => $item->product->category->name ?? 'Sem categoria',
                        'type' => $item->product->type ?? 'desconhecido',
                        'quantity' => $item->quantity ?? 0,
                        'original_unit_price' => $item->original_unit_price ?? $item->unit_price,
                        'unit_price' => $item->unit_price ?? 0,
                        'total_price' => $item->total_price ?? (($item->quantity ?? 0) * ($item->unit_price ?? 0)),
                        'discount_amount' => $item->discount_amount ?? 0,
                        'discount_percentage' => $item->discount_percentage,
                        'discount_type' => $item->discount_type,
                        'discount_reason' => $item->discount_reason,
                        'has_discount' => $item->hasDiscount(),
                        'original_total' => $item->getOriginalTotal(),
                        'savings' => $item->getSavings(),
                        'description' => $item->description ?? ''
                    ];
                })->toArray()
            ];

            return response()->json($saleData);

        } catch (\Exception $e) {
            \Log::error('Erro no Quick View da venda ' . ($sale->id ?? 'desconhecida'), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'sale_id' => $sale->id ?? null
            ]);

            return response()->json([
                'error' => 'Erro ao carregar detalhes da venda. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Relatório de vendas com análise de descontos
     */
    public function report(Request $request)
    {
        $query = Sale::with(['user', 'items.product']);
        
        // Filtros do relatório
        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }
        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Novo filtro: apenas vendas com desconto
        if ($request->filled('has_discount')) {
            if ($request->has_discount === '1') {
                $query->where('discount_amount', '>', 0);
            } elseif ($request->has_discount === '0') {
                $query->where('discount_amount', '=', 0);
            }
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();
        
        // Estatísticas expandidas com análise de descontos
        $stats = [
            'total_sales' => $sales->count(),
            'total_subtotal' => $sales->sum('subtotal'),
            'total_discount' => $sales->sum('discount_amount'),
            'total_amount' => $sales->sum('total_amount'),
            'avg_sale' => $sales->avg('total_amount'),
            'avg_discount' => $sales->where('discount_amount', '>', 0)->avg('discount_amount'),
            'sales_with_discount' => $sales->where('discount_amount', '>', 0)->count(),
            'discount_percentage_of_sales' => $sales->count() > 0 ? 
                ($sales->where('discount_amount', '>', 0)->count() / $sales->count()) * 100 : 0,
            'payment_methods' => $sales->groupBy('payment_method')->map->count(),
            'discount_impact' => [
                'potential_revenue' => $sales->sum('subtotal'),
                'actual_revenue' => $sales->sum('total_amount'),
                'revenue_lost_to_discounts' => $sales->sum('discount_amount'),
                'discount_percentage' => $sales->sum('subtotal') > 0 ? 
                    ($sales->sum('discount_amount') / $sales->sum('subtotal')) * 100 : 0
            ],
            'daily_sales' => $sales->groupBy(function($sale) {
                return $sale->sale_date->format('Y-m-d');
            })->map(function($daySales) {
                return [
                    'count' => $daySales->count(),
                    'subtotal' => $daySales->sum('subtotal'),
                    'discount' => $daySales->sum('discount_amount'),
                    'amount' => $daySales->sum('total_amount'),
                    'sales_with_discount' => $daySales->where('discount_amount', '>', 0)->count()
                ];
            }),
        ];
        
        return view('sales.report', compact('sales', 'stats'));
    }

    /**
     * Dashboard de vendas com métricas de desconto
     */
    public function dashboard(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);
        
        $sales = Sale::where('sale_date', '>=', $startDate)
                    ->with(['items.product'])
                    ->get();
        
        $stats = [
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('total_amount'),
            'total_subtotal' => $sales->sum('subtotal'),
            'total_discounts' => $sales->sum('discount_amount'),
            'avg_sale_value' => $sales->avg('total_amount'),
            'avg_discount_value' => $sales->where('discount_amount', '>', 0)->avg('discount_amount'),
            'sales_with_discount_count' => $sales->where('discount_amount', '>', 0)->count(),
            'discount_rate' => $sales->count() > 0 ? 
                ($sales->where('discount_amount', '>', 0)->count() / $sales->count()) * 100 : 0,
            'revenue_impact' => [
                'without_discounts' => $sales->sum('subtotal'),
                'with_discounts' => $sales->sum('total_amount'),
                'discount_impact_percentage' => $sales->sum('subtotal') > 0 ? 
                    ($sales->sum('discount_amount') / $sales->sum('subtotal')) * 100 : 0
            ],
            'best_selling_products' => $this->getBestSellingProducts($startDate),
            'daily_sales' => $this->getDailySales($startDate),
            'payment_methods' => $sales->groupBy('payment_method')->map->count(),
            'discount_analysis' => $this->discountService->getDiscountStats(
                \DateTime::createFromInterface($startDate)
            )
        ];
        
        return view('sales.dashboard', compact('stats', 'period'));
    }

    public function print(Sale $sale)
    {
        $sale->load(['user', 'items.product.category']);
        return view('sales.print', compact('sale'));
    }

    public function searchProducts(Request $request)
    {
        $search = $request->get('q');
        
        $products = Product::where('is_active', true)
                          ->where(function($query) use ($search) {
                              $query->where('name', 'like', "%{$search}%")
                                    ->orWhere('code', 'like', "%{$search}%");
                          })
                          ->with('category')
                          ->limit(10)
                          ->get();
        
        return response()->json($products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'price' => $product->selling_price,
                'stock' => $product->stock_quantity,
                'type' => $product->type,
                'category' => $product->category->name ?? 'Sem categoria'
            ];
        }));
    }

    public function duplicate(Sale $sale)
    {
        try {
            $products = Product::where('is_active', true)
                              ->with('category')
                              ->orderBy('name')
                              ->get();
            
            $sale->load(['items.product']);
            
            return view('sales.create', compact('products', 'sale'));
            
        } catch (\Exception $e) {
            return redirect()->route('sales.index')
                ->with('error', 'Erro ao duplicar venda.');
        }
    }

    public function updatePaymentStatus(Request $request, Sale $sale)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,partial,cancelled'
        ]);

        try {
            $sale->update([
                'payment_status' => $request->payment_status
            ]);

            return redirect()->back()
                ->with('success', 'Status de pagamento atualizado com sucesso.');
                
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar status de pagamento: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erro ao atualizar status de pagamento.');
        }
    }

    public function export(Request $request)
    {
        return redirect()->back()
            ->with('info', 'Funcionalidade de exportação será implementada em breve.');
    }

    private function getBestSellingProducts($startDate)
    {
        return SaleItem::whereHas('sale', function($query) use ($startDate) {
                    $query->where('sale_date', '>=', $startDate);
                })
                ->with('product')
                ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), 
                        DB::raw('SUM(total_price) as total_revenue'),
                        DB::raw('SUM(discount_amount) as total_discount'))
                ->groupBy('product_id')
                ->orderBy('total_quantity', 'desc')
                ->limit(10)
                ->get();
    }

    private function getDailySales($startDate)
    {
        return Sale::where('sale_date', '>=', $startDate)
                  ->select(
                      DB::raw('DATE(sale_date) as date'),
                      DB::raw('COUNT(*) as sales_count'),
                      DB::raw('SUM(subtotal) as subtotal_amount'),
                      DB::raw('SUM(discount_amount) as discount_amount'),
                      DB::raw('SUM(total_amount) as total_amount')
                  )
                  ->groupBy('date')
                  ->orderBy('date')
                  ->get();
    }
}