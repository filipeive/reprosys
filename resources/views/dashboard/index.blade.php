@extends('layouts.app')

@section('title', 'Dashboard Principal')
@section('page-title', 'Dashboard Executivo')

@push('styles')
{{-- Seu CSS original vai aqui. Ele já está ótimo e compatível com o layout. --}}
{{-- Nenhuma mudança necessária no seu CSS. --}}
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
        transform: translateY(-5px) scale(1.02); /* Adicionado scale para 'pop' */
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
        /* Adiciona transição para suavizar a atualização */
        transition: color 0.3s ease; 
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
        .dashboard-welcome { padding: 20px; }
        .metric-card { padding: 15px; height: 120px; }
        .metric-value { font-size: 24px; }
        .metric-icon { width: 40px; height: 40px; font-size: 16px; }
        .chart-container { height: 300px; padding: 15px; }
        .quick-actions { grid-template-columns: 1fr; }
    }

    /* ===== LOADING STATES (Seu CSS original, está perfeito) ===== */
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
<div class="dashboard-welcome">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="mb-2">
                {{-- Lógica de saudação dinâmica baseada na hora --}}
                <span id="welcome-greeting">Bom dia</span>, {{ explode(' ', auth()->user()->name)[0] }}!
            </h2>
            <p class="mb-0 opacity-90">
                Bem-vindo ao painel de controle do FDSMULTSERVICES+. 
                Aqui você tem uma visão completa do seu negócio em tempo real.
            </p>
        </div>
        <div class="col-md-4 text-end">
            <div class="d-flex flex-column text-end">
                <small class="opacity-75">{{ now()->translatedFormat('l, j \d\e F \d\e Y') }}</small>
                <h4 class="mb-0" id="current-time">{{ now()->format('H:i') }}</h4>
            </div>
        </div>
    </div>
</div>

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

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="metric-card success">
            <div class="metric-icon success">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="metric-value" id="today-sales" data-target="{{ $todaySales }}">
                {{ number_format($todaySales, 2, ',', '.') }}
            </div>
            <div class="metric-label">Vendas de Hoje</div>
            {{-- DADO DINÂMICO --}}
            <div class="metric-change {{ $salesChangeDirection }}" id="today-sales-change">
                <i class="fas {{ $salesChangeIcon }}"></i>
                <span id="today-sales-change-percent">{{ $salesChangePercent }}%</span>
                <span>vs ontem</span>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="metric-card warning">
            <div class="metric-icon warning">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="metric-value" id="today-expenses" data-target="{{ $todayExpenses }}">
                {{ number_format($todayExpenses, 2, ',', '.') }}
            </div>
            <div class="metric-label">Despesas de Hoje</div>
            {{-- DADO DINÂMICO --}}
            <div class="metric-change {{ $expensesChangeDirection }}" id="today-expenses-change">
                <i class="fas {{ $expensesChangeIcon }}"></i>
                <span id="today-expenses-change-percent">{{ $expensesChangePercent }}%</span>
                <span>vs ontem</span>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="metric-card primary">
            <div class="metric-icon primary">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="metric-value" id="month-sales" data-target="{{ $monthSales }}">
                {{ number_format($monthSales, 2, ',', '.') }}
            </div>
            <div class="metric-label">Vendas do Mês</div>
            {{-- DADO DINÂMICO --}}
            <div class="metric-change {{ $monthSalesChangeDirection }}" id="month-sales-change">
                <i class="fas {{ $monthSalesChangeIcon }}"></i>
                <span id="month-sales-change-percent">{{ $monthSalesChangePercent }}%</span>
                <span>vs mês anterior</span>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="metric-card {{ $lowStockProducts->count() > 0 ? 'danger' : 'success' }}">
            <div class="metric-icon {{ $lowStockProducts->count() > 0 ? 'danger' : 'success' }}">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="metric-value" id="low-stock-count" data-target="{{ $lowStockProducts->count() }}">
                {{ $lowStockProducts->count() }}
            </div>
            <div class="metric-label">Produtos Baixo Estoque</div>
            <div id="low-stock-change">
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
</div>

