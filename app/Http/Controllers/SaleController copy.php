<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaleControllerCopy extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with('user', 'items.product');
        
        // Filtros
        if ($request->filled('search')) {
            $query->where('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $request->search . '%');
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
        
        $sales = $query->latest()->paginate(20);
        
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

    /* public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|in:cash,card,transfer,credit',
            'notes' => 'nullable|string',
            'items' => 'required|string',
        ]);

        try {
            // Decode items JSON
            $items = json_decode($request->items, true);
            
            if (!$items || !is_array($items) || empty($items)) {
                return redirect()->back()
                    ->with('error', 'Nenhum item foi adicionado ao carrinho.')
                    ->withInput();
            }

            // Validate items
            foreach ($items as $item) {
                if (!isset($item['product_id']) || !isset($item['quantity']) || !isset($item['unit_price'])) {
                    return redirect()->back()
                        ->with('error', 'Dados do item inválidos.')
                        ->withInput();
                }
                
                if ($item['quantity'] <= 0) {
                    return redirect()->back()
                        ->with('error', 'Quantidade deve ser maior que zero.')
                        ->withInput();
                }
            }

            DB::transaction(function () use ($request, $items) {
                $sale = Sale::create([
                    'user_id' => auth()->id(),
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'payment_method' => $request->payment_method,
                    'notes' => $request->notes,
                    'sale_date' => now()->toDateString(),
                    'total_amount' => 0,
                ]);

                $totalAmount = 0;
                
                foreach ($items as $item) {
                    $product = Product::find($item['product_id']);
                    
                    if (!$product) {
                        throw new \Exception("Produto não encontrado: {$item['product_id']}");
                    }
                    
                    // Verificar stock para produtos
                    if ($product->type === 'product') {
                        if ($product->stock_quantity < $item['quantity']) {
                            throw new \Exception("Stock insuficiente para {$product->name}. Disponível: {$product->stock_quantity}");
                        }
                    }
                    
                    $totalPrice = $item['quantity'] * $item['unit_price'];
                    
                    // Create sale item
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $totalPrice,
                    ]);

                    
                    // Update stock for products (not services)
                    if ($product->type === 'product') {
                        $product->decrement('stock_quantity', $item['quantity']);
                        
                        // Record stock movement
                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'movement_type' => 'out',
                            'quantity' => $item['quantity'],
                            'reason' => 'Venda',
                            'reference_id' => $sale->id,
                            'movement_date' => now()->toDateString(),
                        ]);
                    }
                    
                    $totalAmount += $totalPrice;
                }
                
                $sale->update(['total_amount' => $totalAmount]);
            });

            return redirect()->route('sales.index')
                ->with('success', 'Venda registrada com sucesso.');
                
        } catch (\Exception $e) {
            Log::error('Erro ao criar venda: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erro ao processar venda: ' . $e->getMessage())
                ->withInput();
        }
    } */
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
        $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|in:cash,card,transfer,credit',
            'notes' => 'nullable|string',
            'items' => 'required|string',
            'sale_date' => 'nullable|date_format:Y-m-d\TH:i', // aceita datetime-local do HTML5
        ]);

        try {
            $items = json_decode($request->items, true);

            if (!$items || !is_array($items) || empty($items)) {
                return redirect()->back()
                    ->with('error', 'Nenhum item foi adicionado ao carrinho.')
                    ->withInput();
            }

            foreach ($items as $item) {
                if (!isset($item['product_id']) || !isset($item['quantity']) || !isset($item['unit_price'])) {
                    return redirect()->back()
                        ->with('error', 'Dados do item inválidos.')
                        ->withInput();
                }
                if ($item['quantity'] <= 0) {
                    return redirect()->back()
                        ->with('error', 'Quantidade deve ser maior que zero.')
                        ->withInput();
                }
            }

            DB::transaction(function () use ($request, $items) {
                $sale = Sale::create([
                    'user_id' => auth()->id(),
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'payment_method' => $request->payment_method,
                    'notes' => $request->notes,
                    'sale_date' => $request->sale_date ? date('Y-m-d H:i:s', strtotime($request->sale_date)) : now()->toDateTimeString(),
                    'total_amount' => 0,
                ]);

                $totalAmount = 0;

                foreach ($items as $item) {
                    $product = Product::find($item['product_id']);

                    if (!$product) {
                        throw new \Exception("Produto não encontrado: {$item['product_id']}");
                    }

                    if ($product->type === 'product') {
                        if ($product->stock_quantity < $item['quantity']) {
                            throw new \Exception("Stock insuficiente para {$product->name}. Disponível: {$product->stock_quantity}");
                        }
                    }

                    $totalPrice = $item['quantity'] * $item['unit_price'];

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $totalPrice,
                    ]);

                    if ($product->type === 'product') {
                        $product->decrement('stock_quantity', $item['quantity']);

                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'movement_type' => 'out',
                            'quantity' => $item['quantity'],
                            'reason' => 'Venda',
                            'reference_id' => $sale->id,
                            'movement_date' => $sale->sale_date,
                        ]);
                    }

                    $totalAmount += $totalPrice;
                }

                $sale->update(['total_amount' => $totalAmount]);
            });

            return redirect()->route('sales.index')
                ->with('success', 'Venda registrada com sucesso.');

        } catch (\Exception $e) {
            Log::error('Erro ao criar venda: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Erro ao processar venda: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.product']);
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $sale->load(['items.product']);
        $products = Product::where('is_active', true)->get();
        
        return view('sales.edit', compact('sale', 'products'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|in:cash,card,transfer,credit',
            'notes' => 'nullable|string',
        ]);

        try {
            $sale->update([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

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
                ->with('success', 'Venda cancelada com sucesso.');
                
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar venda: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erro ao cancelar venda.');
        }
    }

    public function print(Sale $sale)
    {
        $sale->load(['user', 'items.product']);
        return view('sales.print', compact('sale'));
    }
}