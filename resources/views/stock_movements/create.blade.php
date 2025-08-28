@extends('layouts.app')

@section('title', 'Registrar Movimento de Estoque')

@section('content_header')
    <h1>Novo Movimento de Estoque</h1>
    <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary">Voltar</a>
@endsection

@section('content')
    <div class="card mt-3">
        <div class="card-header">Registrar Movimento</div>
        <div class="card-body">
            <form action="{{ route('stock-movements.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Produto</label>
                    <select name="product_id" class="form-control" required>
                        <option value="">Selecione</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Tipo de Movimento</label>
                    <select name="movement_type" class="form-control" required>
                        <option value="">-- Selecione o tipo de movimento --</option>
                        <option value="in">Entrada</option>
                        <option value="out">Saída</option>
                        <option value="adjustment">Ajuste</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Quantidade</label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>
                <div class="mb-3">
                    <label>Data</label>
                    <input type="date" name="movement_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Motivo</label>
                    <input type="text" name="reason" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Salvar</button>
            </form>
        </div>
    </div>
@endsection
