
<div class="debt-details">
    <!-- Cabeçalho da Dívida -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h5 class="mb-1">Dívida #{{ $debt->id }}</h5>
            <p class="text-muted mb-2">
                <i class="fas fa-calendar me-1"></i>
                Criada em {{ $debt->debt_date->format('d/m/Y') }}
                @if($debt->due_date)
                    • Vencimento: {{ $debt->due_date->format('d/m/Y') }}
                @endif
            </p>
            <span class="badge {{ $debt->status_badge }} fs-6">{{ $debt->status_text }}</span>
        </div>
        <div class="col-md-4 text-end">
            <div class="mb-2">
                <small class="text-muted d-block">Total Original</small>
                <h4 class="mb-0 text-primary">MT {{ number_format($debt->original_amount, 2, ',', '.') }}</h4>
            </div>
            @if($debt->remaining_amount > 0)
                <small class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Restante: MT {{ number_format($debt->remaining_amount, 2, ',', '.') }}
                </small>
            @endif
        </div>
    </div>

    <!-- Dados do Cliente -->
    <div class="card mb-3">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-user me-2"></i>Dados do Cliente</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Nome:</strong> {{ $debt->customer_name }}
                </div>
                <div class="col-md-6">
                    @if($debt->customer_phone)
                        <strong>Telefone:</strong> {{ $debt->customer_phone }}
                    @endif
                </div>
            </div>
            @if($debt->customer_document)
                <div class="row mt-2">
                    <div class="col-md-6">
                        <strong>Documento:</strong> {{ $debt->customer_document }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Produtos da Dívida -->
    @if($debt->items->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-box me-2"></i>Produtos da Dívida</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th class="text-center">Qtd</th>
                                <th class="text-end">Preço Unit.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($debt->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->product->name }}</div>
                                        @if($item->product->category)
                                            <small class="text-muted">{{ $item->product->category->name }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">MT {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                    <td class="text-end fw-semibold">MT {{ number_format($item->total_price, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th class="text-end">MT {{ number_format($debt->original_amount, 2, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Histórico de Pagamentos -->
    @if($debt->payments->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-money-bill me-2"></i>Histórico de Pagamentos</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Data</th>
                                <th>Valor</th>
                                <th>Forma</th>
                                <th>Usuário</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($debt->payments->sortByDesc('payment_date') as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                    <td class="text-success fw-semibold">MT {{ number_format($payment->amount, 2, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $payment->payment_method_badge }}">
                                            {{ $payment->payment_method_text }}
                                        </span>
                                    </td>
                                    <td>{{ $payment->user->name }}</td>
                                    <td>
                                        @if($payment->notes)
                                            <small class="text-muted">{{ $payment->notes }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Venda Associada -->
    @if($debt->sale)
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Venda Associada</h6>
            </div>
            <div class="card-body">
                <p class="mb-1">
                    <strong>Venda #{{ $debt->sale->id }}</strong> - 
                    Realizada em {{ $debt->sale->sale_date->format('d/m/Y') }}
                </p>
                <p class="mb-0">
                    <span class="badge bg-success">Venda Confirmada</span>
                </p>
            </div>
        </div>
    @endif

    <!-- Observações -->
    @if($debt->notes)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Observações</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $debt->notes }}</p>
            </div>
        </div>
    @endif

    <!-- Resumo Financeiro -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="text-center p-3 bg-light rounded">
                <small class="text-muted d-block">Valor Original</small>
                <h5 class="mb-0 text-primary">MT {{ number_format($debt->original_amount, 2, ',', '.') }}</h5>
            </div>
        </div>
        <div class="col-md-4">
            <div class="text-center p-3 bg-light rounded">
                <small class="text-muted d-block">Total Pago</small>
                <h5 class="mb-0 text-success">MT {{ number_format($debt->paid_amount, 2, ',', '.') }}</h5>
            </div>
        </div>
        <div class="col-md-4">
            <div class="text-center p-3 bg-light rounded">
                <small class="text-muted d-block">Restante</small>
                <h5 class="mb-0 text-{{ $debt->remaining_amount > 0 ? 'danger' : 'success' }}">
                    MT {{ number_format($debt->remaining_amount, 2, ',', '.') }}
                </h5>
            </div>
        </div>
    </div>

    <!-- Ações Disponíveis -->
    @if($debt->canAddPayment())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-success" 
                            onclick="openPaymentOffcanvas({{ $debt->id }}, '{{ $debt->customer_name }}', {{ $debt->remaining_amount }})">
                        <i class="fas fa-money-bill me-2"></i>Registrar Pagamento
                    </button>
                    @if($debt->status === 'active')
                        <button type="button" class="btn btn-outline-danger" 
                                onclick="confirmCancelDebt({{ $debt->id }})">
                            <i class="fas fa-ban me-2"></i>Cancelar Dívida
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.debt-details .card {
    border: 1px solid #e9ecef;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.debt-details .badge {
    font-size: 0.85em;
}

.debt-details .table th {
    font-weight: 600;
    font-size: 0.875rem;
}

.debt-details .bg-light {
    border: 1px solid #dee2e6;
}
</style>