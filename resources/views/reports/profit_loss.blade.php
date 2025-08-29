@extends('layouts.app')

@section('title', 'Relatório de Lucros e Perdas')
@section('page-title', 'Lucros e Perdas')
@section('title-icon', 'fa-chart-line')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item active">Lucros e Perdas</li>
@endsection

@section('content')
    <!-- Header com botões de ação -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-chart-line me-2"></i>
                Relatório de Lucros e Perdas
            </h2>
            <p class="text-muted mb-0">Demonstração de resultados financeiros do período</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Voltar
        </a>
    </div>

    <!-- Filtros -->
    <div class="card mb-4 fade-in">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filtros de Período
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.profit-loss') }}" id="filters-form">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Data Inicial</label>
                        <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Data Final</label>
                        <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-success w-100" onclick="exportReport()">
                            <i class="fas fa-file-pdf me-1"></i> Exportar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Receita Total</h6>
                            <h3 class="mb-0 text-success fw-bold">{{ number_format($revenue, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">vendas brutas</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Custo dos Produtos</h6>
                            <h3 class="mb-0 text-warning fw-bold">{{ number_format($costOfGoodsSold, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">custo direto</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Despesas Operacionais</h6>
                            <h3 class="mb-0 text-danger fw-bold">{{ number_format($totalExpenses, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">custos fixos e variáveis</small>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-receipt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card {{ $profit >= 0 ? 'success' : 'danger' }} h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Resultado Líquido</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($profit, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">{{ $profit >= 0 ? 'Lucro' : 'Prejuízo' }}</small>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Demonstração de Resultados -->
    <div class="card fade-in mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-calculator me-2 text-primary"></i>
                Demonstração de Resultados
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <tbody>
                        <tr class="bg-success text-white">
                            <td><strong>RECEITAS</strong></td>
                            <td class="text-end">
                                <strong>{{ number_format($revenue, 2, ',', '.') }} MT</strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4">Vendas de Produtos</td>
                            <td class="text-end">{{ number_format($revenue, 2, ',', '.') }} MT</td>
                        </tr>
                        <tr class="bg-warning text-dark">
                            <td><strong>CUSTO DOS PRODUTOS VENDIDOS</strong></td>
                            <td class="text-end">
                                <strong>{{ number_format($costOfGoodsSold, 2, ',', '.') }} MT</strong>
                            </td>
                        </tr>
                        <tr class="bg-info text-white">
                            <td><strong>LUCRO BRUTO</strong></td>
                            <td class="text-end">
                                <strong>{{ number_format($revenue - $costOfGoodsSold, 2, ',', '.') }} MT</strong>
                            </td>
                        </tr>
                        <tr class="bg-danger text-white">
                            <td><strong>DESPESAS OPERACIONAIS</strong></td>
                            <td class="text-end">
                                <strong>{{ number_format($totalExpenses, 2, ',', '.') }} MT</strong>
                            </td>
                        </tr>
                        <tr class="bg-{{ $profit >= 0 ? 'success' : 'danger' }} text-white">
                            <td><strong>{{ $profit >= 0 ? 'LUCRO' : 'PREJUÍZO' }} LÍQUIDO</strong></td>
                            <td class="text-end">
                                <strong>{{ number_format($profit, 2, ',', '.') }} MT</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Gráfico de Resultados -->
    <div class="card fade-in mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-chart-bar me-2 text-success"></i>
                Análise de Resultados
            </h5>
        </div>
        <div class="card-body">
            <canvas id="profitLossChart" height="100"></canvas>
        </div>
    </div>

    <!-- Métricas Financeiras -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card fade-in h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-percentage me-2 text-success"></i>
                        Margem Bruta
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="display-4 fw-bold text-success mb-2">
                        {{ $revenue > 0 ? number_format((($revenue - $costOfGoodsSold) / $revenue) * 100, 1) : 0 }}%
                    </div>
                    <p class="text-muted mb-0">Lucro bruto sobre receita</p>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ $revenue > 0 ? (($revenue - $costOfGoodsSold) / $revenue) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card fade-in h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-chart-pie me-2 text-info"></i>
                        Margem Líquida
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="display-4 fw-bold text-info mb-2">
                        {{ $revenue > 0 ? number_format(($profit / $revenue) * 100, 1) : 0 }}%
                    </div>
                    <p class="text-muted mb-0">Lucro líquido sobre receita</p>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-info" role="progressbar"
                            style="width: {{ $revenue > 0 ? ($profit / $revenue) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card fade-in h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-coins me-2 text-warning"></i>
                        Retorno sobre Investimento
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="display-4 fw-bold text-warning mb-2">
                        {{ $costOfGoodsSold > 0 ? number_format(($profit / $costOfGoodsSold) * 100, 1) : 0 }}%
                    </div>
                    <p class="text-muted mb-0">ROI sobre custo dos produtos</p>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-warning" role="progressbar"
                            style="width: {{ $costOfGoodsSold > 0 ? ($profit / $costOfGoodsSold) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalhamento de Despesas -->
    @if ($totalExpenses > 0)
        <div class="card fade-in">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-receipt me-2 text-danger"></i>
                    Detalhamento de Despesas
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Categoria</th>
                                <th>Descrição</th>
                                <th class="text-end">Valor</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->limit(10)->get() as $expense)
                                <tr>
                                    <td><span
                                            class="badge bg-light text-dark">{{ $expense->category?->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ Str::limit($expense->description, 50) }}</td>
                                    <td class="text-end text-danger fw-bold">
                                        {{ number_format($expense->amount, 2, ',', '.') }} MT</td>
                                    <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if (\App\Models\Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->count() > 10)
                    <div class="card-footer bg-light">
                        <small class="text-muted">
                            Mostrando os 10 principais.
                            <a
                                href="{{ route('reports.index', ['date_from' => $dateFrom, 'date_to' => $dateTo, 'report_type' => 'expenses']) }}">Ver
                                todos</a>
                        </small>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function exportReport() {
            const params = new URLSearchParams();
            params.set('date_from', document.querySelector('input[name="date_from"]').value);
            params.set('date_to', document.querySelector('input[name="date_to"]').value);
            params.set('export', 'pdf');
            params.set('type', 'profit-loss');

            window.open('{{ route('reports.export') }}?' + params.toString(), '_blank');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Dados para o gráfico
            const revenue = {{ $revenue }};
            const costOfGoodsSold = {{ $costOfGoodsSold }};
            const totalExpenses = {{ $totalExpenses }};
            const profit = {{ $profit }};

            const ctx = document.getElementById('profitLossChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                {
                    labels: ['Receita', 'Custo dos Produtos', 'Despesas', 'Resultado'],
                    datasets: [{
                        label: 'Valores (MT)',
                        [revenue, costOfGoodsSold, totalExpenses, profit],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.7)',
                            'rgba(255, 193, 7, 0.7)',
                            'rgba(220, 53, 69, 0.7)',
                            profit >= 0 ? 'rgba(25, 135, 84, 0.7)' : 'rgba(220, 53, 69, 0.7)'
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(220, 53, 69, 1)',
                            profit >= 0 ? 'rgba(25, 135, 84, 1)' : 'rgba(220, 53, 69, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) =>
                                    `Valor: ${context.parsed.y.toLocaleString('pt-BR', { style: 'currency', currency: 'AOA' })}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => 'MT ' + value.toLocaleString('pt-BR')
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .stats-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .stats-card.success {
            border-left-color: #059669;
        }

        .stats-card.warning {
            border-left-color: #ea580c;
        }

        .stats-card.danger {
            border-left-color: #dc2626;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .loading-spinner {
            width: 30px;
            height: 30px;
            border: 3px solid #f3f4f6;
            border-top: 3px solid #0d6efd;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush
