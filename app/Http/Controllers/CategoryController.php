<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Exibir lista de categorias
     */
    public function index()
    {
        $categories = Category::withCount('products')
                            ->orderBy('name')
                            ->get();
        
        return view('categories.index', compact('categories'));
    }

    /**
     * Criar nova categoria
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string|max:500',
                'type' => 'required|in:product,service',
                'color' => 'required|string|max:7',
                'icon' => 'required|string|max:100',
                'status' => 'required|in:active,inactive'
            ]);

            Category::create($validated);

            return redirect()->back()->with('success', 'Categoria criada com sucesso!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                           ->withErrors($e->errors())
                           ->withInput()
                           ->with('error', 'Erro ao criar categoria. Verifique os dados informados.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erro interno do servidor. Tente novamente.');
        }
    }

    /**
     * Atualizar categoria existente
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('categories', 'name')->ignore($id)
                ],
                'description' => 'nullable|string|max:500',
                'type' => 'required|in:product,service',
                'color' => 'required|string|max:7',
                'icon' => 'required|string|max:100',
                'status' => 'required|in:active,inactive'
            ]);

            $category->update($validated);

            return redirect()->back()->with('success', 'Categoria atualizada com sucesso!');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Categoria não encontrada.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                           ->withErrors($e->errors())
                           ->withInput()
                           ->with('error', 'Erro ao atualizar categoria. Verifique os dados informados.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erro interno do servidor. Tente novamente.');
        }
    }

    /**
     * Excluir categoria
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            
            // Verificar se a categoria tem produtos associados
            if ($category->products()->count() > 0) {
                return redirect()->back()
                               ->with('error', 'Não é possível excluir esta categoria pois ela possui produtos associados.');
            }

            $category->delete();

            return redirect()->back()->with('success', 'Categoria excluída com sucesso!');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Categoria não encontrada.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erro ao excluir categoria. Tente novamente.');
        }
    }

    /**
     * Obter categoria específica (para AJAX)
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return response()->json($category);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Categoria não encontrada'], 404);
        }
    }

    /**
     * Alternar status da categoria
     */
    public function toggleStatus($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->status = $category->status === 'active' ? 'inactive' : 'active';
            $category->save();

            return redirect()->back()
                           ->with('success', 'Status da categoria alterado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erro ao alterar status da categoria.');
        }
    }
}