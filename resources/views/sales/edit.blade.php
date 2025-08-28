@extends('layouts.app')

@section('title', 'Editar Venda')

@section('content_header')
    <h1>Editar Venda</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('sales.update', $sale) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="customer_name" class="form-label">Cliente</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ old('customer_name', $sale->customer_name) }}">
            </div>

            <div class="mb-3">
                <label for="customer_phone" class="form-label">Telefone</label>
                <input type="text" class="form-control" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $sale->customer_phone) }}">
            </div>

            <div class="mb-3">
                <label for="payment_method" class="form-label">Método de Pagamento</label>
                <select class="form-control" id="payment_method" name="payment_method" required>
                    <option value="cash" {{ $sale->payment_method == 'cash' ? 'selected' : '' }}>Dinheiro</option>
                    <option value="card" {{ $sale->payment_method == 'card' ? 'selected' : '' }}>Cartão</option>
                    <option value="transfer" {{ $sale->payment_method == 'transfer' ? 'selected' : '' }}>Transferência</option>
                    <option value="credit" {{ $sale->payment_method == 'credit' ? 'selected' : '' }}>Crédito</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Observações</label>
                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes', $sale->notes) }}</textarea>
            </div>

            {{-- Itens da venda (apenas exibição, edição avançada exige lógica JS) --}}
            <div class="mb-3">
                <label class="form-label">Itens da Venda</label>
                <ul class="list-group">
                    @foreach($sale->items as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $item->product->name ?? 'Produto removido' }} 
                            <span>
                                {{ $item->quantity }} x {{ number_format($item->unit_price, 2, ',', '.') }} = 
                                <strong>{{ number_format($item->total_price, 2, ',', '.') }}</strong>
                            </span>
                        </li>
                    @endforeach
                </ul>
                <small class="text-muted">Para alterar itens, cancele e refaça a venda.</small>
            </div>

            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@stop