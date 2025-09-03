@extends('layouts.app')

@section('title', 'Pedido #' . $order->id)
@section('page-title', 'Pedido #' . $order->id)
@section('title-icon', 'fa-clipboard-list')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Pedidos</a></li>
    <li class="breadcrumb-item active">Pedido #{{ $order->id }}</li>
@endsection

@section('content')
    <div class="row">
        <!-- Informações Principais -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i> Pedido #{{ $order->id }}</h5>
                        <span class="badge {{ $order->status_badge }} fs-6">{{ $order->status_text }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-user me-2 text-primary"></i> Cliente</h6>
                            <p><strong>{{ $order->customer_name }}</strong></p>
                            @if ($order->customer_phone)
                                <p><i class="fas fa-phone me-2 text-muted"></i> {{ $order->customer_phone }}</p>
                            @endif
                            @if ($order->customer_email)
                                <p><i class="fas fa-envelope me-2 text-muted"></i> {{ $order->customer_email }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-info-circle me-2 text-primary"></i> Informações</h6>
                            <p><strong>Prioridade:</strong> <span
                                    class="badge {{ $order->priority_badge }}">{{ $order->priority_text }}</span></p>
                            <p><strong>Criado em:</strong> {{ $order->created_at->format('d/m/Y \à\s H:i') }}</p>
                            @if ($order->delivery_date)
                                <p>
                                    <strong>Entrega:</strong> {{ $order->delivery_date->format('d/m/Y') }}
                                    @if ($order->isOverdue())
                                        <span class="badge bg-danger ms-2"><i class="fas fa-exclamation-triangle me-1"></i>
                                            Atrasado</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    @if ($order->notes)
                        <div class="mt-3 p-3 bg-light border rounded">
                            <h6><i class="fas fa-sticky-note me-2"></i> Observações</h6>
                            <p>{{ $order->notes }}</p>
                        </div>
                    @endif

                    @if ($order->internal_notes)
                        <div class="mt-3 p-3 bg-info bg-opacity-10 border border-info rounded">
                            <h6><i class="fas fa-lock me-2"></i> Notas Internas</h6>
                            <p class="text-secondary">{{ $order->internal_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Itens do Pedido -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i> Itens do Pedido</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Preço Unit.</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            {{ $item->item_name }}
                                            @if ($item->product)
                                                <br><small class="text-muted">({{ $item->product->name }})</small>
                                            @endif
                                            @if ($item->description)
                                                <br><small class="text-muted">{{ $item->description }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">MT {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                        <td class="text-end">MT {{ number_format($item->total_price, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="3" class="text-end">Total Estimado:</td>
                                    <td class="text-end">MT {{ number_format($order->estimated_amount, 2, ',', '.') }}</td>
                                </tr>
                                @if ($order->advance_payment > 0)
                                    <tr class="text-success">
                                        <td colspan="3" class="text-end">Sinal Recebido:</td>
                                        <td class="text-end">MT {{ number_format($order->advance_payment, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">Restante:</td>
                                        <td class="text-end">MT
                                            {{ number_format($order->estimated_amount - $order->advance_payment, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações e Status -->
        <div class="col-lg-4">
            <!-- Status e Ações -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i> Ações</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if ($order->canBeCompleted())
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-lg btn-outline-secondary">
                                <i class="fas fa-edit me-2"></i> Editar Pedido
                            </a>
                        @endif

                        @if ($order->canBeDelivered())
                            <form action="{{ route('orders.convert-to-sale', $order) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-lg btn-success">
                                    <i class="fas fa-exchange-alt me-2"></i> Converter em Venda
                                </button>
                            </form>
                        @endif

                        @if ($order->canBeCancelled())
                            <form id="cancelOrderForm" action="{{ route('orders.destroy', $order) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-lg btn-outline-danger">
                                    <i class="fas fa-trash me-2"></i> Cancelar Pedido
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('orders.index') }}" class="btn btn-lg btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> Voltar à Lista
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dívida (se houver) -->
            @if ($order->debt)
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i> Dívida Relacionada</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Status:</strong> <span
                                class="badge {{ $order->debt->status_badge }}">{{ $order->debt->status_text }}</span></p>
                        <p><strong>Valor:</strong> MT {{ number_format($order->debt->remaining_amount, 2, ',', '.') }} de
                            {{ number_format($order->debt->original_amount, 2, ',', '.') }}</p>
                        @if ($order->debt->due_date)
                            <p><strong>Vencimento:</strong> {{ $order->debt->due_date->format('d/m/Y') }}</p>
                        @endif
                        <a href="{{ route('debts.show', $order->debt) }}" class="btn btn-sm btn-warning w-100 mt-2">
                            <i class="fas fa-eye me-2"></i> Ver Dívida
                        </a>
                    </div>
                </div>
            @endif

            <!-- Informações de Valor -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i> Valores</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Estimado:</strong> <span class="float-end fw-bold">MT
                            {{ number_format($order->estimated_amount, 2, ',', '.') }}</span></p>
                    <p class="mb-2"><strong>Sinal:</strong> <span class="float-end text-success">MT
                            {{ number_format($order->advance_payment, 2, ',', '.') }}</span></p>
                    <p class="mb-0"><strong>Restante:</strong> <span class="float-end text-danger fw-bold">MT
                            {{ number_format($order->estimated_amount - $order->advance_payment, 2, ',', '.') }}</span></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('cancelOrderForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (!confirm('Tem certeza que deseja cancelar este pedido?')) return;

        fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = "{{ route('orders.index') }}"; // redireciona para a lista
                } else {
                    alert(data.message);
                }
            })
            .catch(() => alert('Erro ao cancelar pedido.'));
    });
</script>
@endpush
