@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    @php
        $titleIcon = 'fas fa-tachometer-alt';
    @endphp
    <!-- Cabeçalho de Boas Vindas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(45deg, #5B9BD5, #4A90E2); color: white;">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-1" style="font-weight: 600;">
                                <span id="greeting-icon" class="me-2" style="animation: bounce 2s ease-in-out infinite;">
                                    <i class="fas fa-spinner fa-spin"></i> <!-- Ícone de carregamento -->
                                </span>
                                <span id="greeting-text">Carregando</span>
                                {{ auth()->user()->name }}!
                            </h2>
                            <p class="mb-0" style="opacity: 0.95; font-weight: 500;">
                                Hoje é <strong>{{ now()->format('d/m/Y') }}</strong> —
                                <span id="day-message">...</span>
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <i class="fas fa-print fa-4x" style="opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas (Alinhados ao layout) -->
    <div class="dashboard-stats mb-4">
        <!-- Vendas Hoje -->
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">MZN {{ number_format($todaySales, 2, ',', '.') }}</div>
                <div class="stat-label">Vendas Hoje</div>
            </div>
            <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                <i class="fas fa-arrow-right me-1"></i>Ver Vendas
            </a>
        </div>

        <!-- Despesas Hoje -->
        <div class="stat-card warning">
            <div class="stat-icon warning">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">MZN {{ number_format($todayExpenses, 2, ',', '.') }}</div>
                <div class="stat-label">Despesas Hoje</div>
            </div>
            <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-warning mt-2">
                <i class="fas fa-arrow-right me-1"></i>Ver Despesas
            </a>
        </div>

        @can('admin')
            <!-- Vendas do Mês -->
            <div class="stat-card success">
                <div class="stat-icon success">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">MZN {{ number_format($monthSales, 2, ',', '.') }}</div>
                    <div class="stat-label">Vendas do Mês</div>
                </div>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-success mt-2">
                    <i class="fas fa-arrow-right me-1"></i>Relatórios
                </a>
            </div>

            <!-- Lucro do Mês -->
            <div class="stat-card info">
                <div class="stat-icon info">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">MZN {{ number_format($monthSales - $monthExpenses, 2, ',', '.') }}</div>
                    <div class="stat-label">Lucro do Mês</div>
                </div>
                <a href="{{ route('reports.profit-loss') }}" class="btn btn-sm btn-outline-info mt-2">
                    <i class="fas fa-arrow-right me-1"></i>Ver P&L
                </a>
            </div>
        @endcan
    </div>

    <!-- Alerta de Estoque Baixo + Vendas Recentes -->
    <div class="row g-4">
        <!-- Estoque Baixo -->
        @if ($lowStockProducts->count() > 0)
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-warning text-dark d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Produtos com Estoque Baixo</strong>
                        <span class="badge bg-danger ms-auto">{{ $lowStockProducts->count() }}</span>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Produtos que precisam de reposição urgente
                        </div>
                        @foreach ($lowStockProducts->take(5) as $product)
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <div>
                                    <div class="fw-semibold">{{ $product->name }}</div>
                                    <small class="text-muted">{{ $product->category?->name ?? 'Sem categoria' }}</small>
                                </div>
                                <span class="badge bg-danger">{{ $product->stock_quantity }} {{ $product->unit }}</span>
                            </div>
                        @endforeach
                        @if ($lowStockProducts->count() > 5)
                            <div class="text-center mt-3">
                                <small class="text-muted">E mais {{ $lowStockProducts->count() - 5 }} produtos...</small>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('products.index') }}" class="btn btn-warning w-100">
                            <i class="fas fa-boxes me-2"></i>Gerenciar Estoque
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Vendas Recentes -->
        <div class="col-lg-{{ $lowStockProducts->count() > 0 ? '6' : '12' }}">
            <div class="card h-100">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="fas fa-shopping-cart me-2"></i>
                    <strong>Vendas Recentes</strong>
                    @can('admin')
                        <span class="badge bg-success ms-auto">Todas</span>
                    @else
                        <span class="badge bg-info ms-auto">Minhas</span>
                    @endcan
                </div>
                <div class="card-body">
                    @if ($recentSales->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-shopping-cart fa-3x mb-3 opacity-50"></i>
                            <p>Nenhuma venda registrada hoje</p>
                        </div>
                    @else
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        @can('admin')
                                            <th>Funcionário</th>
                                        @endcan
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentSales as $sale)
                                        @can('admin')
                                            <tr>
                                                <td><span
                                                        class="badge bg-light text-dark">{{ $sale->sale_date->format('d/m') }}</span>
                                                </td>
                                                <td>{{ Str::limit($sale->customer_name ?: 'Balcão', 15) }}</td>
                                                <td><span class="text-success fw-bold">MZN
                                                        {{ number_format($sale->total_amount, 2, ',', '.') }}</span></td>
                                                <td>{{ explode(' ', $sale->user->name)[0] }}</td>
                                                <td>
                                                    <a href="{{ route('sales.show', $sale) }}"
                                                        class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip"
                                                        title="Ver detalhes">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @else
                                            @if ($sale->user_id == auth()->id())
                                                <tr>
                                                    <td><span
                                                            class="badge bg-light text-dark">{{ $sale->sale_date->format('d/m') }}</span>
                                                    </td>
                                                    <td>{{ Str::limit($sale->customer_name ?: 'Balcão', 15) }}</td>
                                                    <td><span class="text-success fw-bold">MZN
                                                            {{ number_format($sale->total_amount, 2, ',', '.') }}</span></td>
                                                    <td>
                                                        <a href="{{ route('sales.show', $sale) }}"
                                                            class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip"
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
                    @endif
                </div>
                <div class="card-footer">
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
            <div class="card">
                <div class="card-header bg-dark text-white d-flex align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('sales.create') }}"
                                class="btn btn-success w-100 py-3 d-block text-decoration-none">
                                <i class="fas fa-cash-register fa-2x d-block mb-2"></i>
                                <strong>Nova Venda</strong>
                                <small>PDV</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('products.create') }}"
                                class="btn btn-info w-100 py-3 d-block text-decoration-none">
                                <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                                <strong>Novo Produto</strong>
                                <small>Cadastro</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('expenses.create') }}"
                                class="btn btn-warning w-100 py-3 d-block text-decoration-none">
                                <i class="fas fa-receipt fa-2x d-block mb-2"></i>
                                <strong>Nova Despesa</strong>
                                <small>Financeiro</small>
                            </a>
                        </div>
                        @can('admin')
                            <div class="col-lg-2 col-md-4 col-6">
                                <a href="{{ route('reports.index') }}"
                                    class="btn btn-primary w-100 py-3 d-block text-decoration-none">
                                    <i class="fas fa-chart-bar fa-2x d-block mb-2"></i>
                                    <strong>Relatórios</strong>
                                    <small>Análise</small>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6">
                                <a href="{{ route('stock-movements.index') }}"
                                    class="btn btn-secondary w-100 py-3 d-block text-decoration-none">
                                    <i class="fas fa-warehouse fa-2x d-block mb-2"></i>
                                    <strong>Estoque</strong>
                                    <small>Controle</small>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6">
                                <a href="{{ route('users.index') }}"
                                    class="btn btn-dark w-100 py-3 d-block text-decoration-none">
                                    <i class="fas fa-users fa-2x d-block mb-2"></i>
                                    <strong>Usuários</strong>
                                    <small>Sistema</small>
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
            <div class="card border-0" style="background: var(--content-bg);">
                <div class="card-body py-2">
                    <div class="row text-center text-md-start align-items-center">
                        <div class="col-md-3"><small class="text-muted"><i class="fas fa-server me-1"></i> Sistema
                                Online</small></div>
                        <div class="col-md-3"><small class="text-muted"><i class="fas fa-database me-1"></i> Backup
                                Automático</small></div>
                        <div class="col-md-3"><small class="text-muted"><i class="fas fa-shield-alt me-1"></i> Conexão
                                Segura</small></div>
                        <div class="col-md-3"><small class="text-muted"><i class="fas fa-clock me-1"></i> Atualização:
                                {{ now()->format('H:i') }}</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userTime = new Date().getHours();
            const userName = "{{ auth()->user()->name }}";
            const iconElement = document.getElementById('greeting-icon');
            const greetingElement = document.getElementById('greeting-text');
            const dayMessageElement = document.getElementById('day-message');

            let greeting, iconClass, dayMessage;

            if (userTime < 12) {
                greeting = 'Bom dia,';
                iconClass = 'fas fa-sun';
                dayMessage = 'Pronto para um ótimo início de dia?';
            } else if (userTime < 18) {
                greeting = 'Boa tarde,';
                iconClass = 'fas fa-cloud-sun';
                dayMessage = 'Ótimo rendimento até agora!';
            } else {
                greeting = 'Boa noite,';
                iconClass = 'fas fa-moon';
                dayMessage = 'Trabalhando com foco até o fim do dia!';
            }

            // Atualiza o ícone
            iconElement.innerHTML = `<i class="${iconClass}"></i>`;
            greetingElement.textContent = greeting;
            dayMessageElement.textContent = dayMessage;

            // Toast de boas-vindas
            setTimeout(() => {
                FDSMULTSERVICES.Toast.show(
                    `<i class="${iconClass} me-1"></i> ${greeting} ${userName}! Bem-vindo(a) de volta.`,
                    'success'
                );
            }, 1500);
        });
    </script>

    <style>
        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        #greeting-icon i {
            color: var(--print-blue);
            font-size: 1.2em;
            transition: transform 0.3s ease;
        }

        #greeting-icon:hover i {
            transform: rotate(10deg) scale(1.1);
        }
    </style>
@endpush
