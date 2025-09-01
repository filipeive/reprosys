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
            @php
                $statusBadge = match($debt->status) {
                    'active' => 'bg-warning',
                    'paid' => 'bg-success',
                    'cancelled' => 'bg-secondary',
                    'overdue' => 'bg-danger',
                    default => 'bg-secondary'
                };
                $statusText = match($debt->status) {
                    'active' => 'Ativa',
                    'paid' => 'Paga',
                    'cancelled' => 'Cancelada',
                    'overdue' => 'Vencida',
                    default => ucfirst($debt->status)
                };
            @endphp
            <span class="badge {{ $statusBadge }} fs-6">{{ $statusText }}</span>
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
            <div class="row mt-2">
                <div class="col-md-12">
                    <strong>Descrição:</strong> {{ $debt->description }}
                    @if($debt->notes)
                        <br><small class="text-muted"><strong>Obs:</strong> {{ $debt->notes }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Produtos da Dívida -->
    @if($debt->items->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-box me-2"></i>Produtos da Dívida ({{ $debt->items->count() }} {{ $debt->items->count() === 1 ? 'item' : 'itens' }})</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produto/Serviço</th>
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
                                        <small class="d-block text-muted">{{ $item->product->type === 'product' ? 'Produto' : 'Serviço' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $item->quantity }} {{ $item->product->unit ?? 'unid' }}</span>
                                    </td>
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
                <h6 class="mb-0"><i class="fas fa-money-bill me-2"></i>Histórico de Pagamentos ({{ $debt->payments->count() }} {{ $debt->payments->count() === 1 ? 'pagamento' : 'pagamentos' }})</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Data</th>
                                <th class="text-end">Valor</th>
                                <th>Forma</th>
                                <th>Usuário</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($debt->payments->sortByDesc('payment_date') as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                    <td class="text-end text-success fw-semibold">MT {{ number_format($payment->amount, 2, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $methodBadge = match($payment->payment_method) {
                                                'cash' => 'bg-success',
                                                'card' => 'bg-primary',
                                                'transfer' => 'bg-info',
                                                'mpesa' => 'bg-warning text-dark',
                                                'emola' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                            $methodText = match($payment->payment_method) {
                                                'cash' => 'Dinheiro',
                                                'card' => 'Cartão',
                                                'transfer' => 'Transferência',
                                                'mpesa' => 'M-Pesa',
                                                'emola' => 'E-mola',
                                                default => ucfirst($payment->payment_method)
                                            };
                                        @endphp
                                        <span class="badge {{ $methodBadge }}">{{ $methodText }}</span>
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
                        <tfoot class="table-light">
                            <tr>
                                <th>Total Pago:</th>
                                <th class="text-end text-success">MT {{ number_format($debt->original_amount - $debt->remaining_amount, 2, ',', '.') }}</th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
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
                <h5 class="mb-0 text-success">MT {{ number_format($debt->original_amount - $debt->remaining_amount, 2, ',', '.') }}</h5>
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

    <!-- Status e Vencimento -->
    @if($debt->due_date)
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-{{ $debt->status === 'overdue' || ($debt->status === 'active' && $debt->due_date->isPast()) ? 'warning' : 'info' }}">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <strong>Vencimento:</strong> {{ $debt->due_date->format('d/m/Y') }}
                    
                    @if($debt->status === 'active')
                        @php $daysTodue = now()->diffInDays($debt->due_date, false); @endphp
                        @if($daysTodue < 0)
                            <br><small class="text-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                Vencida há {{ abs($daysTodue) }} {{ abs($daysTodue) === 1 ? 'dia' : 'dias' }}
                            </small>
                        @elseif($daysTodue <= 7)
                            <br><small class="text-warning">
                                <i class="fas fa-clock"></i>
                                Vence em {{ $daysTodue }} {{ $daysTodue === 1 ? 'dia' : 'dias' }}
                            </small>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Ações Disponíveis -->
    @if($debt->status === 'active' && $debt->remaining_amount > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-success" 
                            onclick="parent.openPaymentOffcanvas({{ $debt->id }}, '{{ $debt->customer_name }}', {{ $debt->remaining_amount }})">
                        <i class="fas fa-money-bill me-2"></i>Registrar Pagamento
                    </button>
                    <button type="button" class="btn btn-outline-success" 
                            onclick="parent.markAsPaid({{ $debt->id }})">
                        <i class="fas fa-check-circle me-2"></i>Marcar como Paga
                    </button>
                    <button type="button" class="btn btn-outline-danger" 
                            onclick="parent.confirmCancelDebt({{ $debt->id }})">
                        <i class="fas fa-ban me-2"></i>Cancelar
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Informações de Sistema -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Informações do Sistema</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Criado por:</small>
                            <div class="fw-semibold">{{ $debt->user->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Data de criação:</small>
                            <div class="fw-semibold">{{ $debt->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        @if($debt->updated_at != $debt->created_at)
                            <div class="col-md-6 mt-2">
                                <small class="text-muted">Última atualização:</small>
                                <div class="fw-semibold">{{ $debt->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        @endif
                        @if($debt->payments->count() > 0)
                            <div class="col-md-6 mt-2">
                                <small class="text-muted">Último pagamento:</small>
                                <div class="fw-semibold">{{ $debt->payments->sortByDesc('payment_date')->first()->payment_date->format('d/m/Y') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
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

.debt-details .alert {
    border: none;
    border-left: 4px solid;
}

.debt-details .alert-info {
    border-left-color: #0dcaf0;
}

.debt-details .alert-warning {
    border-left-color: #ffc107;
}
</style>