@extends('layouts.app')

@section('title', 'Pedido #' . $order->id)
@section('title-icon', 'fa-clipboard-list')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Pedidos</a></li>
    <li class="breadcrumb-item active">Pedido #{{ $order->id }}</li>
@endsection

@section('content')
<main>
    <div class="row">
        <div class="col-lg-8">
            <!-- Informações do Cliente -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Cliente</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nome:</strong> {{ $order->customer_name }}</p>
                    @if($order->customer_phone)
                        <p><strong>Telefone:</strong> {{ $order->customer_phone }}</p>
                    @endif
                    @if($order->customer_email)
                        <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                    @endif
                </div>
            </div>

            <!-- Itens do Pedido -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i>Itens do Pedido</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantidade</th>
                                <th>Preço Unit.</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->item_name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>MT {{ number_format($item->unit_price, 2) }}</td>
                                    <td>MT {{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Estimado:</th>
                                <th>MT {{ number_format($order->estimated_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status e Ações -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6>Status:</h6>
                    <span class="badge {{ $order->status_badge }} mb-3">
                        {{ $order->status_text }}
                    </span>

                    <h6 class="mt-3">Prioridade:</h6>
                    <span class="badge {{ $order->priority_badge }}">
                        {{ $order->priority_text }}
                    </span>

                    @if($order->delivery_date)
                        <h6 class="mt-3">Entrega:</h6>
                        <p class="{{ $order->isOverdue() ? 'text-danger' : '' }}">
                            {{ $order->delivery_date->format('d/m/Y') }}
                            @if($order->isOverdue())
                                <br><small><i class="fas fa-exclamation-triangle"></i> Atrasado</small>
                            @endif
                        </p>
                    @endif

                    <!-- Botões de Ação -->
                    <div class="mt-4">
                        @if($order->canBeCompleted())
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-outline-secondary w-100 mb-2">
                                <i class="fas fa-edit me-1"></i> Editar
                            </a>
                        @endif

                        @if($order->canBeDelivered())
                            <form action="{{ route('orders.convertToSale', $order) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success w-100">
                                    <i class="fas fa-exchange-alt me-1"></i> Converter em Venda
                                </button>
                            </form>
                        @endif

                        @if($order->canBeCancelled())
                            <form action="{{ route('orders.destroy', $order) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                    <i class="fas fa-trash me-1"></i> Cancelar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Observações -->
            @if($order->notes || $order->internal_notes)
                <div class="card">
                    <div class="card-body">
                        @if($order->notes)
                            <p><strong>Observações:</strong><br>{{ $order->notes }}</p>
                        @endif
                        @if($order->internal_notes)
                            <p><strong>Notas Internas:</strong><br>{{ $order->internal_notes }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</main>
@endsection