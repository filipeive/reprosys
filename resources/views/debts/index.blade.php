@extends('layouts.app')

@section('title', 'Dívidas')
@section('title-icon', 'fa-credit-card')
@section('page-title', 'Gestão de Dívidas')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dívidas</li>
@endsection

@section('content')
<div class="fade-in">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-0">MT {{ number_format($stats['total_active'], 2) }}</h4>
                        <small class="text-muted">Total em Aberto</small>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-exclamation-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-0">{{ $stats['count_active'] }}</h4>
                        <small class="text-muted">Dívidas Ativas</small>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-file-invoice-dollar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-0">MT {{ number_format($stats['total_overdue'], 2) }}</h4>
                        <small class="text-muted">Vencidas</small>
                    </div>
                    <div class="text-danger">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-0">{{ $stats['count_paid_this_month'] }}</h4>
                        <small class="text-muted">Pagas Este Mês</small>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros e Actions -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-8">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Todos</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativa</option>
                                <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Parcial</option>
                                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Vencida</option>
                                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paga</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cliente</label>
                            <input type="text" name="customer" class="form-control" 
                                   placeholder="Nome do cliente" value="{{ request('customer') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Data Início</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Data Fim</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <div class="form-check mb-2">
                                <input type="checkbox" name="overdue_only" class="form-check-input" value="1" 
                                       {{ request('overdue_only') ? 'checked' : '' }}>
                                <label class="form-check-label">Apenas Vencidas</label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search me-1"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group">
                        <a href="{{ route('debts.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i> Nova Dívida
                        </a>
                        <a href="{{ route('debts.debtors-report') }}" class="btn btn-outline-primary">
                            <i class="fas fa-chart-bar me-2"></i> Relatório
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Dívidas -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Descrição</th>
                            <th>Valor Original</th>
                            <th>Valor Pago</th>
                            <th>Restante</th>
                            <th>Vencimento</th>
                            <th>Status</th>
                            <th width="120">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debts as $debt)
                        <tr class="{{ $debt->is_overdue ? 'table-warning' : '' }}">
                            <td>
                                <span class="fw-bold text-primary">#{{ $debt->id }}</span>
                                <br><small class="text-muted">{{ $debt->debt_date->format('d/m/Y') }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $debt->customer_name }}</div>
                                @if($debt->customer_phone)
                                    <small class="text-muted">
                                        <i class="fas fa-phone"></i> {{ $debt->customer_phone }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div>{{ Str::limit($debt->description, 40) }}</div>
                                @if($debt->sale_id)
                                    <small class="text-muted">Venda #{{ $debt->sale_id }}</small>
                                @elseif($debt->order_id)
                                    <small class="text-muted">Pedido #{{ $debt->order_id }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">MT {{ number_format($debt->original_amount, 2) }}</div>
                            </td>
                            <td>
                                @if($debt->paid_amount > 0)
                                    <div class="text-success fw-semibold">MT {{ number_format($debt->paid_amount, 2) }}</div>
                                @else
                                    <span class="text-muted">MT 0,00</span>
                                @endif
                            </td>
                            <td>
                                @if($debt->remaining_amount > 0)
                                    <div class="fw-bold {{ $debt->is_overdue ? 'text-danger' : 'text-warning' }}">
                                        MT {{ number_format($debt->remaining_amount, 2) }}
                                    </div>
                                @else
                                    <span class="text-success">MT 0,00</span>
                                @endif
                            </td>
                            <td>
                                @if($debt->due_date)
                                    <div class="{{ $debt->is_overdue ? 'text-danger' : '' }}">
                                        {{ $debt->due_date->format('d/m/Y') }}
                                    </div>
                                    @if($debt->is_overdue)
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> 
                                            {{ abs($debt->days_overdue) }} dias
                                        </small>
                                    @endif
                                @else
                                    <span class="text-muted">Sem prazo</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $debt->status_badge }}">
                                    {{ $debt->status_text }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('debts.show', $debt) }}" 
                                       class="btn btn-outline-primary" title="Ver Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($debt->canAddPayment())
                                        <button type="button" class="btn btn-outline-success" 
                                                onclick="showPaymentModal({{ $debt->id }}, {{ $debt->remaining_amount }})"
                                                title="Adicionar Pagamento">
                                            <i class="fas fa-money-bill"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-credit-card fa-3x mb-3 opacity-50"></i>
                                    <p>Nenhuma dívida encontrada</p>
                                    <a href="{{ route('debts.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Registrar Primeira Dívida
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginação -->
    @if($debts->hasPages())
    <div class="mt-4">
        {{ $debts->links() }}
    </div>
    @endif
</div>

<!-- Modal para Adicionar Pagamento -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-money-bill me-2"></i>
                    Registrar Pagamento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Valor do Pagamento *</label>
                        <div class="input-group">
                            <span class="input-group-text">MT</span>
                            <input type="number" step="0.01" name="amount" id="paymentAmount" 
                                   class="form-control" required>
                        </div>
                        <small class="text-muted">Valor máximo: <span id="maxAmount"></span></small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Forma de Pagamento *</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">Dinheiro</option>
                            <option value="card">Cartão</option>
                            <option value="transfer">Transferência</option>
                            <option value="pix">PIX</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Data do Pagamento *</label>
                        <input type="date" name="payment_date" class="form-control" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Observações</label>
                        <textarea name="notes" class="form-control" rows="2" 
                                  placeholder="Observações sobre o pagamento..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i> Registrar Pagamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
function showPaymentModal(debtId, remainingAmount) {
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    const form = document.getElementById('paymentForm');
    
    form.action = `/debts/${debtId}/add-payment`;
    document.getElementById('paymentAmount').max = remainingAmount;
    document.getElementById('maxAmount').textContent = `MT ${remainingAmount.toFixed(2)}`;
    
    modal.show();
}

// Auto-hide alerts
setTimeout(() => {
    document.querySelectorAll('.alert.fade.show').forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        setTimeout(() => bsAlert.close(), 5000);
    });
}, 100);
</script>
@endsection
@endsection