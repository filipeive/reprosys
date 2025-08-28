<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Construir query com filtros
        $query = Product::with('category');

        // Aplicar filtros se fornecidos
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Ordenar e paginar
        $products = $query->orderBy('name')->paginate(10)->withQueryString();
        
        // Buscar categorias para filtros
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        
        // Calcular estatísticas
        $allProducts = Product::all();
        $lowStockCount = Product::where('type', 'product')
                                ->whereRaw('stock_quantity <= min_stock_level')
                                ->count();

        return view('products.index', compact('products', 'categories', 'allProducts', 'lowStockCount'));
    }

    public function create()
    {
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $validationRules = [
                    'name' => 'required|string|max:150',
                    'category_id' => 'required|exists:categories,id',
                    'type' => 'required|in:product,service',
                    'selling_price' => 'required|numeric|min:0',
                    'purchase_price' => 'nullable|numeric|min:0',
                    'unit' => 'nullable|string|max:20',
                    'description' => 'nullable|string|max:500',
                    'is_active' => 'boolean'
                ];

                // Validações adicionais se for produto
                if ($request->type === 'product') {
                    $validationRules['stock_quantity'] = 'required|integer|min:0';
                    $validationRules['min_stock_level'] = 'required|integer|min:0';
                }

                $validated = $request->validate($validationRules);

                DB::beginTransaction();

                $data = collect($validated)->only([
                    'name', 'category_id', 'type', 'selling_price',
                    'purchase_price', 'unit', 'description'
                ])->toArray();

                $data['is_active'] = $request->boolean('is_active', true);

                if ($request->type === 'product') {
                    $data['stock_quantity'] = (int) $request->input('stock_quantity', 0);
                    $data['min_stock_level'] = (int) $request->input('min_stock_level', 0);
                } else {
                    $data['stock_quantity'] = 0;
                    $data['min_stock_level'] = 0;
                }

                $product = Product::create($data);

                // Criar movimento inicial de estoque
                if ($product->type === 'product' && $product->stock_quantity > 0) {
                    StockMovement::create([
                        'product_id'     => $product->id,
                        'user_id'        => auth()->id(),
                        'movement_type'  => 'in',
                        'quantity'       => $product->stock_quantity,
                        'reason'         => 'Estoque inicial',
                        'movement_date'  => now(),
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Produto criado com sucesso.'
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors'  => $e->errors()
                ], 422);

            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao salvar produto',
                    'error'   => $e->getMessage()
                ], 500);
            }
        }
    }


    public function show(Product $product)
    {
        $product->load(['category', 'stockMovements.user']);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        try {
            $validationRules = [
                'name' => 'required|string|max:150',
                'category_id' => 'required|exists:categories,id',
                'selling_price' => 'required|numeric|min:0',
                'purchase_price' => 'nullable|numeric|min:0',
                'unit' => 'nullable|string|max:20',
                'description' => 'nullable|string|max:500',
                'is_active' => 'boolean'
            ];

            if ($product->type === 'product') {
                $validationRules['min_stock_level'] = 'required|integer|min:0';
            }

            $validated = $request->validate($validationRules);

            $data = collect($validated)->only([
                'name', 'category_id', 'selling_price',
                'purchase_price', 'unit', 'description'
            ])->toArray();

            $data['is_active'] = $request->boolean('is_active', true);

            if ($product->type === 'product') {
                $data['min_stock_level'] = (int) $request->input('min_stock_level', 0);
            }

            $product->update($data);

            // Se for AJAX, responde JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produto atualizado com sucesso.'
                ]);
            }

            // Caso contrário, redireciona normalmente
            return redirect()->route('products.index')
                ->with('success', 'Produto atualizado com sucesso.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar produto: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar produto',
                    'error'   => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao atualizar produto.');
        }
    }


    public function destroy(Product $product)
    {
        try {
            // Soft delete: apenas marcar como excluído
            $product->delete();

            return redirect()->route('products.index')
                ->with('success', 'Produto excluído com sucesso.');

        } catch (\Exception $e) {
            Log::error('Erro ao excluir produto: ' . $e->getMessage());
            return back()->with('error', 'Erro ao excluir produto.');
        }
    }

    public function adjustStock(Request $request, Product $product)
    {
        try {
            // Verificar se é produto
            if ($product->type !== 'product') {
                return response()->json([
                    'success' => false,
                    'message' => 'Apenas produtos podem ter estoque ajustado.'
                ], 400);
            }

            $request->validate([
                'adjustment_type' => 'required|in:increase,decrease',
                'quantity' => 'required|integer|min:1',
                'reason' => 'required|string|max:200',
            ]);

            DB::beginTransaction();

            $movementType = $request->adjustment_type === 'increase' ? 'in' : 'out';
            $quantity = $request->integer('quantity');

            // Verificar estoque suficiente para saída
            if ($request->adjustment_type === 'decrease') {
                if ($product->stock_quantity < $quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Estoque insuficiente. Disponível: ' . $product->stock_quantity
                    ], 400);
                }
            }

            // Atualizar estoque
            $product->updateStock($quantity, $movementType);

            // Registrar movimento
            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'reason' => $request->reason,
                'movement_date' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Estoque ajustado com sucesso.',
                'new_stock' => $product->fresh()->stock_quantity
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao ajustar estoque: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor.'
            ], 500);
        }
    }

    // Métodos API para AJAX (mantidos para compatibilidade)
    public function getCategories()
    {
        try {
            $categories = Category::where('status', 'active')->orderBy('name')->get();
            return response()->json($categories);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar categorias: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao carregar produtos'], 500);
        }
    }
    public function editData(Product $product)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $product->name,
                    'category_id' => $product->category_id,
                    'type' => $product->type,
                    'description' => $product->description,
                    'selling_price' => $product->selling_price,
                    'purchase_price' => $product->purchase_price,
                    'unit' => $product->unit,
                    'stock_quantity' => $product->stock_quantity,
                    'min_stock_level' => $product->min_stock_level,
                    'is_active' => $product->is_active
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados do produto: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dados do produto'
            ], 500);
        }
    }
}