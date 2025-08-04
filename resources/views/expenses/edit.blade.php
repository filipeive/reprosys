@extends('adminlte::page')

@section('title', 'Editar Despesa')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Editar Despesa</h1>
        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Voltar</a>
    </div>
@endsection

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Informações da Despesa</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('expenses.update', $expense->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Categoria</label>
                    <select name="expense_category_id" class="form-control" required>
                        <option value="">-- Selecione --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('expense_category_id', $expense->expense_category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Descrição</label>
                    <textarea name="description" id="description" class="form-control" required>{{ old('description', $expense->description) }}</textarea>
                </div>


                <div class="form-group">
                    <label for="amount">Valor</label>
                    <input type="number" name="amount" id="amount" class="form-control" step="0.01" required
                        value="{{ old('amount', $expense->amount) }}">
                </div>

                <div class="form-group">
                    <label for="expense_date">Data</label>
                    <input type="date" name="expense_date" id="expense_date" class="form-control" required
                        value="{{ old('expense_date', $expense->expense_date) }}">
                </div>

                <div class="form-group">
                    <label for="receipt_number">Número do Recibo</label>
                    <input type="text" name="receipt_number" id="receipt_number" class="form-control"
                        value="{{ old('receipt_number', $expense->receipt_number) }}">
                </div>

                <div class="form-group">
                    <label for="notes">Observações</label>
                    <textarea name="notes" id="notes" class="form-control">{{ old('notes', $expense->notes) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Salvar Alterações</button>

            </form>
        </div>
    </div>
@endsection

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
                    <input type="text" name="category" class="form-control" required value="{{ old('category') }}">
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
