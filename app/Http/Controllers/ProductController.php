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
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.index', compact('categories'));
    }
   /*  public function getCategories()
    {
        try {
            $categories = Category::orderBy('name')->get();
            return response()->json($categories);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar categorias: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao carregar categorias'], 500);
        }
    } */
    public function getProducts(Request $request)
    {
        try {
            $query = Product::with('category');
    
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }
            if ($request->filled('status')) {
                $query->where('is_active', $request->status === 'active' ? 1 : 0);
            }
            if ($request->filled('stock')) {
                if ($request->stock === 'low') {
                    $query->where('type', 'product')
                          ->whereRaw('stock_quantity <= min_stock_level');
                } elseif ($request->stock === 'ok') {
                    $query->where('type', 'product')
                          ->whereRaw('stock_quantity > min_stock_level');
                }
            }
    
            $perPage = $request->get('per_page', 10);
            $products = $query->orderBy('name')->paginate($perPage);
    
            return response()->json($products);
    
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar produtos: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao carregar produtos'], 500);
        }
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
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

            // Validações condicionais para produtos
            if ($request->type === 'product') {
                $validationRules['stock_quantity'] = 'required|integer|min:0';
                $validationRules['min_stock_level'] = 'required|integer|min:0';
            }

            $request->validate($validationRules);

            DB::beginTransaction();

            $data = $request->only([
                'name', 'category_id', 'type', 'selling_price', 
                'purchase_price', 'unit', 'description'
            ]);
            
            $data['is_active'] = $request->boolean('is_active', true);

            // Campos específicos para produtos
            if ($request->type === 'product') {
                $data['stock_quantity'] = $request->integer('stock_quantity', 0);
                $data['min_stock_level'] = $request->integer('min_stock_level', 0);
            } else {
                $data['stock_quantity'] = 0;
                $data['min_stock_level'] = 0;
            }
            
            $product = Product::create($data);

            // Criar movimento de estoque inicial apenas se for produto e tiver estoque
            if ($request->type === 'product' && $request->integer('stock_quantity', 0) > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'movement_type' => 'in',
                    'quantity' => $request->integer('stock_quantity'),
                    'reason' => 'Estoque inicial',
                    'movement_date' => now(),
                ]);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produto criado com sucesso.',
                    'product' => $product->load('category')
                ]);
            }

            return redirect()->route('products.index')
                ->with('success', 'Produto criado com sucesso.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar produto: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno do servidor.'
                ], 500);
            }
            return back()->with('error', 'Erro ao criar produto.');
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'stockMovements.user']);
        
        if (request()->ajax()) {
            return response()->json($product);
        }
        
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        
        if (request()->ajax()) {
            return response()->json([
                'product' => $product->load('category'),
                'categories' => $categories
            ]);
        }
        
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

            // Validações condicionais para produtos
            if ($product->type === 'product') {
                $validationRules['min_stock_level'] = 'required|integer|min:0';
            }

            $request->validate($validationRules);

            $data = $request->only([
                'name', 'category_id', 'selling_price', 
                'purchase_price', 'unit', 'description'
            ]);
            
            $data['is_active'] = $request->boolean('is_active', true);

            // Campos específicos para produtos
            if ($product->type === 'product') {
                $data['min_stock_level'] = $request->integer('min_stock_level', 0);
            }
            
            $product->update($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produto atualizado com sucesso.',
                    'product' => $product->load('category')
                ]);
            }

            return redirect()->route('products.index')
                ->with('success', 'Produto atualizado com sucesso.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar produto: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno do servidor.'
                ], 500);
            }
            return back()->with('error', 'Erro ao atualizar produto.');
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

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Estoque ajustado com sucesso.',
                    'new_stock' => $product->fresh()->stock_quantity
                ]);
            }

            return back()->with('success', 'Estoque ajustado com sucesso.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao ajustar estoque: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno do servidor.'
                ], 500);
            }
            return back()->with('error', 'Erro ao ajustar estoque.');
        }
    }
    
    /* public function destroy(Product $product)
    {
        try {
            // Verificar permissão
            if (!Gate::allows('delete-product')) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Você não tem permissão para excluir este produto.'
                    ], 403);
                }
                abort(403, 'Você não tem permissão para excluir este produto.');
            }

            // Verificar se produto tem movimentações
            if ($product->stockMovements()->exists()) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Não é possível excluir produto com movimentações de estoque.'
                    ], 400);
                }
                return back()->with('error', 'Não é possível excluir produto com movimentações de estoque.');
            }

            $product->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produto excluído com sucesso.'
                ]);
            }
            
            return redirect()->route('products.index')
                ->with('success', 'Produto excluído com sucesso.');

        } catch (\Exception $e) {
            Log::error('Erro ao excluir produto: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno do servidor.'
                ], 500);
            }
            return back()->with('error', 'Erro ao excluir produto.');
        }
    } */
    public function destroy(Product $product)
    {
        try {
            // Verificar permissão
            if (!Gate::allows('delete-product')) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Você não tem permissão para excluir este produto.'
                    ], 403);
                }
                abort(403, 'Você não tem permissão para excluir este produto.');
            }

            // Soft delete: apenas marcar como excluído (Laravel faz isso automaticamente)
            $product->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produto excluído com sucesso.'
                ]);
            }

            return redirect()->route('products.index')
                ->with('success', 'Produto excluído com sucesso.');

        } catch (\Exception $e) {
            Log::error('Erro ao excluir produto: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno do servidor.'
                ], 500);
            }
            return back()->with('error', 'Erro ao excluir produto.');
        }
    }


    public function getCategories()
    {
        try {
            $categories = Category::orderBy('name')->get();
            return response()->json($categories);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar categorias: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao carregar categorias'], 500);
        }
    }
}