{{-- Esta seção agora combina o alerta da sessão (carga da página) E o alerta de estoque baixo --}}
@if(session('dashboard_alert') || $lowStockProducts->count() > 0)
<div class="alerts-section">
    <h5 class="mb-3">
        <i class="fas fa-bell text-warning me-2"></i>
        Alertas e Notificações
    </h5>
    
    {{-- Alerta da Sessão (vindo do checkAndSetAlerts) --}}
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
    
    {{-- Alerta de Estoque Baixo (sempre visível se houver) --}}
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
    <div class="col-xl-8">
        <div class="chart-container">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-chart-area text-primary"></i>
                    Vendas vs Despesas (Últimos 7 dias)
                </div>
                {{-- Período agora é dinâmico --}}
                <span class="chart-period">Período: {{ $salesChartData['labels'][0] }} - {{ end($salesChartData['labels']) }}/{{ now()->year }}</span>
            </div>
            {{-- Canvas para o Chart.js --}}
            <canvas id="salesChart" style="max-height: 320px;"></canvas>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="activity-feed">
            <div class="activity-header">
                <i class="fas fa-clock me-2"></i>
                Atividades Recentes
            </div>
            
            @forelse($recentSales as $sale)
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
                    {{-- DADO DINÂMICO --}}
                    <span class="comparison-previous">vs MT {{ number_format($prevMonthSales, 2, ',', '.') }} anterior</span>
                </div>
            </div>
            
            <div class="comparison-item">
                <span class="comparison-label">Despesas este mês</span>
                <div class="comparison-values">
                    <span class="comparison-current">MT {{ number_format($monthExpenses, 2, ',', '.') }}</span>
                    {{-- DADO DINÂMICO --}}
                    <span class="comparison-previous">vs MT {{ number_format($prevMonthExpenses, 2, ',', '.') }} anterior</span>
                </div>
            </div>
            
            <div class="comparison-item">
                <span class="comparison-label">Lucro líquido</span>
                <div class="comparison-values">
                    <span class="comparison-current {{ $monthProfitChangeDirection }}">
                        MT {{ number_format($monthProfit, 2, ',', '.') }}
                    </span>
                    {{-- DADO DINÂMICO --}}
                    <span class="comparison-previous {{ $monthProfitChangeDirection }}">
                        {{ $monthProfitChangePercent > 0 ? '+' : '' }}{{ $monthProfitChangePercent }}% vs anterior
                    </span>
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
            
            @php
                // TODO: Mover a meta para as Configurações do Sistema
                $monthlySalesGoal = 100000; 
                $salesGoalPercent = $monthlySalesGoal > 0 ? round(($monthSales / $monthlySalesGoal) * 100, 1) : 0;
            @endphp
            
            <div class="comparison-item">
                <span class="comparison-label">Meta de vendas mensal</span>
                <div class="comparison-values">
                    <span class="comparison-current">
                        {{ $salesGoalPercent }}%
                    </span>
                    <span class="comparison-previous">MT {{ number_format($monthlySalesGoal, 2, ',', '.') }} meta</span>
                </div>
            </div>
            
            <div class="comparison-item">
                <span class="comparison-label">Produtos vendidos hoje</span>
                <div class="comparison-values">
                    {{-- DADO DINÂMICO --}}
                    <span class="comparison-current">{{ $todayProductsSold }}</span>
                    <span class="comparison-previous">unidades</span>
                </div>
            </div>
            
            <div class="comparison-item">
                <span class="comparison-label">Clientes este mês</span>
                <div class="comparison-values">
                    {{-- DADO DINÂMICO --}}
                    <span class="comparison-current">{{ $monthActiveCustomers }}</span>
                    <span class="comparison-previous">clientes</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Chart.js já está incluído no seu layout.app, mas se não estiver, adicione:
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
--}}
<script>
// Variável global para o gráfico, para que possamos atualizá-lo
let mySalesChart;

