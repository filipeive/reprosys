<?php
// resources/views/products/edit.blade.php
?>
@extends('adminlte::page')

@section('title', 'Editar Produto')

@section('content_header')
    <h1>Editar Produto</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" required>
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Categoria</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="selling_price" class="form-label">Preço de Venda</label>
                <input type="number" class="form-control" id="selling_price" name="selling_price" step="0.01" value="{{ $product->selling_price }}" required>
            </div>

            <div class="mb-3">
                <label for="purchase_price" class="form-label">Preço de Compra (Opcional)</label>
                <input type="number" class="form-control" id="purchase_price" name="purchase_price" step="0.01" value="{{ $product->purchase_price }}">
            </div>

            <div class="mb-3">
                <label for="min_stock_level" class="form-label">Nível Mínimo de Estoque</label>
                <input type="number" class="form-control" id="min_stock_level" name="min_stock_level" value="{{ $product->min_stock_level }}">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Descrição</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ $product->description }}</textarea>
            </div>

            <div class="mb-3">
                <label for="is_active" class="form-label">Status</label>
                <select class="form-control" id="is_active" name="is_active" required>
                    <option value="1" {{ $product->is_active ? 'selected' : '' }}>Ativo</option>
                    <option value="0" {{ !$product->is_active ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop