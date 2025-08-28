@extends('layouts.app')

@section('title', 'Gestão de Vendas')
@section('page-title', 'Gestão de Vendas')
@section('title-icon', 'fa-shopping-cart')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Vendas</li>
@endsection

@section('content')
    <!-- Header com botões de ação -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-shopping-cart me-2"></i>
                Gestão de Vendas
            </h2>
            <p class="text-muted mb-0">Controle completo das vendas da sua reprografia</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sales.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nova Venda
            </a>
            <a href="{{ route('sales.manual-create') }}" class="btn btn-success">
                <i class="fas fa-edit me-2"></i> Venda Manual
            </a>
            <button class="btn btn-outline-primary" onclick="exportSales()">
                <i class="fas fa-download me-2"></i> Exportar
            </button>
        </div>
    </div>

    <!-- Filtros e Pesquisa -->
    <div class="card mb-4 fade-in">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i> 
                Filtros de Pesquisa
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('sales.index') }}" id="filter-form">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Pesquisar Cliente</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}"
                                placeholder="Nome ou telefone do cliente...">
                            @if(request('search'))
                                <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Limpar pesquisa">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Data Inicial</label>
                        <input type="date" class="form-control" id="date_from" name="date_from"
                            value="{{ request('date_from') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Data Final</label>
                        <input type="date" class="form-control" id="date_to" name="date_to"
                            value="{{ request('date_to') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label for="payment_method" class="form-label">Pagamento</label>
                        <select class="form-select" id="payment_method" name="payment_method">
                            <option value="">Todos</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>
                                <i class="fas fa-money-bill"></i> Dinheiro
                            </option>
                            <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>
                                <i class="fas fa-credit-card"></i> Cartão
                            </option>
                            <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>
                                <i class="fas fa-exchange-alt"></i> Transferência
                            </option>
                            <option value="credit" {{ request('payment_method') == 'credit' ? 'selected' : '' }}>
                                <i class="fas fa-clock"></i> Crédito
                            </option>
                        </select>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Filtrar
                            </button>
                            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i> Limpar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Total de Vendas</h6>
                            <h3 class="mb-0 text-primary fw-bold">{{ number_format($sales->total()) }}</h3>
                            <small class="text-muted">vendas registradas</small>
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
                            <h6 class="text-muted mb-2 fw-semibold">Valor Total</h6>
                            <h3 class="mb-0 text-success fw-bold">{{ number_format($sales->sum('total_amount'), 2, ',', '.') }} MT</h3>
                            <small class="text-muted">em vendas</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Vendas Hoje</h6>
                            <h3 class="mb-0 text-warning fw-bold">{{ $sales->where('sale_date', now()->toDateString())->count() }}</h3>
                            <small class="text-muted">vendas de hoje</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-calendar-day fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Média por Venda</h6>
                            <h3 class="mb-0 text-info fw-bold">{{ $sales->count() > 0 ? number_format($sales->avg('total_amount'), 2, ',', '.') : '0,00' }} MT</h3>
                            <small class="text-muted">ticket médio</small>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Vendas -->
    <div class="card fade-in">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-list me-2 text-primary"></i> 
                    Vendas Registradas
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary">Total: {{ $sales->total() }}</span>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" onclick="exportSales()"><i class="fas fa-download me-2"></i>Exportar Lista</a></li>
                            <li><a class="dropdown-item" href="#" onclick="printList()"><i class="fas fa-print me-2"></i>Imprimir Lista</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('sales.report') }}"><i class="fas fa-chart-bar me-2"></i>Relatório Detalhado</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="sales-table">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Cliente</th>
                            <th style="width: 120px;">Telefone</th>
                            <th style="width: 140px;">Data</th>
                            <th style="width: 80px;" class="text-center">Itens</th>
                            <th style="width: 140px;">Pagamento</th>
                            <th style="width: 120px;" class="text-end">Total</th>
                            <th style="width: 100px;">Vendedor</th>
                            <th style="width: 200px;" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr class="sale-row" data-sale-id="{{ $sale->id }}">
                                <td>
                                    <span class="fw-bold text-primary">#{{ $sale->id }}</span>
                                </td>
                                
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">{{ $sale->customer_name ?: 'Cliente Avulso' }}</span>
                                        @if($sale->notes)
                                            <small class="text-muted">{{ Str::limit($sale->notes, 35) }}</small>
                                        @endif
                                    </div>
                                </td>
                                
                                <td>
                                    @if($sale->customer_phone)
                                        <a href="tel:{{ $sale->customer_phone }}" class="text-decoration-none">
                                            <i class="fas fa-phone me-1"></i>{{ $sale->customer_phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">{{ $sale->sale_date->format('d/m/Y') }}</span>
                                        <small class="text-muted">{{ $sale->sale_date->format('H:i') }}</small>
                                    </div>
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $sale->items->count() }}</span>
                                </td>
                                
                                <td>
                                    @switch($sale->payment_method)
                                        @case('cash')
                                            <span class="badge bg-success">
                                                <i class="fas fa-money-bill me-1"></i>Dinheiro
                                            </span>
                                            @break
                                        @case('card')
                                            <span class="badge bg-primary">
                                                <i class="fas fa-credit-card me-1"></i>Cartão
                                            </span>
                                            @break
                                        @case('transfer')
                                            <span class="badge bg-info">
                                                <i class="fas fa-exchange-alt me-1"></i>Transferência
                                            </span>
                                            @break
                                        @case('credit')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Crédito
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $sale->payment_method }}</span>
                                    @endswitch
                                </td>
                                
                                <td class="text-end">
                                    <span class="fw-bold text-success fs-6">
                                        {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                                    </span>
                                </td>
                                
                                <td>
                                    <small class="text-muted">{{ $sale->user->name ?? 'N/A' }}</small>
                                </td>
                                
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-info" 
                                                onclick="quickView({{ $sale->id }})" 
                                                title="Visualização Rápida"
                                                data-bs-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <a href="{{ route('sales.show', $sale->id) }}" 
                                           class="btn btn-outline-primary" 
                                           title="Ver Detalhes"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        
                                        <a href="{{ route('sales.edit', $sale->id) }}" 
                                           class="btn btn-outline-warning" 
                                           title="Editar"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <a href="{{ route('sales.print', $sale->id) }}" 
                                           class="btn btn-outline-secondary" 
                                           title="Imprimir" 
                                           target="_blank"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirmDelete({{ $sale->id }}, '{{ $sale->customer_name ?: 'Cliente Avulso' }}')" 
                                                title="Cancelar Venda"
                                                data-bs-toggle="tooltip">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <i class="fas fa-search fa-3x mb-3 opacity-50"></i>
                                        <h5>Nenhuma venda encontrada</h5>
                                        <p class="mb-3">Não há vendas que correspondam aos filtros aplicados.</p>
                                        <a href="{{ route('sales.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Registrar Nova Venda
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($sales->hasPages())
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Mostrando {{ $sales->firstItem() ?? 0 }} a {{ $sales->lastItem() ?? 0 }} 
                                de {{ $sales->total() }} resultados
                            </small>
                        </div>
                        <nav>
                            {{ $sales->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-trash me-2"></i>Confirmar Cancelamento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-exclamation-triangle text-warning fa-3x"></i>
                    </div>
                    <p class="text-center mb-3">Tem certeza que deseja cancelar esta venda?</p>
                    <div class="alert alert-info">
                        <h6><strong>Cliente: <span id="delete-customer-name"></span></strong></h6>
                        <small><i class="fas fa-info-circle me-1"></i> O stock dos produtos será restaurado automaticamente.</small>
                    </div>
                    <p class="text-muted text-center mb-0">
                        <small>Esta ação não pode ser desfeita.</small>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <form id="delete-form" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Cancelar Venda
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Visualização Rápida -->
    <div class="modal fade" id="quickViewModal" tabindex="-1" data-bs-backdrop="true" data-bs-keyboard="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="z-index: 1055;">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-2"></i>Detalhes da Venda #<span id="sale-number"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body" id="quick-view-content" style="max-height: 60vh; overflow-y: auto;">
                    <div class="text-center py-5">
                        <div class="loading-spinner mb-3"></div>
                        <p class="text-muted">Carregando detalhes...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Fechar
                    </button>
                    <a href="#" id="view-full-sale" class="btn btn-primary" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Completo
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Limpar pesquisa
    const clearSearchBtn = document.getElementById('clear-search');
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            document.getElementById('search').value = '';
            document.getElementById('filter-form').submit();
        });
    }

    // Auto-submit nos filtros
    document.getElementById('date_from')?.addEventListener('change', function() {
        document.getElementById('filter-form').submit();
    });

    document.getElementById('date_to')?.addEventListener('change', function() {
        document.getElementById('filter-form').submit();
    });

    document.getElementById('payment_method')?.addEventListener('change', function() {
        document.getElementById('filter-form').submit();
    });
});

// Função para confirmar exclusão
function confirmDelete(saleId, customerName) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    document.getElementById('delete-customer-name').textContent = customerName;
    document.getElementById('delete-form').action = `/sales/${saleId}`;
    modal.show();
}

// Função para visualização rápida
function quickView(saleId) {
    // Remover qualquer backdrop existente primeiro
    const existingBackdrop = document.querySelector('.modal-backdrop');
    if (existingBackdrop) {
        existingBackdrop.remove();
    }
    
    const modal = document.getElementById('quickViewModal');
    
    // Configurar modal sem backdrop problemático
    const modalInstance = new bootstrap.Modal(modal, {
        backdrop: 'static',
        keyboard: true,
        focus: true
    });
    
    const content = document.getElementById('quick-view-content');
    const saleNumber = document.getElementById('sale-number');
    
    // Reset content e mostrar loading
    content.innerHTML = `
        <div class="text-center py-5">
            <div class="loading-spinner mb-3"></div>
            <p class="text-muted">Carregando detalhes...</p>
        </div>
    `;
    
    // Atualizar número da venda
    saleNumber.textContent = saleId;
    
    // Update view full link
    document.getElementById('view-full-sale').href = `/sales/${saleId}`;
    
    // Mostrar modal
    modalInstance.show();
    
    // Fix para backdrop após modal aparecer
    setTimeout(() => {
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.style.pointerEvents = 'none';
        }
        
        // Garantir que o modal content seja clicável
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.style.pointerEvents = 'auto';
            modalContent.style.zIndex = '1060';
        }
    }, 100);
    
    // Simular dados da venda (já que quick-view pode não estar implementado)
    setTimeout(() => {
        // Buscar dados da linha atual da tabela
        const saleRow = document.querySelector(`[data-sale-id="${saleId}"]`);
        if (saleRow) {
            const cells = saleRow.querySelectorAll('td');
            const customerName = cells[1].textContent.trim();
            const customerPhone = cells[2].textContent.trim();
            const saleDate = cells[3].textContent.trim();
            const itemsCount = cells[4].textContent.trim();
            const paymentMethod = cells[5].textContent.trim();
            const totalAmount = cells[6].textContent.trim();
            const seller = cells[7].textContent.trim();
            
            content.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-primary">
                                    <i class="fas fa-user me-2"></i>Informações do Cliente
                                </h6>
                                <div class="mb-2">
                                    <strong>Nome:</strong> ${customerName || 'Cliente Avulso'}
                                </div>
                                <div class="mb-2">
                                    <strong>Telefone:</strong> ${customerPhone !== '-' ? customerPhone : 'Não informado'}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-success">
                                    <i class="fas fa-shopping-cart me-2"></i>Detalhes da Venda
                                </h6>
                                <div class="mb-2">
                                    <strong>Data:</strong> ${saleDate}
                                </div>
                                <div class="mb-2">
                                    <strong>Total de Itens:</strong> ${itemsCount}
                                </div>
                                <div class="mb-2">
                                    <strong>Vendedor:</strong> ${seller || 'N/A'}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-info">
                                    <i class="fas fa-credit-card me-2"></i>Informações de Pagamento
                                </h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Método:</strong> ${paymentMethod}
                                    </div>
                                    <div class="text-end">
                                        <div class="fs-4 fw-bold text-success">${totalAmount}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Informação:</strong> Para ver os itens detalhados da venda, clique em "Ver Completo" abaixo.
                        </div>
                    </div>
                </div>
            `;
        } else {
            content.innerHTML = `
                <div class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                    <p>Erro ao carregar detalhes da venda.</p>
                    <button class="btn btn-outline-primary" onclick="location.href='/sales/${saleId}'">
                        Ver página completa
                    </button>
                </div>
            `;
        }
    }, 500);
}

// Função para exportar vendas
function exportSales() {
    showToast('Funcionalidade de exportação em desenvolvimento', 'info');
}

// Função para imprimir lista
function printList() {
    window.print();
}

// Auto-hide success messages
setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
        if (bsAlert) {
            setTimeout(() => bsAlert.close(), 5000);
        }
    });
}, 100);
</script>
@endpush

@push('styles')
<style>
.stats-card {
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.sale-row:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    color: #6c757d;
    border-bottom: 2px solid #dee2e6;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.4rem;
    font-size: 0.75rem;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f3f4f6;
    border-top: 3px solid #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.badge {
    font-size: 0.7rem;
    font-weight: 500;
}

@media print {
    .btn-group, .card-header .dropdown, .breadcrumb {
        display: none !important;
    }
}

.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}

/* Fix para problemas do modal com backdrop */
.modal-backdrop {
    z-index: 1050 !important;
}

.modal {
    z-index: 1055 !important;
}

.modal-dialog {
    z-index: 1056 !important;
}

/* Garantir que o modal seja clicável */
.modal-content {
    position: relative;
    z-index: 1057 !important;
}

/* Fix para o backdrop não interferir */
.modal.show {
    padding-right: 0 !important;
}
</style>
@endpush