@extends('layouts.app')

@section('title', 'Dashboard')
@section('title-icon', 'fa-tachometer-alt')
@section('page-title', 'Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <!-- Cabeçalho de Boas Vindas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient text-white" style="background: var(--primary-gradient);">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">Bem-vindo ao PrintCenter+</h2>
                            <p class="mb-0 opacity-90">
                                Hoje é {{ now()->format('d/m/Y') }} - Tenha um excelente dia de trabalho!
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <i class="fas fa-print fa-4x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="stats-card primary fade-in">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 50px; height: 50px;">
                            <i class="fas fa-dollar-sign fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-primary fs-4">MZN {{ number_format($todaySales, 2, ',', '.') }}</div>
                        <div class="text-muted">Vendas Hoje</div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('sales.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>Ver Vendas
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="stats-card warning fade-in">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 50px; height: 50px;">
                            <i class="fas fa-receipt fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-warning fs-4">MZN {{ number_format($todayExpenses, 2, ',', '.') }}</div>
                        <div class="text-muted">Despesas Hoje</div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('expenses.index') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>Ver Despesas
                    </a>
                </div>
            </div>
        </div>

        @can('admin')
        <div class="col-md-3 col-6">
            <div class="stats-card success fade-in">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 50px; height: 50px;">
                            <i class="fas fa-chart-line fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-success fs-4">MZN {{ number_format($monthSales, 2, ',', '.') }}</div>
                        <div class="text-muted">Vendas do Mês</div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('reports.index') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>Relatórios
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="stats-card info fade-in">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 50px; height: 50px;">
                            <i class="fas fa-coins fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-info fs-4">MZN {{ number_format($monthSales - $monthExpenses, 2, ',', '.') }}</div>
                        <div class="text-muted">Lucro do Mês</div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('reports.profit-loss') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>Ver P&L
                    </a>
                </div>
            </div>
        </div>
        @endcan
    </div>

    <div class="row g-4">
        <!-- Alerta de Estoque Baixo -->
        @if ($lowStockProducts->count() > 0)
        <div class="col-md-6">
            <div class="card h-100 fade-in">
                <div class="card-header bg-warning text-dark d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <h5 class="card-title mb-0 fw-bold">Produtos com Estoque Baixo</h5>
                    <span class="badge bg-danger ms-auto">{{ $lowStockProducts->count() }}</span>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Produtos que precisam de reposição urgente
                    </div>
                    
                    @foreach ($lowStockProducts->take(5) as $product)
                    <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                        <div>
                            <div class="fw-semibold">{{ $product->name }}</div>
                            <small class="text-muted">{{ $product->category->name ?? 'Sem categoria' }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-danger">{{ $product->stock_quantity }} {{ $product->unit }}</span>
                        </div>
                    </div>
                    @endforeach

                    @if ($lowStockProducts->count() > 5)
                    <div class="text-center mt-3">
                        <small class="text-muted">E mais {{ $lowStockProducts->count() - 5 }} produtos...</small>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('products.index') }}" class="btn btn-warning w-100">
                        <i class="fas fa-boxes me-2"></i>Gerenciar Estoque
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Vendas Recentes -->
        <div class="col-md-{{ $lowStockProducts->count() > 0 ? '6' : '12' }}">
            <div class="card h-100 fade-in">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="fas fa-shopping-cart me-2"></i>
                    <h5 class="card-title mb-0 fw-bold">Vendas Recentes</h5>
                    @can('admin')
                        <span class="badge bg-success ms-auto">Todas</span>
                    @else
                        <span class="badge bg-info ms-auto">Minhas</span>
                    @endcan
                </div>
                <div class="card-body">
                    @if ($recentSales->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    @can('admin')
                                    <th>Funcionário</th>
                                    @endcan
                                    <th width="60">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentSales as $sale)
                                @can('admin')
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $sale->sale_date->format('d/m') }}</span>
                                    </td>
                                    <td>{{ Str::limit($sale->customer_name ?: 'Balcão', 15) }}</td>
                                    <td>
                                        <span class="fw-bold text-success">MZN {{ number_format($sale->total_amount, 2, ',', '.') }}</span>
                                    </td>
                                    <td>{{ explode(' ', $sale->user->name)[0] }}</td>
                                    <td>
                                        <a href="{{ route('sales.show', $sale) }}" 
                                           class="btn btn-outline-primary btn-sm" 
                                           data-bs-toggle="tooltip" 
                                           title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @else
                                @if ($sale->user_id == Auth::id())
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $sale->sale_date->format('d/m') }}</span>
                                    </td>
                                    <td>{{ Str::limit($sale->customer_name ?: 'Balcão', 15) }}</td>
                                    <td>
                                        <span class="fw-bold text-success">MZN {{ number_format($sale->total_amount, 2, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('sales.show', $sale) }}" 
                                           class="btn btn-outline-primary btn-sm" 
                                           data-bs-toggle="tooltip" 
                                           title="Ver detalhes">
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
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhuma venda registrada hoje</p>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('sales.index') }}" class="btn btn-primary w-100">
                        <i class="fas fa-list me-2"></i>Ver Todas as Vendas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card fade-in">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-bolt me-2 text-warning"></i>Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Nova Venda -->
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('sales.create') }}" 
                               class="btn btn-success w-100 py-3 text-decoration-none">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-cash-register fa-2x mb-2"></i>
                                    <span class="fw-semibold">Nova Venda</span>
                                    <small class="text-success-emphasis">POS</small>
                                </div>
                            </a>
                        </div>

                        <!-- Adicionar Produto -->
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('products.create') }}" 
                               class="btn btn-info w-100 py-3 text-decoration-none">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                    <span class="fw-semibold">Novo Produto</span>
                                    <small class="text-info-emphasis">Cadastro</small>
                                </div>
                            </a>
                        </div>

                        <!-- Despesas -->
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('expenses.create') }}" 
                               class="btn btn-warning w-100 py-3 text-decoration-none">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-receipt fa-2x mb-2"></i>
                                    <span class="fw-semibold">Nova Despesa</span>
                                    <small class="text-warning-emphasis">Financeiro</small>
                                </div>
                            </a>
                        </div>

                        @can('admin')
                        <!-- Relatórios -->
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('reports.index') }}" 
                               class="btn btn-primary w-100 py-3 text-decoration-none">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                    <span class="fw-semibold">Relatórios</span>
                                    <small class="text-primary-emphasis">Análise</small>
                                </div>
                            </a>
                        </div>

                        <!-- Estoque -->
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('stock-movements.index') }}" 
                               class="btn btn-secondary w-100 py-3 text-decoration-none">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-warehouse fa-2x mb-2"></i>
                                    <span class="fw-semibold">Estoque</span>
                                    <small class="text-secondary-emphasis">Controle</small>
                                </div>
                            </a>
                        </div>

                        <!-- Usuários -->
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('users.index') }}" 
                               class="btn btn-dark w-100 py-3 text-decoration-none">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <span class="fw-semibold">Usuários</span>
                                    <small class="text-dark-emphasis">Sistema</small>
                                </div>
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status do Sistema -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 bg-light fade-in">
                <div class="card-body py-2">
                    <div class="row align-items-center text-center text-md-start">
                        <div class="col-md-3">
                            <small class="text-muted">
                                <i class="fas fa-server me-1"></i>Sistema Online
                            </small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">
                                <i class="fas fa-database me-1"></i>Backup Automático
                            </small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>Conexão Segura
                            </small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>Última atualização: {{ now()->format('H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .bg-gradient {
        background: var(--primary-gradient) !important;
    }
    
    .stats-card {
        transition: all 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-out;
    }
    
    .btn:hover {
        transform: translateY(-2px);
    }
    
    .card-header {
        font-weight: 600;
    }
    
    .table td {
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Atualizar contadores em tempo real (simulação)
        function updateCounters() {
            // Aqui você pode fazer requisições AJAX para atualizar os dados
            console.log('Atualizando contadores...');
        }
        
        // Atualizar a cada 5 minutos
        setInterval(updateCounters, 300000);
        
        // Mostrar toast de boas vindas
        setTimeout(() => {
            showToast('Sistema PrintCenter+ carregado com sucesso!', 'success');
        }, 1000);
    });
</script>
@endpush