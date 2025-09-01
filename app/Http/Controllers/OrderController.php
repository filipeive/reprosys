<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])->latest();

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('customer')) {
            $query->where('customer_name', 'like', '%' . $request->customer . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(15);

        // Estatísticas
        $stats = [
            'pending' => Order::where('status', 'pending')->count(),
            'in_progress' => Order::where('status', 'in_progress')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'overdue' => Order::where('delivery_date', '<', now())
                            ->whereIn('status', ['pending', 'in_progress'])
                            ->count()
        ];

        $products = Product::where('is_active', true)->get();

        return view('orders.index', compact('orders', 'stats', 'products'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('status', 'active')->get();
        
        // Não passar $order para evitar erro de rota
        return view('orders.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:100',
            'description' => 'required|string',
            'estimated_amount' => 'required|numeric|min:0.01',
            'advance_payment' => 'nullable|numeric|min:0',
            'delivery_date' => 'nullable|date|after:today',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
            'items' => 'required|string', // JSON string dos itens
            'create_debt' => 'boolean',
            'debt_due_date' => 'nullable|date|after_or_equal:delivery_date'
        ]);

        try {
            // Decodificar itens
            $items = json_decode($request->items, true);
            
            if (!$items || !is_array($items) || empty($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum item foi fornecido para o pedido.'
                ], 400);
            }

            // Validar estrutura dos itens
            foreach ($items as $index => $item) {
                if (!isset($item['item_name']) || !isset($item['quantity']) || !isset($item['unit_price'])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Item #{$index} com dados incompletos."
                    ], 400);
                }

                if ($item['quantity'] <= 0 || $item['unit_price'] < 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Quantidade e preço devem ser valores positivos."
                    ], 400);
                }
            }

            $order = DB::transaction(function () use ($request, $items) {
                // Validar valor estimado com soma dos itens
                $calculatedTotal = array_sum(array_map(function($item) {
                    return $item['quantity'] * $item['unit_price'];
                }, $items));

                $estimatedAmount = (float) $request->estimated_amount;
                
                // Permitir diferença de até 5% para flexibilidade
                if (abs($calculatedTotal - $estimatedAmount) > ($calculatedTotal * 0.05)) {
                    $estimatedAmount = $calculatedTotal;
                }

                // Criar o pedido
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'customer_email' => $request->customer_email,
                    'description' => $request->description,
                    'estimated_amount' => $estimatedAmount,
                    'advance_payment' => $request->advance_payment ?? 0,
                    'delivery_date' => $request->delivery_date,
                    'priority' => $request->priority,
                    'notes' => $request->notes,
                    'status' => 'pending'
                ]);

                // Criar os itens do pedido
                foreach ($items as $item) {
                    $totalPrice = $item['quantity'] * $item['unit_price'];
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'] ?? null,
                        'item_name' => $item['item_name'],
                        'description' => $item['description'] ?? null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $totalPrice
                    ]);
                }

                // Criar dívida se solicitado
                $remainingAmount = $order->estimated_amount - $order->advance_payment;
                if ($request->create_debt && $remainingAmount > 0) {
                    Debt::create([
                        'user_id' => auth()->id(),
                        'customer_name' => $order->customer_name,
                        'customer_phone' => $order->customer_phone,
                        'original_amount' => $remainingAmount,
                        'remaining_amount' => $remainingAmount,
                        'debt_date' => now()->toDateString(),
                        'due_date' => $request->debt_due_date ?: now()->addDays(30)->toDateString(),
                        'description' => "Pedido #{$order->id} - {$order->description}",
                        'status' => 'active',
                        'notes' => "Valor em aberto do pedido #{$order->id}"
                    ]);
                }

                return $order;
            });

            return response()->json([
                'success' => true,
                'message' => 'Pedido criado com sucesso!',
                'redirect' => route('orders.show', $order)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos: ' . implode(' ', $e->validator->errors()->all())
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Erro ao criar pedido: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'debt']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('status', 'active')->get();
        $order->load('items');
        
        return view('orders.edit', compact('order', 'products', 'categories'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:100',
            'description' => 'required|string',
            'estimated_amount' => 'required|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'delivery_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,delivered,cancelled',
            'notes' => 'nullable|string',
            'internal_notes' => 'nullable|string'
        ]);

        try {
            $order->update($request->all());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pedido atualizado com sucesso!'
                ]);
            }

            return redirect()->route('orders.show', $order)
                ->with('success', 'Pedido atualizado com sucesso!');
                
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar pedido: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar pedido.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Erro ao atualizar pedido.')
                ->withInput();
        }
    }

    public function showDetails(Order $order)
    {
        $order->load(['user', 'items.product.category', 'debt']);

        $html = view('orders.partials.details', compact('order'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function editData(Order $order)
    {
        $order->load('items');
        
        // Transformar os itens em formato esperado pelo frontend
        $orderData = $order->toArray();
        $orderData['items'] = $order->items->map(function($item) {
            return [
                'id' => $item->product_id,
                'name' => $item->item_name,
                'price' => $item->unit_price,
                'quantity' => $item->quantity
            ];
        });

        return response()->json(['success' => true, 'data' => $orderData]);
    }

    public function destroy(Order $order)
    {
        try {
            if (in_array($order->status, ['completed', 'delivered'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pedidos concluídos ou entregues não podem ser cancelados.'
                ], 400);
            }

            DB::transaction(function () use ($order) {
                // Se há dívida relacionada, cancelar também
                if ($order->debt) {
                    $order->debt->update(['status' => 'cancelled']);
                }
                
                $order->update(['status' => 'cancelled']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pedido cancelado com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar pedido: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar pedido.'
            ], 500);
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,delivered,cancelled'
        ]);

        try {
            $order->update(['status' => $request->status]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status atualizado com sucesso!'
                ]);
            }

            return redirect()->route('orders.show', $order)
                ->with('success', 'Status do pedido atualizado!');
                
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar status: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar status.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Erro ao atualizar status.');
        }
    }

    public function convertToSale(Order $order)
    {
        if (!in_array($order->status, ['completed', 'delivered'])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Pedido deve estar concluído para ser convertido em venda.');
        }

        try {
            $sale = $order->convertToSale();
            
            return redirect()->route('sales.show', $sale)
                ->with('success', 'Pedido convertido em venda com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao converter pedido: ' . $e->getMessage());
            return redirect()->route('orders.show', $order)
                ->with('error', 'Erro ao converter pedido em venda: ' . $e->getMessage());
        }
    }

    public function duplicate(Order $order)
    {
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('status', 'active')->get();
        $order->load('items');
        
        return view('orders.create', compact('order', 'products', 'categories'));
    }

    // Relatório de pedidos com filtros avançados
    public function report(Request $request)
    {
        $query = Order::with(['user', 'items']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('customer')) {
            $query->where('customer_name', 'like', '%' . $request->customer . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->get();

        // Estatísticas do relatório
        $reportStats = [
            'total_orders' => $orders->count(),
            'total_amount' => $orders->sum('estimated_amount'),
            'total_advance' => $orders->sum('advance_payment'),
            'total_pending' => $orders->sum(function($order) {
                return $order->estimated_amount - $order->advance_payment;
            }),
            'by_status' => [
                'pending' => $orders->where('status', 'pending')->count(),
                'in_progress' => $orders->where('status', 'in_progress')->count(),
                'completed' => $orders->where('status', 'completed')->count(),
                'delivered' => $orders->where('status', 'delivered')->count(),
                'cancelled' => $orders->where('status', 'cancelled')->count(),
            ],
            'by_priority' => [
                'low' => $orders->where('priority', 'low')->count(),
                'medium' => $orders->where('priority', 'medium')->count(),
                'high' => $orders->where('priority', 'high')->count(),
                'urgent' => $orders->where('priority', 'urgent')->count(),
            ],
            'overdue_count' => $orders->filter(function($order) {
                return $order->delivery_date && $order->delivery_date < now() && 
                       in_array($order->status, ['pending', 'in_progress']);
            })->count()
        ];

        return view('orders.report', compact('orders', 'reportStats'));
    }

    // API para busca de produtos
    public function searchProducts(Request $request)
    {
        $term = $request->get('term', $request->get('q', ''));
        
        $products = Product::where('is_active', true)
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', '%' . $term . '%')
                      ->orWhere('description', 'like', '%' . $term . '%');
            })
            ->with('category')
            ->limit(10)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->selling_price,
                    'stock' => $product->stock_quantity,
                    'category' => $product->category->name ?? 'Sem categoria'
                ];
            });

        return response()->json($products);
    }
}