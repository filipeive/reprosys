@extends('layouts.app')

@section('title', 'Finanças')
@section('page-title', 'Finanças')
@section('title-icon', 'fa-wallet')

@section('content')
<div class="container-fluid">
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Capital Atual</small>
                    <h3 class="mt-2 mb-1 text-primary">MT {{ number_format($currentCapital, 2, ',', '.') }}</h3>
                    <small class="text-muted">Saldo total nas contas financeiras</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Contas a Receber</small>
                    <h3 class="mt-2 mb-1 text-warning">MT {{ number_format($receivables, 2, ',', '.') }}</h3>
                    <small class="text-muted">Dívidas ativas de produtos e dinheiro</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Entradas do Mês</small>
                    <h3 class="mt-2 mb-1 text-success">MT {{ number_format($monthSummary['inflows'], 2, ',', '.') }}</h3>
                    <small class="text-muted">Recebimentos efetivos no período</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Saídas do Mês</small>
                    <h3 class="mt-2 mb-1 text-danger">MT {{ number_format($monthSummary['outflows'], 2, ',', '.') }}</h3>
                    <small class="text-muted">Despesas, salários e retiradas</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-university me-2 text-primary"></i>Contas Financeiras</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($accounts as $account)
                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="fw-semibold">{{ $account->name }}</div>
                                            <small class="text-muted">{{ strtoupper(str_replace('_', ' ', $account->type)) }}</small>
                                        </div>
                                        <span class="badge bg-light text-dark">Saldo</span>
                                    </div>
                                    <div class="h5 mb-1 {{ $account->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                        MT {{ number_format($account->current_balance, 2, ',', '.') }}
                                    </div>
                                    <small class="text-muted">Saldo inicial: MT {{ number_format($account->opening_balance, 2, ',', '.') }}</small>
                                    @if(userCan('manage_finances'))
                                        <form method="POST" action="{{ route('finances.accounts.update', $account) }}" class="mt-3">
                                            @csrf
                                            @method('PATCH')
                                            <label class="form-label small text-muted">Ajustar saldo inicial</label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="0.01" name="opening_balance" value="{{ old('opening_balance', $account->opening_balance) }}" class="form-control">
                                                <button type="submit" class="btn btn-outline-primary">Salvar</button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-plus-circle me-2 text-success"></i>Lançamento Manual</h6>
                </div>
                <div class="card-body">
                    @if(userCan('manage_finances'))
                        <div class="alert alert-light border small">
                            Pagamentos de salário são registrados no módulo de funcionários e aparecem aqui automaticamente no histórico.
                        </div>
                        <form method="POST" action="{{ route('finances.transactions.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Conta</label>
                                <select name="financial_account_id" class="form-select" required>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo de Movimento</label>
                                <select name="type" class="form-select" required>
                                    @foreach($manualTransactionTypes as $key => $type)
                                        <option value="{{ $key }}">{{ $type['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Valor</label>
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Data</label>
                                <input type="date" name="transaction_date" value="{{ now()->format('Y-m-d') }}" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <input type="text" name="description" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notas</label>
                                <textarea name="notes" rows="3" class="form-control"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i>Registrar Movimento
                            </button>
                        </form>
                    @else
                        <div class="text-muted small">Você tem acesso apenas para consulta financeira.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-wave-square me-2 text-info"></i>Resumo Diário</h6>
                    <small class="text-muted">Hoje</small>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-4">
                            <small class="text-muted d-block">Entradas</small>
                            <div class="fw-bold text-success">MT {{ number_format($todayInflow, 2, ',', '.') }}</div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Saídas</small>
                            <div class="fw-bold text-danger">MT {{ number_format($todayOutflow, 2, ',', '.') }}</div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Líquido</small>
                            <div class="fw-bold {{ ($todayInflow - $todayOutflow) >= 0 ? 'text-primary' : 'text-danger' }}">
                                MT {{ number_format($todayInflow - $todayOutflow, 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    <div class="finance-chart-wrap">
                        <canvas id="financeFlowChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-list me-2 text-secondary"></i>Histórico de Movimentos</h6>
                </div>
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('finances.index') }}" class="row g-2">
                        <div class="col-md-2">
                            <label class="form-label small">De</label>
                            <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Até</label>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Conta</label>
                            <select name="financial_account_id" class="form-select form-select-sm">
                                <option value="">Todas</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" @selected((string) $filters['financial_account_id'] === (string) $account->id)>{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Direção</label>
                            <select name="direction" class="form-select form-select-sm">
                                <option value="">Todas</option>
                                <option value="in" @selected($filters['direction'] === 'in')>Entradas</option>
                                <option value="out" @selected($filters['direction'] === 'out')>Saídas</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Tipo</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                @foreach($transactionTypes as $key => $type)
                                    <option value="{{ $key }}" @selected($filters['type'] === $key)>{{ $type['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Buscar</label>
                            <input type="text" name="search" value="{{ $filters['search'] }}" class="form-control form-control-sm" placeholder="Descrição">
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-filter me-1"></i>Filtrar
                            </button>
                            <a href="{{ route('finances.index') }}" class="btn btn-sm btn-outline-secondary">Limpar</a>
                        </div>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Descrição</th>
                                    <th>Conta</th>
                                    <th>Tipo</th>
                                    <th>Categoria</th>
                                    <th class="text-end">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                        <td>{{ $transaction->description }}</td>
                                        <td>{{ $transaction->account->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $transaction->direction === 'in' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $transaction->direction === 'in' ? 'Entrada' : 'Saída' }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $transactionTypes[$transaction->type]['label'] ?? ucfirst(str_replace('_', ' ', $transaction->type)) }}</small>
                                        </td>
                                        <td class="text-end fw-semibold {{ $transaction->direction === 'in' ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->direction === 'in' ? '+' : '-' }}MT {{ number_format($transaction->amount, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Nenhum movimento financeiro encontrado para os filtros aplicados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('financeFlowChart');
    if (!canvas) return;

    if (window.financeFlowChart instanceof Chart) {
        window.financeFlowChart.destroy();
    }

    window.financeFlowChart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: @json($cashFlowLabels),
            datasets: [
                {
                    label: 'Entradas',
                    data: @json($cashFlowInflows),
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderRadius: 6
                },
                {
                    label: 'Saídas',
                    data: @json($cashFlowOutflows),
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            resizeDelay: 150,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
    .finance-chart-wrap {
        position: relative;
        height: 260px;
        min-height: 260px;
    }
</style>
@endpush