document.addEventListener('DOMContentLoaded', function() {
    
    // ===== SAUDAÇÃO DINÂMICA =====
    function updateGreeting() {
        const hour = new Date().getHours();
        const greetingElement = document.getElementById('welcome-greeting');
        if (!greetingElement) return;
        
        if (hour < 12) {
            greetingElement.textContent = 'Bom dia';
        } else if (hour < 18) {
            greetingElement.textContent = 'Boa tarde';
        } else {
            greetingElement.textContent = 'Boa noite';
        }
    }
    
    // ===== RELÓGIO EM TEMPO REAL =====
    function updateClock() {
        const now = new Date();
        // Usando pt-PT para Moçambique (formato 24h)
        const timeString = now.toLocaleTimeString('pt-PT', { 
            hour: '2-digit', 
            minute: '2-digit'
        });
        const clockElement = document.getElementById('current-time');
        if (clockElement) {
            clockElement.textContent = timeString;
        }
    }
    
    updateGreeting();
    updateClock();
    setInterval(updateClock, 1000); // Atualiza relógio a cada segundo

    // ===== GRÁFICO DE VENDAS DINÂMICO =====
    const ctx = document.getElementById('salesChart');
    if (ctx && window.Chart) {
        mySalesChart = new Chart(ctx, {
            type: 'line',
            data: {
                // DADOS VINDOS DO CONTROLLER
                labels: @json($salesChartData['labels']),
                datasets: [{
                    label: 'Vendas',
                    // DADOS VINDOS DO CONTROLLER
                    data: @json($salesChartData['salesData']),
                    borderColor: 'var(--primary-blue)', // Usa variável CSS
                    backgroundColor: 'rgba(91, 155, 213, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Despesas',
                    // DADOS VINDOS DO CONTROLLER
                    data: @json($salesChartData['expensesData']),
                    borderColor: 'var(--warning-orange)', // Usa variável CSS
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
                            color: 'var(--text-secondary)', // Cor do texto baseada no tema
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
                            color: 'var(--text-secondary)', // Cor do texto baseada no tema
                            font: {
                                family: 'Segoe UI, Inter, system-ui',
                                size: 11
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'var(--border-color)' // Cor da grade baseada no tema
                        },
                        ticks: {
                            color: 'var(--text-secondary)', // Cor do texto baseada no tema
                            font: {
                                family: 'Segoe UI, Inter, system-ui',
                                size: 11
                            },
                            callback: function(value) {
                                // Formata para Moeda
                                return 'MT ' + value.toLocaleString('pt-PT');
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
                }
            }
        });
    }

    // ===== ANIMAÇÕES DE ENTRADA (LENDO DE data-target) =====
    function animateCounters() {
        document.querySelectorAll('.metric-value[data-target]').forEach(counter => {
            const target = parseFloat(counter.getAttribute('data-target'));
            if (isNaN(target)) return;
            
            const elementId = counter.id;
            // Evitar re-animar valores que não mudaram
            if (counter.dataset.animatedValue == target) return; 
            
            counter.dataset.animatedValue = target; // Marcar como animado
            
            let start = 0;
            const duration = 1500; // Duração mais rápida
            
            // Tentar pegar o valor atual se já estiver na tela
            const currentValueText = counter.textContent.replace(/[,.]/g, '');
            let currentValue = parseFloat(currentValueText) / 100; // Ajustar para centavos
            if (isNaN(currentValue) || currentValue === 0) {
                 // Começa de 0 se for a primeira vez
                 start = 0;
            } else {
                // Começa do valor atual para uma transição suave
                start = currentValue; 
            }

            const increment = (target - start) / (duration / 16);

            const timer = setInterval(() => {
                start += increment;
                
                if ((increment > 0 && start >= target) || (increment < 0 && start <= target) || increment === 0) {
                    // Animação concluída
                    counter.textContent = formatCurrency(target, false);
                    clearInterval(timer);
                } else {
                    // Durante a animação
                    counter.textContent = formatCurrency(start, false);
                }
            }, 16); // ~60fps
        });
    }
    
    // Helper para formatar moeda (simplificado, sem MT)
    function formatCurrency(value, usePrefix = true) {
         const formatted = (value || 0).toLocaleString('pt-PT', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return usePrefix ? 'MT ' + formatted : formatted;
    }

    // Executar animações após um pequeno delay
    setTimeout(animateCounters, 300);

    // ===== ATUALIZAÇÃO AUTOMÁTICA DE MÉTRICAS (MELHORADA) =====
    // Estado para evitar alertas repetidos
    let lastAlerts = []; 
    
    function updateDashboardMetrics() {
        fetch('{{ route("dashboard.metrics") }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest' // Importante para o Laravel
            }
        })
        .then(response => response.ok ? response.json() : Promise.reject('Erro na resposta'))
        .then(data => {
            console.log('📊 Métricas atualizadas:', data);

            // 1. Atualizar Métrica: Vendas de Hoje
            updateMetricCard('today-sales', data.todaySales, data.salesChangePercent, data.salesChangeDirection, data.salesChangeIcon);
            
            // 2. Atualizar Métrica: Despesas de Hoje
            updateMetricCard('today-expenses', data.todayExpenses, data.expensesChangePercent, data.expensesChangeDirection, data.expensesChangeIcon);

            // 3. Atualizar Métrica: Estoque Baixo
            const lowStockElement = document.getElementById('low-stock-count');
            if (lowStockElement && parseInt(lowStockElement.textContent) !== data.lowStockCount) {
                lowStockElement.textContent = data.lowStockCount;
                
                const stockCard = lowStockElement.closest('.metric-card');
                const stockIcon = stockCard?.querySelector('.metric-icon');
                const stockChangeContainer = document.getElementById('low-stock-change');
                
                if (stockCard && stockIcon && stockChangeContainer) {
                    if (data.lowStockCount > 0) {
                        stockCard.className = 'metric-card danger';
                        stockIcon.className = 'metric-icon danger';
                        stockChangeContainer.innerHTML = `<div class="metric-change negative pulse"><i class="fas fa-exclamation-triangle"></i><span>Atenção requerida</span></div>`;
                    } else {
                        stockCard.className = 'metric-card success';
                        stockIcon.className = 'metric-icon success';
                        stockChangeContainer.innerHTML = `<div class="metric-change positive"><i class="fas fa-check"></i><span>Tudo em ordem</span></div>`;
                    }
                }
            }
            
            // 4. Atualizar Gráfico em Tempo Real
            if (mySalesChart && data.salesChartData) {
                mySalesChart.data.labels = data.salesChartData.labels;
                mySalesChart.data.datasets[0].data = data.salesChartData.salesData;
                mySalesChart.data.datasets[1].data = data.salesChartData.expensesData;
                mySalesChart.update('none'); // Atualiza sem animação para ser mais rápido
            }
            
            // 5. Mostrar Alertas Dinâmicos (com Toasts)
            if (data.dynamicAlerts && data.dynamicAlerts.length > 0) {
                const newAlertMessages = data.dynamicAlerts.map(a => a.message).join('|');
                const oldAlertMessages = lastAlerts.map(a => a.message).join('|');

                // Só mostra o toast se for um alerta novo
                if (newAlertMessages !== oldAlertMessages && window.FDSMULTSERVICES?.Toast) {
                    data.dynamicAlerts.forEach(alert => {
                        window.FDSMULTSERVICES.Toast.show(alert.message, alert.type);
                    });
                    lastAlerts = data.dynamicAlerts; // Armazena os últimos alertas
                }
            } else {
                lastAlerts = []; // Limpa os alertas se não houver mais
            }

        })
        .catch(error => {
            console.warn('⚠️ Erro ao atualizar métricas:', error);
            // Poderia mostrar um toast de erro
            // window.FDSMULTSERVICES.Toast.show('Erro ao conectar ao servidor.', 'error');
        });
    }

    // Helper para atualizar os cards de métrica
    function updateMetricCard(idPrefix, value, percent, direction, icon) {
        const valueElement = document.getElementById(idPrefix);
        const changeElement = document.getElementById(idPrefix + '-change');
        if (!valueElement || !changeElement) return;

        const newValueFormatted = formatCurrency(value, false);
        
        // Só atualiza se o valor mudou
        if (valueElement.textContent !== newValueFormatted) {
            valueElement.classList.add('metric-loading');
            
            setTimeout(() => {
                valueElement.textContent = newValueFormatted;
                valueElement.setAttribute('data-target', value); // Atualiza o target da animação
                
                // Atualiza a comparação
                const percentElement = document.getElementById(idPrefix + '-change-percent');
                const iconElement = changeElement.querySelector('i');
                
                if (percentElement) percentElement.textContent = percent + '%';
                if (iconElement) iconElement.className = 'fas ' + icon;
                changeElement.className = 'metric-change ' + direction;
                
                // Efeito visual de atualização
                valueElement.classList.remove('metric-loading');
                valueElement.style.transform = 'scale(1.05)';
                valueElement.style.color = 'var(--success-green)';
                setTimeout(() => {
                    valueElement.style.transform = 'scale(1)';
                    valueElement.style.color = ''; // Volta à cor normal
                }, 300);
            }, 500);
        }
    }

    // ===== INICIALIZAÇÃO DO POLLING =====
    
    // Atualizar métricas a cada 30 segundos
    const metricsInterval = setInterval(updateDashboardMetrics, 30000);
    
    // Primeira atualização após 5 segundos da carga
    setTimeout(updateDashboardMetrics, 5000);

    // Pausar atualização se a aba não estiver visível (melhor performance)
    document.addEventListener("visibilitychange", () => {
        if (document.hidden) {
            clearInterval(metricsInterval);
        } else {
            // Roda imediatamente ao voltar e reinicia o timer
            updateDashboardMetrics();
            setInterval(updateDashboardMetrics, 30000);
        }
    });

    // ===== Atalhos de Teclado (do seu código original) =====
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'r' && e.shiftKey) {
            e.preventDefault();
            window.FDSMULTSERVICES?.Toast?.show('🔄 Atualizando métricas manualmente...', 'info');
            updateDashboardMetrics();
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'n' && @json(userCan('create_sales'))) {
            e.preventDefault();
            window.location.href = '{{ route("sales.create") }}';
        }
    });
    
    console.log('🚀 Dashboard profissional carregado com sucesso!');
});
</script>
@endpush