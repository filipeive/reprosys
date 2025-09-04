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
            {{-- Relatórios básicos --}}
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
            <a href="{{ route('reports.cash-flow') }}" class="btn btn-dark btn-sm" target="_blank" title="Fluxo de Caixa">
                <i class="fas fa-money-bill-wave me-1"></i> Fluxo Caixa
            </a>
            <a href="{{ route('reports.low-stock') }}" class="btn btn-danger btn-sm" target="_blank" title="Baixo Estoque">
                <i class="fas fa-exclamation-triangle me-1"></i> Estoque Baixo
            </a>
            <a href="{{ route('reports.inventory') }}" class="btn btn-secondary btn-sm" target="_blank" title="Inventário">
                <i class="fas fa-boxes me-1"></i> Inventário
            </a>

            {{-- Análises avançadas --}}
            {{-- <a href="{{ route('reports.customer-profitability') }}" class="btn btn-outline-primary btn-sm" target="_blank"
                title="Rentabilidade de Clientes">
                <i class="fas fa-users me-1"></i> Rentabilidade Clientes
            </a> --}}
            <a href="{{ route('reports.abc-analysis') }}" class="btn btn-outline-info btn-sm" target="_blank"
                title="Análise ABC">
                <i class="fas fa-sort-amount-down me-1"></i> ABC
            </a>
            <a href="{{ route('reports.period-comparison') }}" class="btn btn-outline-success btn-sm" target="_blank"
                title="Comparação de Períodos">
                <i class="fas fa-exchange-alt me-1"></i> Comparação
            </a>
            <a href="{{ route('reports.business-insights') }}" class="btn btn-outline-warning btn-sm" target="_blank"
                title="Insights do Negócio">
                <i class="fas fa-lightbulb me-1"></i> Insights
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
                        <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Final</label>
                        <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Tipo de Relatório</label>
                        <select class="form-select" name="report_type">
                            <option value="all" {{ $reportType == 'all' ? 'selected' : '' }}>Todos</option>
                            <option value="sales" {{ $reportType == 'sales' ? 'selected' : '' }}>Vendas</option>
                            <option value="expenses" {{ $reportType == 'expenses' ? 'selected' : '' }}>Despesas</option>
                            <option value="products" {{ $reportType == 'products' ? 'selected' : '' }}>Produtos</option>
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
                            <a href="{{ route('reports.export', array_merge(['date_from' => $dateFrom, 'date_to' => $dateTo, 'report_type' => $reportType], ['format' => 'pdf'])) }}"
                                class="btn btn-danger btn-sm w-100" target="_blank" title="Exportar PDF">
                                <i class="fas fa-file-pdf me-1"></i> PDF
                            </a>
                            <a href="{{ route('reports.export', array_merge(['date_from' => $dateFrom, 'date_to' => $dateTo, 'report_type' => $reportType], ['format' => 'excel'])) }}"
                                class="btn btn-success btn-sm w-100" target="_blank" title="Exportar Excel">
                                <i class="fas fa-file-excel me-1"></i> XLS
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards de Resumo Principal -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Total de Vendas</h6>
                            <h3 class="mb-0 text-primary fw-bold">{{ $totalSales }}</h3>
                            <small class="text-muted">em vendas
                                @if (isset($revenueGrowth))
                                    <span class="badge bg-{{ $revenueGrowth >= 0 ? 'success' : 'danger' }} ms-1">
                                        {{ $revenueGrowth >= 0 ? '+' : '' }}{{ number_format($revenueGrowth, 1) }}%
                                    </span>
                                @endif
                            </small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Lucro Bruto</h6>
                            <h3 class="mb-0 text-info fw-bold">{{ number_format($grossProfit, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">
                                Margem: {{ number_format($grossMargin, 1) }}%
                            </small>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card {{ $netProfit >= 0 ? 'success' : 'danger' }} h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Lucro Líquido</h6>
                            <h3 class="mb-0 fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($netProfit, 2, ',', '.') }} MT
                            </h3>
                            <small class="text-muted">
                                Margem: {{ number_format($netMargin, 1) }}%
                            </small>
                        </div>
                        <div class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                            <i class="fas fa-{{ $netProfit >= 0 ? 'trophy' : 'exclamation-triangle' }} fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Métricas Secundárias -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card bg-light h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Custo dos Produtos Vendidos</h6>
                    <h4 class="text-warning mb-0">{{ number_format($costOfGoodsSold, 2, ',', '.') }} MT</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card bg-light h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Despesas Operacionais</h6>
                    <h4 class="text-danger mb-0">{{ number_format($totalExpenses, 2, ',', '.') }} MT</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card bg-light h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Ticket Médio</h6>
                    <h4 class="text-primary mb-0">{{ number_format($averageTicket, 2, ',', '.') }} MT</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card bg-light h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Vendas/Dia (Média)</h6>
                    <h4 class="text-info mb-0">
                        {{ $totalSales > 0 ? number_format($totalSales / max(1, \Carbon\Carbon::parse($dateFrom)->diffInDays(\Carbon\Carbon::parse($dateTo)) + 1), 1) : 0 }}
                    </h4>
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
                        Evolução das Vendas e Lucro
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
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Receita</th>
                                    <th class="text-end">Lucro</th>
                                    <th class="text-center">Margem</th>
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
                                        <td class="text-end">
                                            <span
                                                class="fw-bold {{ $product->profit >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($product->profit, 2, ',', '.') }} MT
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-{{ $product->margin >= 20 ? 'success' : ($product->margin >= 10 ? 'warning' : 'danger') }}">
                                                {{ number_format($product->margin, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
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
                                    <th class="text-center">Itens</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSales as $sale)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                                        <td>{{ $sale->customer_name ?? 'N/A' }}</td>
                                        <td class="text-end text-success fw-bold">
                                            {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                                        </td>
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
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">{{ $sale->items->count() }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
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

    <!-- Tabelas Condicionais com Melhor Análise -->
    @if ($reportType === 'sales' || $reportType === 'all')
        <div class="card mt-4 fade-in">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 d-flex align-items-center justify-content-between">
                    <span>
                        <i class="fas fa-table me-2 text-success"></i>
                        Vendas Detalhadas com Análise de Margem
                    </span>
                    <small class="text-muted">{{ $sales->count() }} vendas</small>
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
                                <th class="text-end">Total</th>
                                <th class="text-end">Custo</th>
                                <th class="text-end">Lucro</th>
                                <th class="text-center">Margem</th>
                                <th>Pagamento</th>
                                <th>Vendedor</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td><strong class="text-primary">#{{ $sale->id }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                                    <td>{{ $sale->customer_name ?? 'N/A' }}</td>
                                    <td class="text-end text-success fw-bold">
                                        {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                                    </td>
                                    <td class="text-end text-warning">
                                        {{ number_format($sale->cost ?? 0, 2, ',', '.') }} MT
                                    </td>
                                    <td class="text-end">
                                        <span
                                            class="fw-bold {{ ($sale->profit ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($sale->profit ?? 0, 2, ',', '.') }} MT
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-{{ ($sale->margin ?? 0) >= 20 ? 'success' : (($sale->margin ?? 0) >= 10 ? 'warning' : 'danger') }}">
                                            {{ number_format($sale->margin ?? 0, 1) }}%
                                        </span>
                                    </td>
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
                                    <td>{{ $sale->user->name ?? 'N/A' }}</td>
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
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="fas fa-shopping-cart fa-2x mb-3 opacity-50"></i>
                                        <p>Nenhuma venda encontrada no período.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="3">TOTAIS:</td>
                                <td class="text-end text-success">
                                    {{ number_format($sales->sum('total_amount'), 2, ',', '.') }} MT</td>
                                <td class="text-end text-warning">{{ number_format($sales->sum('cost'), 2, ',', '.') }} MT
                                </td>
                                <td class="text-end {{ $sales->sum('profit') >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($sales->sum('profit'), 2, ',', '.') }} MT
                                </td>
                                <td class="text-center">
                                    @php
                                        $totalMargin =
                                            $sales->sum('total_amount') > 0
                                                ? ($sales->sum('profit') / $sales->sum('total_amount')) * 100
                                                : 0;
                                    @endphp
                                    <span
                                        class="badge bg-{{ $totalMargin >= 20 ? 'success' : ($totalMargin >= 10 ? 'warning' : 'danger') }}">
                                        {{ number_format($totalMargin, 1) }}%
                                    </span>
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if ($reportType === 'products' || $reportType === 'all')
        <div class="card mt-4 fade-in">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 d-flex align-items-center justify-content-between">
                    <span>
                        <i class="fas fa-boxes me-2 text-info"></i>
                        Relatório de Estoque e Performance
                    </span>
                    <small class="text-muted">{{ $products->count() }} produtos</small>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="products-table">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th>Categoria</th>
                                <th class="text-center">Estoque</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Vendido</th>
                                <th class="text-end">Custo</th>
                                <th class="text-end">Venda</th>
                                <th class="text-center">Markup</th>
                                <th class="text-end">Receita</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td><strong>{{ $product->name }}</strong></td>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        {{ $product->type === 'product' ? $product->stock_quantity : '-' }}
                                    </td>
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
                                            <span class="badge bg-info">Serviço</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-{{ $product->quantity_sold > 0 ? 'success' : 'light text-dark' }}">
                                            {{ $product->quantity_sold }}
                                        </span>
                                    </td>
                                    <td class="text-end">{{ number_format($product->purchase_price, 2, ',', '.') }} MT
                                    </td>
                                    <td class="text-end text-success fw-bold">
                                        {{ number_format($product->selling_price, 2, ',', '.') }} MT
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-{{ $product->markup >= 50 ? 'success' : ($product->markup >= 20 ? 'warning' : 'danger') }}">
                                            {{ number_format($product->markup, 1) }}%
                                        </span>
                                    </td>
                                    <td class="text-end text-primary fw-bold">
                                        {{ number_format($product->revenue_generated ?? 0, 2, ',', '.') }} MT
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
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

    @if ($reportType === 'expenses' || $reportType === 'all')
        <div class="card mt-4 fade-in">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 d-flex align-items-center justify-content-between">
                    <span>
                        <i class="fas fa-receipt me-2 text-danger"></i>
                        Despesas Operacionais
                    </span>
                    <small class="text-muted">{{ $expenses->count() }} despesas</small>
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
                                    <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $expense->category->name ?? 'Sem Categoria' }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($expense->description, 50) }}</td>
                                    <td class="text-end text-danger fw-bold">
                                        {{ number_format($expense->amount, 2, ',', '.') }} MT
                                    </td>
                                    <td>{{ $expense->receipt_number ?? 'N/A' }}</td>
                                    <td>{{ $expense->user->name ?? 'N/A' }}</td>
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
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="4">TOTAL DE DESPESAS:</td>
                                <td class="text-end text-danger">
                                    {{ number_format($expenses->sum('amount'), 2, ',', '.') }} MT</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
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
            // Gráfico de Vendas com Lucro
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: @json($salesChartLabels),
                    datasets: [{
                            label: 'Receita (MT)',
                            data: @json($salesChartData),
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            tension: 0.3,
                            fill: false,
                            yAxisID: 'y'
                        },
                        @if (isset($salesChartProfitData))
                            {
                                label: 'Lucro Bruto (MT)',
                                data: @json($salesChartProfitData),
                                borderColor: '#198754',
                                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                                tension: 0.3,
                                fill: false,
                                yAxisID: 'y'
                            }
                        @endif
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y
                                        .toLocaleString('pt-MZ') + ' MT';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            ticks: {
                                callback: value => value.toLocaleString('pt-MZ') + ' MT'
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
                            '#28a745', '#007bff', '#17a2b8', '#ffc107', '#6f42c1'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed * 100) / total).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' (' + percentage +
                                        '%)';
                                }
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

        .stats-card.primary {
            border-left-color: #1e3a8a;
        }

        .stats-card.success {
            border-left-color: #059669;
        }

        .stats-card.info {
            border-left-color: #0891b2;
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

        .badge {
            font-size: 0.75em;
        }

        /* Cores para margens */
        .badge.bg-success {
            background-color: #198754 !important;
        }

        .badge.bg-warning {
            background-color: #f0ad4e !important;
        }

        .badge.bg-danger {
            background-color: #dc3545 !important;
        }
    </style>
@endpush
