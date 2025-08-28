<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Estatísticas rápidas
        $stats = [
            'pending' => Order::where('status', 'pending')->count(),
            'in_progress' => Order::where('status', 'in_progress')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'overdue' => Order::overdue()->count()
        ];

        return view('orders.index', compact('orders', 'stats'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('status', 'active')->get();
        
        return view('orders.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:100',
            'description' => 'required|string',
            'estimated_amount' => 'required|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'delivery_date' => 'nullable|date|after:today',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.item_name' => 'required|string|max:150',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0'
        ]);

        DB::transaction(function () use ($request) {
            // Criar o pedido
            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'description' => $request->description,
                'estimated_amount' => $request->estimated_amount,
                'advance_payment' => $request->advance_payment ?? 0,
                'delivery_date' => $request->delivery_date,
                'priority' => $request->priority,
                'notes' => $request->notes
            ]);

            // Criar os itens do pedido
            foreach ($request->items as $item) {
                $orderItem = new OrderItem([
                    'product_id' => $item['product_id'],
                    'item_name' => $item['item_name'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price']
                ]);
                
                $orderItem->total_price = $orderItem->quantity * $orderItem->unit_price;
                $order->items()->save($orderItem);
            }

            // Se há valor em aberto e o cliente vai pagar depois, criar dívida
            $remainingAmount = $order->estimated_amount - $order->advance_payment;
            if ($remainingAmount > 0 && $request->create_debt) {
                $order->debt()->create([
                    'user_id' => auth()->id(),
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'original_amount' => $remainingAmount,
                    'remaining_amount' => $remainingAmount,
                    'debt_date' => now()->toDateString(),
                    'due_date' => $request->debt_due_date,
                    'description' => "Pedido #{$order->id} - {$order->description}"
                ]);
            }
        });

        return redirect()->route('orders.index')
            ->with('success', 'Pedido criado com sucesso!');
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

        $order->update($request->all());

        return redirect()->route('orders.show', $order)
            ->with('success', 'Pedido atualizado com sucesso!');
    }

    public function destroy(Order $order)
    {
        if (!$order->canBeCancelled()) {
            return redirect()->route('orders.index')
                ->with('error', 'Este pedido não pode ser cancelado.');
        }

        DB::transaction(function () use ($order) {
            // Se há dívida relacionada, cancelar também
            if ($order->debt) {
                $order->debt->update(['status' => 'cancelled']);
            }
            
            $order->delete();
        });

        return redirect()->route('orders.index')
            ->with('success', 'Pedido cancelado com sucesso!');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Status do pedido atualizado!');
    }

    public function convertToSale(Order $order)
    {
        if (!$order->canBeDelivered()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Pedido deve estar concluído para ser convertido em venda.');
        }

        try {
            $sale = $order->convertToSale();
            
            return redirect()->route('sales.show', $sale)
                ->with('success', 'Pedido convertido em venda com sucesso!');
        } catch (\Exception $e) {
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

    // API para busca de produtos
    public function searchProducts(Request $request)
    {
        $term = $request->get('term');
        
        $products = Product::where('is_active', true)
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', '%' . $term . '%')
                      ->orWhere('description', 'like', '%' . $term . '%');
            })
            ->with('category')
            ->limit(10)
            ->get();

        return response()->json($products);
    }
}