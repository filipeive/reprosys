@extends('layouts.app')

@section('title', 'Dashboard Principal')
@section('page-title', 'Dashboard Executivo')

@push('styles')
<style>
    /* ===== DASHBOARD ESPECÍFICO ===== */
    .dashboard-welcome {
        background: linear-gradient(135deg, var(--primary-blue) 0%, #4A90E2 100%);
        color: white;
        border-radius: var(--border-radius-lg);
        padding: 25px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    .dashboard-welcome::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(10deg); }
    }

    .metric-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 25px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
        height: 140px;
    }

    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-blue);
        transition: all 0.3s ease;
    }

    .metric-card.success::before { background: var(--success-green); }
    .metric-card.warning::before { background: var(--warning-orange); }
    .metric-card.danger::before { background: var(--danger-red); }
    .metric-card.info::before { background: var(--info-blue); }

    .metric-icon {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: var(--border-radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
        opacity: 0.9;
    }

    .metric-icon.primary { background: linear-gradient(45deg, var(--primary-blue), #4A90E2); }
    .metric-icon.success { background: linear-gradient(45deg, var(--success-green), #22C55E); }
    .metric-icon.warning { background: linear-gradient(45deg, var(--warning-orange), #F59E0B); }
    .metric-icon.danger { background: linear-gradient(45deg, var(--danger-red), #EF4444); }

    .metric-value {
        font-size: 32px;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 8px;
        line-height: 1;
    }

    .metric-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-change {
        font-size: 12px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .metric-change.positive {
        background: rgba(34, 197, 94, 0.15);
        color: var(--success-green);
    }

    .metric-change.negative {
        background: rgba(239, 68, 68, 0.15);
        color: var(--danger-red);
    }

    .metric-change.neutral {
        background: rgba(107, 114, 128, 0.15);
        color: var(--text-secondary);
    }

    .chart-container {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: var(--shadow-sm);
        position: relative;
        height: 400px;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
    }

    .chart-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chart-period {
        font-size: 12px;
        padding: 6px 12px;
        background: var(--content-bg);
        border-radius: var(--border-radius);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }

    .activity-feed {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 0;
        box-shadow: var(--shadow-sm);
        max-height: 500px;
        overflow-y: auto;
    }

    .activity-header {
        padding: 20px 25px;
        border-bottom: 1px solid var(--border-color);
        background: var(--content-bg);
        font-weight: 700;
        font-size: 16px;
        color: var(--text-primary);
    }

    .activity-item {
        padding: 15px 25px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        transition: background-color 0.2s ease;
    }

    .activity-item:hover {
        background: var(--content-bg);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 16px;
        color: white;
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
        min-width: 0;
    }

    .activity-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 4px;
        line-height: 1.4;
    }

    .activity-meta {
        font-size: 12px;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .activity-value {
        font-weight: 700;
        color: var(--success-green);
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .quick-action {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 20px;
        text-decoration: none;
        color: var(--text-primary);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: var(--shadow-sm);
    }

    .quick-action:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow);
        color: var(--text-primary);
        border-color: var(--primary-blue);
    }

    .quick-action-icon {
        width: 50px;
        height: 50px;
        border-radius: var(--border-radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }

    .quick-action-content h4 {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .quick-action-content p {
        font-size: 12px;
        color: var(--text-secondary);
        margin: 0;
    }

    .alerts-section {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: var(--shadow-sm);
    }

    .alert-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .alert-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .alert-icon {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 14px;
        color: white;
    }

    .pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }

    .stats-comparison {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 25px;
        box-shadow: var(--shadow-sm);
    }

    .comparison-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .comparison-item:last-child {
        border-bottom: none;
    }

    .comparison-label {
        font-size: 14px;
        font-weight: 500;
        color: var(--text-secondary);
    }

    .comparison-values {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .comparison-current {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .comparison-previous {
        font-size: 12px;
        color: var(--text-muted);
    }

    /* ===== RESPONSIVE ADJUSTMENTS ===== */
    @media (max-width: 1199.98px) {
        .metric-card {
            padding: 20px;
            height: 130px;
        }
        
        .metric-value {
            font-size: 28px;
        }
    }

    @media (max-width: 767.98px) {
        .dashboard-welcome {
            padding: 20px;
        }
        
        .metric-card {
            padding: 15px;
            height: 120px;
        }
        
        .metric-value {
            font-size: 24px;
        }
        
        .metric-icon {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }
        
        .chart-container {
            height: 300px;
            padding: 15px;
        }
        
        .quick-actions {
            grid-template-columns: 1fr;
        }
    }

    /* ===== LOADING STATES ===== */
    .metric-loading {
        background: linear-gradient(90deg, var(--border-color) 25%, rgba(255,255,255,0.3) 50%, var(--border-color) 75%);
        background-size: 200% 100%;
        animation: loading 2s infinite;
        color: transparent !important;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>
@endpush

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard Executivo</li>
@endsection

@section('content')
<!-- Welcome Section -->
<div class="dashboard-welcome">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="mb-2">
                <i class="fas fa-sun me-2"></i>
                Bom dia, {{ explode(' ', auth()->user()->name)[0] }}!
            </h2>
            <p class="mb-0 opacity-90">
                Bem-vindo ao painel de controle do FDSMULTSERVICES+. 
                Aqui você tem uma visão completa do seu negócio em tempo real.
            </p>
        </div>
        <div class="col-md-4 text-end">
            <div class="d-flex flex-column text-end">
                <small class="opacity-75">{{ now()->format('l, j \d\e F \d\e Y') }}</small>
                <h4 class="mb-0" id="current-time">{{ now()->format('H:i') }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    @if(userCan('create_sales'))
    <a href="{{ route('sales.create') }}" class="quick-action">
        <div class="quick-action-icon" style="background: linear-gradient(45deg, var(--success-green), #22C55E);">
            <i class="fas fa-cash-register"></i>
        </div>
        <div class="quick-action-content">
            <h4>Nova Venda</h4>
            <p>Registrar venda rápida</p>
        </div>
    </a>
    @endif
    
    @if(userCan('create_products'))
    <a href="{{ route('products.create') }}" class="quick-action">
        <div class="quick-action-icon" style="background: linear-gradient(45deg, var(--primary-blue), #4A90E2);">
            <i class="fas fa-cube"></i>
        </div>
        <div class="quick-action-content">
            <h4>Novo Produto</h4>
            <p>Cadastrar produto</p>
        </div>
    </a>
    @endif
    
    @if(userCan('view_reports'))
    <a href="{{ route('reports.index') }}" class="quick-action">
        <div class="quick-action-icon" style="background: linear-gradient(45deg, var(--info-blue), #0EA5E9);">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="quick-action-content">
            <h4>Relatórios</h4>
            <p>Análises detalhadas</p>
        </div>
    </a>
    @endif
    
    @if(userCan('view_expenses'))
    <a href="{{ route('expenses.create') }}" class="quick-action">
        <div class="quick-action-icon" style="background: linear-gradient(45deg, var(--warning-orange), #F59E0B);">
            <i class="fas fa-receipt"></i>
        </div>
        <div class="quick-action-content">
            <h4>Nova Despesa</h4>
            <p>Registrar despesa</p>
        </div>
    </a>
    @endif
</div>

<!-- Key Metrics -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="metric-card success">
            <div class="metric-icon success">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="metric-value" id="today-sales">{{ number_format($todaySales, 2, ',', '.') }}</div>
            <div class="metric-label">Vendas de Hoje</div>
            <div class="metric-change positive">
                <i class="fas fa-arrow-up"></i>
                <span>+12% vs ontem</span>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="metric-card warning">
            <div class="metric-icon warning">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="metric-value" id="today-expenses">{{ number_format($todayExpenses, 2, ',', '.') }}</div>
            <div class="metric-label">Despesas de Hoje</div>
            <div class="metric-change negative">
                <i class="fas fa-arrow-down"></i>
                <span>-5% vs ontem</span>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="metric-card primary">
            <div class="metric-icon primary">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="metric-value">{{ number_format($monthSales, 2, ',', '.') }}</div>
            <div class="metric-label">Vendas do Mês</div>
            <div class="metric-change positive">
                <i class="fas fa-arrow-up"></i>
                <span>+24% vs mês anterior</span>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="metric-card {{ $lowStockProducts->count() > 0 ? 'danger' : 'success' }}">
            <div class="metric-icon {{ $lowStockProducts->count() > 0 ? 'danger' : 'success' }}">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="metric-value" id="low-stock-count">{{ $lowStockProducts->count() }}</div>
            <div class="metric-label">Produtos Baixo Estoque</div>
            @if($lowStockProducts->count() > 0)
                <div class="metric-change negative pulse">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Atenção requerida</span>
                </div>
            @else
                <div class="metric-change positive">
                    <i class="fas fa-check"></i>
                    <span>Tudo em ordem</span>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Alerts Section -->
@if($lowStockProducts->count() > 0 || session('dashboard_alert'))
<div class="alerts-section">
    <h5 class="mb-3">
        <i class="fas fa-bell text-warning me-2"></i>
        Alertas e Notificações
    </h5>
    
    @if(session('dashboard_alert'))
        <div class="alert-item">
            <div class="alert-icon" style="background: 
                @if(session('dashboard_alert')['type'] === 'success') var(--success-green)
                @elseif(session('dashboard_alert')['type'] === 'warning') var(--warning-orange)
                @elseif(session('dashboard_alert')['type'] === 'error') var(--danger-red)
                @else var(--info-blue) @endif">
                <i class="fas fa-
                    @if(session('dashboard_alert')['type'] === 'success') check
                    @elseif(session('dashboard_alert')['type'] === 'warning') exclamation-triangle
                    @elseif(session('dashboard_alert')['type'] === 'error') exclamation-circle
                    @else info @endif"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-semibold">{{ session('dashboard_alert')['message'] }}</div>
                <small class="text-muted">Agora mesmo</small>
            </div>
        </div>
    @endif
    
    @if($lowStockProducts->count() > 0)
        <div class="alert-item">
            <div class="alert-icon pulse" style="background: var(--danger-red);">
                <i class="fas fa-box-open"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-semibold">Estoque Baixo Detectado</div>
                <small class="text-muted">
                    {{ $lowStockProducts->count() }} produto(s) precisam de reposição:
                    {{ $lowStockProducts->take(3)->pluck('name')->join(', ') }}
                    @if($lowStockProducts->count() > 3) e outros... @endif
                </small>
            </div>
            <a href="{{ route('products.index', ['filter' => 'low_stock']) }}" class="btn btn-sm btn-outline-danger">
                Ver Produtos
            </a>
        </div>
    @endif
</div>
@endif

<div class="row g-4">
    <!-- Sales Chart -->
    <div class="col-xl-8">
        <div class="chart-container">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-chart-area text-primary"></i>
                    Vendas vs Despesas (Últimos 7 dias)
                </div>
                <span class="chart-period">Período: {{ now()->subDays(7)->format('d/m') }} - {{ now()->format('d/m/Y') }}</span>
            </div>
            <canvas id="salesChart" style="max-height: 320px;"></canvas>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="col-xl-4">
        <div class="activity-feed">
            <div class="activity-header">
                <i class="fas fa-clock me-2"></i>
                Atividades Recentes
            </div>
            
            @forelse($recentSales->take(8) as $sale)
            <div class="activity-item">
                <div class="activity-icon" style="background: var(--success-green);">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">
                        Nova venda #{{ $sale->id }}
                    </div>
                    <div class="activity-meta">
                        <span>{{ $sale->user->name ?? 'Sistema' }}</span>
                        <span class="activity-value">MT {{ number_format($sale->total_amount, 2, ',', '.') }}</span>
                        <span>{{ $sale->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="activity-item">
                <div class="activity-icon" style="background: var(--text-muted);">
                    <i class="fas fa-inbox"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">Nenhuma atividade recente</div>
                    <div class="activity-meta">
                        <span>Comece fazendo uma venda!</span>
                    </div>
                </div>
            </div>
            @endforelse
            
            <div class="p-3 text-center border-top">
                <a href="{{ route('sales.index') }}" class="text-decoration-none small">
                    <i class="fas fa-eye me-1"></i>
                    Ver todas as vendas
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats Comparison -->
<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="stats-comparison">
            <h5 class="mb-3">
                <i class="fas fa-balance-scale text-info me-2"></i>
                Comparativo Mensal
            </h5>
            
            <div class="comparison-item">
                <span class="comparison-label">Vendas este mês</span>
                <div class="comparison-values">
                    <span class="comparison-current">MT {{ number_format($monthSales, 2, ',', '.') }}</span>
                    <span class="comparison-previous">vs MT 45.230,00 anterior</span>
                </div>
            </div>
            
            <div class="comparison-item">
                <span class="comparison-label">Despesas este mês</span>
                <div class="comparison-values">
                    <span class="comparison-current">MT {{ number_format($monthExpenses, 2, ',', '.') }}</span>
                    <span class="comparison-previous">vs MT 18.450,00 anterior</span>
                </div>
            </div>
            
            <div class="comparison-item">
                <span class="comparison-label">Lucro líquido</span>
                <div class="comparison-values">
                    <span class="comparison-current text-success">
                        MT {{ number_format($monthSales - $monthExpenses, 2, ',', '.') }}
                    </span>
                    <span class="comparison-previous">+24% vs anterior</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="stats-comparison">
            <h5 class="mb-3">
                <i class="fas fa-target text-warning me-2"></i>
                Metas e Objetivos
            </h5>
            
            <div class="comparison-item">
                <span class="comparison-label">Meta de vendas mensal</span>
                <div class="comparison-values">
                    <span class="comparison-current">
                        {{ $monthSales > 0 ? round(($monthSales / 100000) * 100, 1) : 0 }}%
                    </span>
                    <span class="comparison-previous">MT 100.000,00 meta</span>
                </div>
            </div>
            
            <div class="comparison-item">
                <span class="comparison-label">Produtos vendidos hoje</span>
                <div class="comparison-values">
                    <span class="comparison-current">{{ $recentSales->sum(function($sale) { return $sale->items->sum('quantity'); }) }}</span>
                    <span class="comparison-previous">unidades</span>
                </div>
            </div>
            
            <div class="comparison-item">
                <span class="comparison-label">Clientes ativos</span>
                <div class="comparison-values">
                    <span class="comparison-current">{{ \App\Models\Sale::distinct('customer_name')->whereNotNull('customer_name')->count() }}</span>
                    <span class="comparison-previous">cadastrados</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== RELÓGIO EM TEMPO REAL =====
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('pt-PT', { 
            hour: '2-digit', 
            minute: '2-digit'
        });
        const clockElement = document.getElementById('current-time');
        if (clockElement) {
            clockElement.textContent = timeString;
        }
    }
    
    updateClock();
    setInterval(updateClock, 1000);

    // ===== GRÁFICO DE VENDAS =====
    const ctx = document.getElementById('salesChart');
    if (ctx && window.Chart) {
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
                datasets: [{
                    label: 'Vendas',
                    data: [12000, 19000, 15000, 25000, 22000, 30000, {{ $todaySales }}],
                    borderColor: '#5B9BD5',
                    backgroundColor: 'rgba(91, 155, 213, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Despesas',
                    data: [8000, 12000, 9000, 15000, 13000, 18000, {{ $todayExpenses }}],
                    borderColor: '#FFA500',
                    backgroundColor: 'rgba(255, 165, 0, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                family: 'Segoe UI, Inter, system-ui',
                                size: 12,
                                weight: '500'
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Segoe UI, Inter, system-ui',
                                size: 11
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            font: {
                                family: 'Segoe UI, Inter, system-ui',
                                size: 11
                            },
                            callback: function(value) {
                                return 'MT ' + value.toLocaleString();
                            }
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 5,
                        hoverRadius: 8,
                        backgroundColor: '#fff',
                        borderWidth: 2
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutCubic'
                }
            }
        });
    }

    // ===== ATUALIZAÇÃO AUTOMÁTICA DE MÉTRICAS =====
    function updateDashboardMetrics() {
        fetch('{{ route("dashboard.metrics") }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.ok ? response.json() : Promise.reject('Erro na resposta'))
        .then(data => {
            // Atualizar vendas de hoje
            const todaySalesElement = document.getElementById('today-sales');
            if (todaySalesElement && data.todaySales !== undefined) {
                const newValue = parseFloat(data.todaySales).toLocaleString('pt-PT', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                
                if (todaySalesElement.textContent !== newValue) {
                    todaySalesElement.classList.add('metric-loading');
                    setTimeout(() => {
                        todaySalesElement.textContent = newValue;
                        todaySalesElement.classList.remove('metric-loading');
                        
                        // Efeito visual de atualização
                        todaySalesElement.style.transform = 'scale(1.05)';
                        todaySalesElement.style.color = 'var(--success-green)';
                        setTimeout(() => {
                            todaySalesElement.style.transform = 'scale(1)';
                            todaySalesElement.style.color = '';
                        }, 300);
                    }, 500);
                }
            }

            // Atualizar despesas de hoje
            const todayExpensesElement = document.getElementById('today-expenses');
            if (todayExpensesElement && data.todayExpenses !== undefined) {
                const newValue = parseFloat(data.todayExpenses).toLocaleString('pt-PT', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                
                if (todayExpensesElement.textContent !== newValue) {
                    todayExpensesElement.textContent = newValue;
                }
            }

            // Atualizar contagem de estoque baixo
            const lowStockElement = document.getElementById('low-stock-count');
            if (lowStockElement && data.lowStockCount !== undefined) {
                if (parseInt(lowStockElement.textContent) !== data.lowStockCount) {
                    lowStockElement.textContent = data.lowStockCount;
                    
                    // Atualizar cor do card baseado no estoque
                    const stockCard = lowStockElement.closest('.metric-card');
                    const stockIcon = stockCard?.querySelector('.metric-icon');
                    const stockChange = stockCard?.querySelector('.metric-change');
                    
                    if (stockCard && stockIcon && stockChange) {
                        if (data.lowStockCount > 0) {
                            stockCard.className = 'metric-card danger';
                            stockIcon.className = 'metric-icon danger';
                            stockChange.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>Atenção requerida</span>';
                            stockChange.className = 'metric-change negative pulse';
                        } else {
                            stockCard.className = 'metric-card success';
                            stockIcon.className = 'metric-icon success';
                            stockChange.innerHTML = '<i class="fas fa-check"></i><span>Tudo em ordem</span>';
                            stockChange.className = 'metric-change positive';
                        }
                    }
                }
            }

            console.log('📊 Métricas atualizadas:', data);
        })
        .catch(error => {
            console.warn('⚠️ Erro ao atualizar métricas:', error);
        });
    }

    // Atualizar métricas a cada 30 segundos
    setInterval(updateDashboardMetrics, 30000);
    
    // Primeira atualização após 5 segundos
    setTimeout(updateDashboardMetrics, 5000);

    // ===== ANIMAÇÕES DE ENTRADA =====
    function animateCounters() {
        const counters = document.querySelectorAll('.metric-value');
        counters.forEach(counter => {
            const target = parseFloat(counter.textContent.replace(/[,.]/g, ''));
            if (isNaN(target)) return;
            
            let start = 0;
            const duration = 2000;
            const increment = target / (duration / 16);
            
            const timer = setInterval(() => {
                start += increment;
                if (start >= target) {
                    counter.textContent = target.toLocaleString('pt-PT', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(start).toLocaleString('pt-PT', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            }, 16);
        });
    }

    // Executar animações após carregamento
    setTimeout(animateCounters, 500);

    // ===== INTERAÇÕES AVANÇADAS =====
    
    // Hover effects nos cards de métricas
    document.querySelectorAll('.metric-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Click para refresh manual das métricas
    document.querySelectorAll('.metric-card').forEach(card => {
        card.addEventListener('dblclick', function() {
            const valueElement = this.querySelector('.metric-value');
            if (valueElement) {
                valueElement.classList.add('metric-loading');
                setTimeout(() => {
                    valueElement.classList.remove('metric-loading');
                    updateDashboardMetrics();
                }, 1000);
            }
        });
    });

    // ===== NOTIFICAÇÕES INTELIGENTES =====
    function checkCriticalAlerts() {
        // Verificar se há produtos com estoque crítico (0)
        const lowStockCount = parseInt(document.getElementById('low-stock-count')?.textContent || '0');
        
        if (lowStockCount > 5 && !localStorage.getItem('critical_stock_notified_today')) {
            window.FDSMULTSERVICES?.Toast?.show(
                `🚨 CRÍTICO: ${lowStockCount} produtos estão com estoque baixo!`,
                'error'
            );
            localStorage.setItem('critical_stock_notified_today', new Date().toDateString());
        }

        // Verificar vendas excepcionais
        const todaySales = parseFloat(document.getElementById('today-sales')?.textContent.replace(/[,.]/g, '') || '0');
        if (todaySales > 50000 && !localStorage.getItem('high_sales_celebrated_today')) {
            window.FDSMULTSERVICES?.Toast?.show(
                '🎉 Excelente! Vendas hoje já passaram de MT 50.000!',
                'success'
            );
            localStorage.setItem('high_sales_celebrated_today', new Date().toDateString());
        }
    }

    // Executar verificações após 3 segundos
    setTimeout(checkCriticalAlerts, 3000);

    // ===== KEYBOARD SHORTCUTS =====
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + R para refresh das métricas
        if ((e.ctrlKey || e.metaKey) && e.key === 'r' && e.shiftKey) {
            e.preventDefault();
            window.FDSMULTSERVICES?.Toast?.show('🔄 Atualizando métricas...', 'info');
            updateDashboardMetrics();
        }
        
        // Ctrl/Cmd + N para nova venda (se permitido)
        if ((e.ctrlKey || e.metaKey) && e.key === 'n' && @json(userCan('create_sales'))) {
            e.preventDefault();
            window.location.href = '{{ route("sales.create") }}';
        }
    });

    // ===== RESPONSIVE CHART HANDLING =====
    window.addEventListener('resize', function() {
        if (chart && chart.resize) {
            chart.resize();
        }
    });

    console.log('🚀 Dashboard profissional carregado com sucesso!');
    console.log('⌨️  Atalhos: Ctrl+Shift+R (refresh), Ctrl+N (nova venda)');
    console.log('🔄 Auto-refresh: Ativo a cada 30 segundos');
});

// ===== GLOBAL DASHBOARD FUNCTIONS =====
window.DashboardUtils = {
    refreshMetrics: function() {
        document.dispatchEvent(new CustomEvent('dashboard:refresh'));
    },
    
    exportDashboard: function() {
        window.FDSMULTSERVICES?.Toast?.show('📊 Preparando exportação...', 'info');
        // Implementar lógica de exportação aqui
    },
    
    toggleAutoRefresh: function(enabled) {
        localStorage.setItem('dashboard_auto_refresh', enabled);
        if (enabled) {
            window.FDSMULTSERVICES?.Toast?.show('✅ Auto-refresh ativado', 'success');
        } else {
            window.FDSMULTSERVICES?.Toast?.show('⏸️ Auto-refresh pausado', 'warning');
        }
    }
};
</script>
@endpush