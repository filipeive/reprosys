@extends('layouts.app')

@section('title', 'Relatórios')
@section('page-title', 'Relatórios')
@section('title-icon', 'fa-chart-bar')
@section('breadcrumbs')
    <li class="breadcrumb-item active">Relatórios</li>
@endsection

@section('content')
    <!-- Header com Botões de Acesso Rápido -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-chart-bar me-2"></i>
                Painel de Relatórios
            </h2>
            <p class="text-muted mb-0">Visualize dados financeiros, vendas, estoque e desempenho</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('reports.daily-sales') }}" class="btn btn-primary btn-sm" target="_blank" title="Vendas Diárias">
                <i class="fas fa-calendar-day me-1"></i> Diárias
            </a>
            <a href="{{ route('reports.monthly-sales') }}" class="btn btn-info btn-sm" target="_blank"
                title="Vendas Mensais">
                <i class="fas fa-calendar-alt me-1"></i> Mensais
            </a>
            <a href="{{ route('reports.sales-by-product') }}" class="btn btn-success btn-sm" target="_blank"
                title="Vendas por Produto">
                <i class="fas fa-box me-1"></i> Por Produto
            </a>
            <a href="{{ route('reports.profit-loss') }}" class="btn btn-warning btn-sm" target="_blank"
                title="Lucros e Perdas">
                <i class="fas fa-chart-line me-1"></i> Lucro/Prejuízo
            </a>
            <a href="{{ route('reports.low-stock') }}" class="btn btn-danger btn-sm" target="_blank" title="Baixo Estoque">
                <i class="fas fa-exclamation-triangle me-1"></i> Estoque Baixo
            </a>
            <a href="{{ route('reports.inventory') }}" class="btn btn-secondary btn-sm" target="_blank" title="Inventário">
                <i class="fas fa-boxes me-1"></i> Inventário
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4 fade-in">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filtros de Período e Tipo
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" id="filters-form">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Inicial</label>
                        <input type="date" class="form-control" name="date_from"
                            value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Final</label>
                        <input type="date" class="form-control" name="date_to"
                            value="{{ request('date_to', now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Tipo de Relatório</label>
                        <select class="form-select" name="report_type">
                            <option value="all" {{ request('report_type') == 'all' ? 'selected' : '' }}>Todos</option>
                            <option value="sales" {{ request('report_type') == 'sales' ? 'selected' : '' }}>Vendas
                            </option>
                            <option value="expenses" {{ request('report_type') == 'expenses' ? 'selected' : '' }}>Despesas
                            </option>
                            <option value="products" {{ request('report_type') == 'products' ? 'selected' : '' }}>Produtos
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <a href="{{ route('reports.export', array_merge(request()->all(), ['format' => 'pdf'])) }}"
                                class="btn btn-danger btn-sm w-100" target="_blank" title="Exportar PDF">
                                <i class="fas fa-file-pdf me-1"></i> PDF
                            </a>
                            <a href="{{ route('reports.export', array_merge(request()->all(), ['format' => 'excel'])) }}"
                                class="btn btn-success btn-sm w-100" target="_blank" title="Exportar Excel">
                                <i class="fas fa-file-excel me-1"></i> XLS
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Total de Vendas</h6>
                            <h3 class="mb-0 text-primary fw-bold">{{ $totalSales }}</h3>
                            <small class="text-muted">transações no período</small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Receita Total</h6>
                            <h3 class="mb-0 text-success fw-bold">{{ number_format($totalRevenue, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">em vendas</small>
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
                            <h6 class="text-muted mb-2 fw-semibold">Despesas Totais</h6>
                            <h3 class="mb-0 text-warning fw-bold">{{ number_format($totalExpenses, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">registradas</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-receipt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card {{ $totalRevenue - $totalExpenses >= 0 ? 'success' : 'danger' }} h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Lucro/Prejuízo</h6>
                            <h3 class="mb-0 fw-bold">
                                {{ number_format($totalRevenue - $totalExpenses, 2, ',', '.') }} MT
                            </h3>
                            <small class="text-muted">
                                {{ $totalRevenue - $totalExpenses >= 0 ? 'Lucro' : 'Prejuízo' }}
                            </small>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <!-- Gráfico de Vendas -->
        <div class="col-lg-8">
            <div class="card fade-in">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-chart-area me-2 text-primary"></i>
                        Evolução das Vendas
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Métodos de Pagamento -->
        <div class="col-lg-4">
            <div class="card fade-in">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-chart-pie me-2 text-info"></i>
                        Métodos de Pagamento
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentMethodChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabelas de Dados -->
    <div class="row g-4">
        <!-- Produtos Mais Vendidos -->
        <div class="col-lg-6">
            <div class="card fade-in">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-trophy me-2 text-warning"></i>
                        Produtos Mais Vendidos
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Qtd Vendida</th>
                                    <th class="text-end">Receita</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $product)
                                    <tr>
                                        <td><strong>{{ $product->name }}</strong></td>
                                        <td class="text-center">{{ $product->total_quantity }}</td>
                                        <td class="text-end text-success fw-bold">
                                            {{ number_format($product->total_revenue, 2, ',', '.') }} MT
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="fas fa-box-open me-2"></i> Nenhum produto vendido no período
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendas Recentes -->
        <div class="col-lg-6">
            <div class="card fade-in">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-clock me-2 text-primary"></i>
                        Vendas Recentes
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Cliente</th>
                                    <th class="text-end">Total</th>
                                    <th>Pagamento</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSales as $sale)
                                    <tr>
                                        <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                                        <td>{{ $sale->customer_name ?? 'N/A' }}</td>
                                        <td class="text-end text-success fw-bold">
                                            {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ (($sale->payment_method === 'cash'
                                                            ? 'success'
                                                            : $sale->payment_method === 'card')
                                                        ? 'primary'
                                                        : $sale->payment_method === 'transfer')
                                                    ? 'info'
                                                    : 'warning' }}">
                                                {{ ucfirst($sale->payment_method) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-shopping-cart me-2"></i> Nenhuma venda registrada
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabelas Condicionais -->
    @if (request('report_type') === 'sales' || request('report_type') === 'all')
        <div class="card mt-4 fade-in">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-table me-2 text-success"></i>
                    Vendas Detalhadas
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="sales-table">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Data</th>
                                <th>Cliente</th>
                                <th>Telefone</th>
                                <th>Itens</th>
                                <th class="text-end">Total</th>
                                <th>Pagamento</th>
                                <th>Vendedor</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td><strong class="text-primary">#{{ $sale->id }}</strong></td>
                                    <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                                    <td>{{ $sale->customer_name ?? 'N/A' }}</td>
                                    <td>{{ $sale->customer_phone ?? 'N/A' }}</td>
                                    <td><span class="badge bg-light text-dark">{{ $sale->items->count() }}</span></td>
                                    <td class="text-end text-success fw-bold">
                                        {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                                    </td>
                                    <td>
                                    <td>
                                        @php
                                            $paymentClass = match ($sale->payment_method) {
                                                'cash' => 'success',
                                                'card' => 'primary',
                                                'transfer' => 'info',
                                                'credit' => 'warning',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $paymentClass }}">
                                            {{ ucfirst($sale->payment_method) }}
                                        </span>
                                    </td>

                                    </td>
                                    <td>{{ $sale->user->name }}</td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-info"
                                                title="Ver Detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('sales.print', $sale) }}" class="btn btn-outline-secondary"
                                                target="_blank" title="Imprimir">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fas fa-shopping-cart fa-2x mb-3 opacity-50"></i>
                                        <p>Nenhuma venda encontrada no período.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if (request('report_type') === 'expenses' || request('report_type') === 'all')
        <div class="card mt-4 fade-in">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-receipt me-2 text-danger"></i>
                    Despesas
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="expenses-table">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Data</th>
                                <th>Categoria</th>
                                <th>Descrição</th>
                                <th class="text-end">Valor</th>
                                <th>Recibo</th>
                                <th>Usuário</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $expense)
                                <tr>
                                    <td><strong class="text-danger">#{{ $expense->id }}</strong></td>
                                    <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                                    <td><span
                                            class="badge bg-light text-dark">{{ $expense->category->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ Str::limit($expense->description, 50) }}</td>
                                    <td class="text-end text-danger fw-bold">
                                        {{ number_format($expense->amount, 2, ',', '.') }} MT
                                    </td>
                                    <td>{{ $expense->receipt_number ?? 'N/A' }}</td>
                                    <td>{{ $expense->user->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-money-bill-wave fa-2x mb-3 opacity-50"></i>
                                        <p>Nenhuma despesa registrada no período.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if (request('report_type') === 'products' || request('report_type') === 'all')
        <div class="card mt-4 fade-in">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-boxes me-2 text-info"></i>
                    Relatório de Estoque
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="products-table">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th>Categoria</th>
                                <th>Tipo</th>
                                <th class="text-center">Estoque</th>
                                <th class="text-center">Mínimo</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Custo</th>
                                <th class="text-end">Venda</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td><strong>{{ $product->name }}</strong></td>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $product->type === 'product' ? 'primary' : 'info' }}">
                                            {{ $product->type === 'product' ? 'Produto' : 'Serviço' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        {{ $product->type === 'product' ? $product->stock_quantity : '-' }}</td>
                                    <td class="text-center">
                                        {{ $product->type === 'product' ? $product->min_stock_level : '-' }}</td>
                                    <td class="text-center">
                                        @if ($product->type === 'product')
                                            @if ($product->stock_quantity <= 0)
                                                <span class="badge bg-danger">Esgotado</span>
                                            @elseif($product->stock_quantity <= $product->min_stock_level)
                                                <span class="badge bg-warning">Baixo</span>
                                            @else
                                                <span class="badge bg-success">OK</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format($product->purchase_price, 2, ',', '.') }} MT
                                    </td>
                                    <td class="text-end text-success fw-bold">
                                        {{ number_format($product->selling_price, 2, ',', '.') }} MT</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fa-2x mb-3 opacity-50"></i>
                                        <p>Nenhum produto encontrado.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Vendas
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: @json($salesChartLabels),
                    datasets: [{
                        label: 'Vendas (MT)',
                        data: @json($salesChartData),
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => value + ' MT'
                            }
                        }
                    }
                }
            });

            // Gráfico de Métodos de Pagamento
            const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
            new Chart(paymentCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($paymentMethodLabels),
                    datasets: [{
                        data: @json($paymentMethodData),
                        backgroundColor: [
                            '#28a745', '#007bff', '#17a2b8', '#ffc107'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
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

        .stats-card.primary {
            border-left-color: #1e3a8a;
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

        .btn-sm i {
            font-size: 0.85em;
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
