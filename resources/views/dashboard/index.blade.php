{{-- filepath: resources/views/dashboard/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="fw-bold text-primary"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h1>
        <span class="badge bg-gradient-primary fs-6 px-3 py-2 shadow-sm">Bem-vindo, {{ Auth::user()->name }}</span>
    </div>
@stop

@section('content')
    <div class="row g-4 mb-4">
        <!-- Cards de Resumo -->
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white h-100 dashboard-card">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h3 class="fw-bold mb-1">MZN{{ number_format($todaySales, 2) }}</h3>
                        <p class="mb-0">Vendas de Hoje</p>
                    </div>
                    <div class="mt-3 d-flex align-items-center">
                        <i class="fas fa-dollar-sign fa-2x me-2"></i>
                        <a href="{{ route('sales.index') }}" class="text-white text-decoration-underline ms-auto">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm bg-gradient-danger text-white h-100 dashboard-card">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h3 class="fw-bold mb-1">MZN{{ number_format($todayExpenses, 2) }}</h3>
                        <p class="mb-0">Despesas de Hoje</p>
                    </div>
                    <div class="mt-3 d-flex align-items-center">
                        <i class="fas fa-receipt fa-2x me-2"></i>
                        <a href="{{ route('expenses.index') }}" class="text-white text-decoration-underline ms-auto">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        @can('admin')
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm bg-gradient-success text-white h-100 dashboard-card">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h3 class="fw-bold mb-1">MZN{{ number_format($monthSales, 2) }}</h3>
                            <p class="mb-0">Vendas do Mês</p>
                        </div>
                        <div class="mt-3 d-flex align-items-center">
                            <i class="fas fa-chart-line fa-2x me-2"></i>
                            <a href="{{ route('sales.index') }}" class="text-white text-decoration-underline ms-auto">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm bg-gradient-info text-white h-100 dashboard-card">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h3 class="fw-bold mb-1">MZN{{ number_format($monthSales - $monthExpenses, 2) }}</h3>
                            <p class="mb-0">Lucro Líquido</p>
                        </div>
                        <div class="mt-3 d-flex align-items-center">
                            <i class="fas fa-coins fa-2x me-2"></i>
                            <a href="{{ route('reports.profit-loss') }}" class="text-white text-decoration-underline ms-auto">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    <div class="row g-4">
        <!-- Alerta de Estoque Baixo -->
        @if ($lowStockProducts->count() > 0)
            <div class="col-md-6">
                <div class="card border-warning shadow-sm h-100">
                    <div class="card-header bg-gradient-warning text-dark d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <h5 class="card-title mb-0 fw-bold">Alerta de Estoque Baixo</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($lowStockProducts as $product)
                            <div class="alert alert-warning d-flex align-items-center py-2 mb-2">
                                <strong class="me-2">{{ $product->name }}</strong>
                                <span class="me-2">Apenas {{ $product->stock_quantity }} {{ $product->unit }} restantes</span>
                                <span class="badge bg-danger ms-auto">Estoque Baixo</span>
                            </div>
                        @endforeach
                        <a href="{{ route('products.index') }}" class="btn btn-warning btn-sm mt-2">
                            Gerenciar Estoque
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Vendas Recentes -->
        <div class="col-md-{{ $lowStockProducts->count() > 0 ? '6' : '12' }}">
            <div class="card border-primary shadow-sm h-100">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Vendas Recentes
                        @can('admin')
                            <span class="badge bg-success ms-2">Todas</span>
                        @else
                            <span class="badge bg-info ms-2">Minhas</span>
                        @endcan
                    </h5>
                </div>
                <div class="card-body">
                    @if ($recentSales->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Data</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Funcionário</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentSales as $sale)
                                        @can('admin')
                                            <tr>
                                                <td><span class="badge bg-gradient-info">{{ $sale->sale_date->format('d/m/Y') }}</span></td>
                                                <td>{{ $sale->customer_name ?: 'Balcão' }}</td>
                                                <td><span class="fw-bold text-success">MZN{{ number_format($sale->total_amount, 2) }}</span></td>
                                                <td>{{ $sale->user->name }}</td>
                                                <td>
                                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-primary btn-sm rounded-circle" title="Ver detalhes">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @else
                                            @if ($sale->user_id == Auth::id())
                                                <tr>
                                                    <td><span class="badge bg-gradient-info">{{ $sale->sale_date->format('d/m/Y') }}</span></td>
                                                    <td>{{ $sale->customer_name ?: 'Balcão' }}</td>
                                                    <td><span class="fw-bold text-success">MZN{{ number_format($sale->total_amount, 2) }}</span></td>
                                                    <td>{{ $sale->user->name }}</td>
                                                    <td>
                                                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-primary btn-sm rounded-circle" title="Ver detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endcan
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Nenhuma venda recente encontrada.</p>
                    @endif
                    <a href="{{ route('sales.index') }}" class="btn btn-primary btn-sm mt-2">
                        Ver Todas as Vendas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-light">
                    <h5 class="card-title mb-0 fw-bold"><i class="fas fa-bolt me-2 text-warning"></i>Ações Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 justify-content-center">
                        <div class="col-md-2 col-6">
                            <a href="{{ route('sales.create') }}" class="btn btn-success w-100 py-3 shadow-sm">
                                <i class="fas fa-plus-circle fa-lg d-block mb-1"></i>
                                Nova Venda
                            </a>
                        </div>
                        <div class="col-md-2 col-6">
                            <a href="{{ route('expenses.create') }}" class="btn btn-warning w-100 py-3 shadow-sm">
                                <i class="fas fa-receipt fa-lg d-block mb-1"></i>
                                Adicionar Despesa
                            </a>
                        </div>
                        @can('admin')
                        <div class="col-md-2 col-6">
                            <a href="{{ route('products.index', ['create' => 1]) }}" class="btn btn-info w-100 py-3 shadow-sm">
                                <i class="fas fa-box fa-lg d-block mb-1"></i>
                                Adicionar Produto
                            </a>
                        </div>
                        <div class="col-md-2 col-6">
                            <a href="{{ route('reports.daily-sales') }}" class="btn btn-primary w-100 py-3 shadow-sm">
                                <i class="fas fa-chart-bar fa-lg d-block mb-1"></i>
                                Relatório Diário
                            </a>
                        </div>
                        <div class="col-md-2 col-6">
                            <a href="{{ route('reports.inventory') }}" class="btn btn-secondary w-100 py-3 shadow-sm">
                                <i class="fas fa-warehouse fa-lg d-block mb-1"></i>
                                Inventário
                            </a>
                        </div>
                        <div class="col-md-2 col-6">
                            <a href="{{ route('reports.profit-loss') }}" class="btn btn-dark w-100 py-3 shadow-sm">
                                <i class="fas fa-calculator fa-lg d-block mb-1"></i>
                                Relatório de P&L
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .bg-gradient-primary {
            background: linear-gradient(90deg, #007bff 70%, #0056b3 100%) !important;
        }
        .bg-gradient-danger {
            background: linear-gradient(90deg, #dc3545 70%, #a71d2a 100%) !important;
        }
        .bg-gradient-success {
            background: linear-gradient(90deg, #28a745 70%, #19692c 100%) !important;
        }
        .bg-gradient-info {
            background: linear-gradient(90deg, #17a2b8 70%, #117a8b 100%) !important;
        }
        .bg-gradient-warning {
            background: linear-gradient(90deg, #ffc107 70%, #e0a800 100%) !important;
        }
        .bg-gradient-light {
            background: linear-gradient(90deg, #f8f9fa 70%, #e9ecef 100%) !important;
        }
        .card {
            border-radius: 1rem;
        }
        .dashboard-card {
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .dashboard-card:hover {
            box-shadow: 0 1rem 2rem rgba(0,0,0,.15)!important;
            transform: translateY(-4px) scale(1.02);
        }
        .shadow-sm {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .05) !important;
        }
        .fw-bold {
            font-weight: 700 !important;
        }
        .table th,
        .table td {
            vertical-align: middle !important;
        }
        .btn {
            font-weight: 500;
        }
        .badge {
            font-size: 0.95em;
        }
        .bg-gradient-info, .bg-gradient-success, .bg-gradient-danger, .bg-gradient-primary, .bg-gradient-warning, .bg-gradient-light {
            color: #fff !important;
        }
        .bg-gradient-light {
            color: #333 !important;
        }
    </style>
@stop

@section('js')
    <script>
        // Destaque nos cards ao passar o mouse
        document.querySelectorAll('.dashboard-card').forEach(card => {
            card.addEventListener('mouseenter', () => card.classList.add('shadow-lg'));
            card.addEventListener('mouseleave', () => card.classList.remove('shadow-lg'));
        });
    </script>
@stop