<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SaleController extends Controller
{
    /**
     * Exibir lista de vendas
     */
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'items.product']);
        
        // Filtros
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
        
        $sales = $query->latest('sale_date')->paginate(8);
        
        return view('sales.index', compact('sales'));
    }

    /**
     * Mostrar formulário de criação de venda
     */
    public function create()
    {
        $products = Product::where('is_active', true)
                          ->with('category')
                          ->orderBy('name')
                          ->get();
        
        return view('sales.create', compact('products'));
    }

    /**
     * Mostrar formulário de criação manual de venda
     */
    public function manualCreate()
    {
        $products = Product::where('is_active', true)
                          ->with('category')
                          ->orderBy('name')
                          ->get();
        
        return view('sales.manual-create', compact('products'));
    }

    /**
     * Armazenar nova venda
     */
     
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|in:cash,card,transfer,credit',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|string',
            'sale_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id', // Para vendas manuais
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

            DB::transaction(function () use ($validated, $items) {
                // Determinar data da venda
                $saleDate = array_key_exists('sale_date', $validated) && $validated['sale_date']
                    ? Carbon::parse($validated['sale_date'])
                    : now();

                // Determinar usuário (para vendas manuais pode ser diferente)
                $userId = $validated['user_id'] ?? auth()->id();

                // Criar venda
                $sale = Sale::create([
                    'user_id' => $userId,
                    'customer_name' => $validated['customer_name'] ?: 'Cliente Avulso',
                    'customer_phone' => $validated['customer_phone'],
                    'payment_method' => $validated['payment_method'],
                    'notes' => $validated['notes'],
                    'sale_date' => $saleDate,
                    'total_amount' => 0,
                ]);

                $totalAmount = 0;
                $discountTotal = 0; // Para rastrear descontos aplicados

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

                    // Calcular preço total do item
                    $unitPrice = (float) $item['unit_price'];
                    $totalPrice = $item['quantity'] * $unitPrice;

                    // Calcular desconto se o preço foi alterado
                    $originalPrice = $product->selling_price;
                    $discountPerUnit = $originalPrice - $unitPrice;
                    $itemDiscount = $discountPerUnit * $item['quantity'];
                    
                    if ($itemDiscount > 0) {
                        $discountTotal += $itemDiscount;
                    }

                    // Criar item da venda
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                    ]);

                    // Atualizar stock apenas para produtos físicos
                    if ($product->type === 'product') {
                        $product->decrement('stock_quantity', $item['quantity']);

                        // Registrar movimento de stock
                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => $userId,
                            'movement_type' => 'out',
                            'quantity' => $item['quantity'],
                            'reason' => 'Venda',
                            'reference_id' => $sale->id,
                            'movement_date' => $saleDate->toDateString(),
                        ]);
                    }

                    $totalAmount += $totalPrice;
                }

                // Atualizar total da venda
                $sale->update(['total_amount' => $totalAmount]);

                // Log do desconto aplicado se houver
                if ($discountTotal > 0) {
                    Log::info("Desconto aplicado na venda #{$sale->id}: MZN {$discountTotal}");
                    
                    // Adicionar informação do desconto nas observações se não houver
                    if (!$validated['notes'] || !str_contains($validated['notes'], 'desconto')) {
                        $discountInfo = "Desconto total aplicado: MZN " . number_format($discountTotal, 2, ',', '.');
                        $existingNotes = $validated['notes'] ? $validated['notes'] . " | " : "";
                        $sale->update(['notes' => $existingNotes . $discountInfo]);
                    }
                }
            });

            // Determinar rota de retorno baseada no tipo de venda
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
     * Exibir detalhes de uma venda
     */
    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.product.category']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Mostrar formulário de edição
     */
    public function edit(Sale $sale)
    {
        $sale->load(['items.product']);
        $products = Product::where('is_active', true)
                          ->with('category')
                          ->orderBy('name')
                          ->get();
        
        return view('sales.edit', compact('sale', 'products'));
    }

    /**
     * Atualizar venda
     */
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

    /**
     * Cancelar venda
     */
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

    /**
     * Imprimir comprovante da venda
     */
    public function print(Sale $sale)
    {
        $sale->load(['user', 'items.product.category']);
        return view('sales.print', compact('sale'));
    }

   /*  /**
     * Obter detalhes rápidos da venda (para modal)
     
    public function quickView(Sale $sale)
    {
        try {
            // Carregar as relações necessárias
            $sale->load(['user', 'items.product']);

            // Renderizar a view parcial para o Quick View
            $html = view('sales.partials.quick-view', compact('sale'))->render();

            // Retornar a resposta como JSON
            return response()->json(['html' => $html]);
        } catch (\Exception $e) {
            // Registrar o erro no log
            \Log::error('Erro no Quick View: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao carregar a visualização rápida.'], 500);
        }
    } */

    /**
     * Obter detalhes rápidos da venda (para modal/offcanvas)
     */
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

            // Preparar dados
            $saleData = [
                'id' => $sale->id,
                'customer_name' => $sale->customer_name,
                'customer_phone' => $sale->customer_phone,
                'sale_date' => $sale->sale_date->format('d/m/Y H:i'),
                'payment_method' => $sale->payment_method,
                'total_amount' => $sale->total_amount,
                'notes' => $sale->notes,
                'user_name' => $sale->user ? $sale->user->name : 'Sistema',
                'items' => $sale->items->map(function ($item) {
                    return [
                        'product_name' => $item->product->name ?? $item->description ?? 'Produto',
                        'category' => $item->product->category->name ?? 'Sem categoria',
                        'type' => $item->product->type ?? 'desconhecido',
                        'quantity' => $item->quantity ?? 0,
                        'unit_price' => $item->unit_price ?? 0,
                        'total_price' => $item->total_price ?? (($item->quantity ?? 0) * ($item->unit_price ?? 0)),
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
     * Relatório de vendas
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

        $sales = $query->orderBy('sale_date', 'desc')->get();
        
        // Estatísticas
        $stats = [
            'total_sales' => $sales->count(),
            'total_amount' => $sales->sum('total_amount'),
            'avg_sale' => $sales->avg('total_amount'),
            'payment_methods' => $sales->groupBy('payment_method')->map->count(),
            'daily_sales' => $sales->groupBy(function($sale) {
                return $sale->sale_date->format('Y-m-d');
            })->map(function($daySales) {
                return [
                    'count' => $daySales->count(),
                    'amount' => $daySales->sum('total_amount')
                ];
            }),
        ];
        
        return view('sales.report', compact('sales', 'stats'));
    }

    /**
     * Buscar produtos para autocomplete
     */
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
                'price' => $product->price,
                'stock' => $product->stock_quantity,
                'type' => $product->type,
                'category' => $product->category->name ?? 'Sem categoria'
            ];
        }));
    }

    /**
     * Duplicar venda
     */
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

    /**
     * Alterar status de pagamento
     */
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

    /**
     * Exportar vendas para Excel/CSV
     */
    public function export(Request $request)
    {
        // Esta função pode ser implementada com Laravel Excel
        // Por enquanto, retorna um erro informativo
        return redirect()->back()
            ->with('info', 'Funcionalidade de exportação será implementada em breve.');
    }

    /**
     * Dashboard de vendas
     */
    public function dashboard(Request $request)
    {
        $period = $request->get('period', '30'); // 7, 30, 90 dias
        $startDate = now()->subDays($period);
        
        $sales = Sale::where('sale_date', '>=', $startDate)
                    ->with(['items.product'])
                    ->get();
        
        $stats = [
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('total_amount'),
            'avg_sale_value' => $sales->avg('total_amount'),
            'best_selling_products' => $this->getBestSellingProducts($startDate),
            'daily_sales' => $this->getDailySales($startDate),
            'payment_methods' => $sales->groupBy('payment_method')->map->count(),
        ];
        
        return view('sales.dashboard', compact('stats', 'period'));
    }

    /**
     * Produtos mais vendidos
     */
    private function getBestSellingProducts($startDate)
    {
        return SaleItem::whereHas('sale', function($query) use ($startDate) {
                    $query->where('sale_date', '>=', $startDate);
                })
                ->with('product')
                ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total_price) as total_revenue'))
                ->groupBy('product_id')
                ->orderBy('total_quantity', 'desc')
                ->limit(10)
                ->get();
    }

    /**
     * Vendas diárias
     */
    private function getDailySales($startDate)
    {
        return Sale::where('sale_date', '>=', $startDate)
                  ->select(
                      DB::raw('DATE(sale_date) as date'),
                      DB::raw('COUNT(*) as sales_count'),
                      DB::raw('SUM(total_amount) as total_amount')
                  )
                  ->groupBy('date')
                  ->orderBy('date')
                  ->get();
    }
}