@extends('adminlte::page')

@section('title', 'Adicionar Despesa')

@section('content_header')
    <h1>Nova Despesa</h1>
    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Voltar</a>
@endsection

@section('content')
    <div class="card mt-3">
        <div class="card-header">Cadastrar Despesa</div>
        <div class="card-body">
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Categoria</label>
                    <select name="expense_category_id" class="form-control" required>
                        <option value="">-- Selecione --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Descrição</label>
                    <textarea name="description" class="form-control" required>{{ old('description') }}</textarea>
                </div>
                <div class="mb-3">
                    <label>Valor</label>
                    <input type="number" name="amount" class="form-control" step="0.01" required
                        value="{{ old('amount') }}">
                </div>
                <div class="mb-3">
                    <label>Data</label>
                    <input type="date" name="expense_date" class="form-control" required
                        value="{{ old('expense_date') }}">
                </div>
                <div class="mb-3">
                    <label>Número do Recibo</label>
                    <input type="text" name="receipt_number" class="form-control" value="{{ old('receipt_number') }}">
                </div>
                <div class="mb-3">
                    <label>Observações</label>
                    <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="btn btn-success">Salvar</button>
            </form>
        </div>
    </div>
@endsection
