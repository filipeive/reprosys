@extends('layouts.app')

@section('title', 'Gestão de Vendas')
@section('page-title', 'Gestão de Vendas')

@php
    $titleIcon = 'fas fa-shopping-cart';
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item active">Vendas</li>
@endsection

@section('content')
    <!-- Professional Page Header - Alinhado com o design principal -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h2 mb-2 text-primary fw-bold d-flex align-items-center">
                <i class="{{ $titleIcon }} me-3"></i>
                Gestão de Vendas
            </h1>
            <p class="text-muted mb-0 fs-6">Controle completo das vendas da sua reprografia</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('sales.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nova Venda
            </a>
            <a href="{{ route('sales.manual-create') ?? '#' }}" class="btn btn-success">
                <i class="fas fa-edit me-2"></i>Venda Manual
            </a>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-2"></i>Exportar
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportSales('excel')"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportSales('pdf')"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportSales('csv')"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Professional Statistics Cards - Seguindo padrão do layout -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($sales->total()) }}</div>
                <div class="stat-label">Total de Vendas</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up me-1"></i>vendas registradas
                </div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-icon success">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($sales->sum('total_amount'), 0, ',', '.') }} MT</div>
                <div class="stat-label">Valor Total</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up me-1"></i>em vendas
                </div>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-icon warning">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $sales->where('sale_date', '>=', now()->startOfDay())->count() }}</div>
                <div class="stat-label">Vendas Hoje</div>
                <div class="stat-change positive">
                    <i class="fas fa-clock me-1"></i>vendas de hoje
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $sales->count() > 0 ? number_format($sales->avg('total_amount'), 0, ',', '.') : '0' }} MT</div>
                <div class="stat-label">Ticket Médio</div>
                <div class="stat-change positive">
                    <i class="fas fa-trending-up me-1"></i>por venda
                </div>
            </div>
        </div>
    </div>

    <!-- Professional Filter Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filtros de Pesquisa
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('sales.index') }}" id="filter-form">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label fw-semibold">Pesquisar Cliente</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" 
                                placeholder="Nome ou telefone do cliente...">
                            @if (request('search'))
                                <button class="btn btn-outline-secondary" type="button" id="clear-search"
                                    title="Limpar pesquisa" onclick="clearSearch()">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label for="date_from" class="form-label fw-semibold">Data Inicial</label>
                        <input type="date" class="form-control" id="date_from" name="date_from"
                            value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="date_to" class="form-label fw-semibold">Data Final</label>
                        <input type="date" class="form-control" id="date_to" name="date_to"
                            value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="payment_method" class="form-label fw-semibold">Pagamento</label>
                        <select class="form-select" id="payment_method" name="payment_method">
                            <option value="">Todos</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>
                                Dinheiro
                            </option>
                            <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>
                                Cartão
                            </option>
                            <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>
                                Transferência
                            </option>
                            <option value="credit" {{ request('payment_method') == 'credit' ? 'selected' : '' }}>
                                Crédito
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Professional Table Card -->
    <div class="table-container">
        <div class="table-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Vendas Registradas
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge badge-primary">Total: {{ $sales->total() }}</span>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-cog"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" onclick="printTable()">
                                <i class="fas fa-print me-2"></i>Imprimir
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="refreshTable()">
                                <i class="fas fa-sync-alt me-2"></i>Atualizar
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-chart-bar me-2"></i>Relatório Detalhado
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 80px;">#</th>
                        <th>Cliente</th>
                        <th style="width: 140px;">Telefone</th>
                        <th style="width: 150px;">Data & Hora</th>
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
                                <div>
                                    <div class="fw-semibold">{{ $sale->customer_name ?: 'Cliente Avulso' }}</div>
                                    @if ($sale->notes)
                                        <small class="text-muted">{{ Str::limit($sale->notes, 40) }}</small>
                                    @endif
                                </div>
                            </td>

                            <td>
                                @if ($sale->customer_phone)
                                    <a href="tel:{{ $sale->customer_phone }}" class="text-decoration-none">
                                        <i class="fas fa-phone me-1 text-success"></i>{{ $sale->customer_phone }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $sale->sale_date->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $sale->sale_date->format('H:i') }}</small>
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="nav-badge badge-secondary">{{ $sale->items->count() ?? 0 }}</span>
                            </td>

                            <td>
                                @switch($sale->payment_method)
                                    @case('cash')
                                        <span class="badge badge-success">
                                            <i class="fas fa-money-bill me-1"></i>Dinheiro
                                        </span>
                                    @break
                                    @case('card')
                                        <span class="badge badge-primary">
                                            <i class="fas fa-credit-card me-1"></i>Cartão
                                        </span>
                                    @break
                                    @case('transfer')
                                        <span class="badge badge-primary">
                                            <i class="fas fa-exchange-alt me-1"></i>Transferência
                                        </span>
                                    @break
                                    @case('credit')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock me-1"></i>Crédito
                                        </span>
                                    @break
                                    @default
                                        <span class="badge badge-secondary">{{ $sale->payment_method }}</span>
                                @endswitch
                            </td>

                            <td class="text-end">
                                <span class="fw-bold text-success">
                                    {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                                </span>
                            </td>

                            <td>
                                <small class="text-muted">{{ $sale->user->name ?? 'Sistema' }}</small>
                            </td>

                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-info" 
                                        onclick="quickView({{ $sale->id }})" title="Visualização Rápida">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <a href="{{ route('sales.show', $sale->id) }}" 
                                       class="btn btn-outline-primary" title="Ver Detalhes">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>

                                    @if(userCan('edit_sales'))
                                        <a href="{{ route('sales.edit', $sale->id) }}" 
                                           class="btn btn-outline-warning" title="Editar Venda">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    @if(userCan('delete_sales') || $sale->user_id == auth()->id())
                                        <button type="button" class="btn btn-outline-danger" 
                                            onclick="confirmDelete({{ $sale->id }}, '{{ $sale->customer_name ?: 'Cliente Avulso' }}')"
                                            title="Cancelar Venda">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
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

        <!-- Professional Pagination -->
        @if ($sales->hasPages())
            <div class="card-footer bg-light border-top text-light border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-2 mb-md-0">  
                        <small class="text-dark me-3 d-none d-md-inline">
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

    <!-- Professional Offcanvas for Quick View -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="quickViewOffcanvas">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-eye me-2"></i>Detalhes da Venda #<span id="sale-number"></span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body" id="quick-view-content">
            <div class="text-center py-5">
                <div class="loading"></div>
                <p class="text-muted mt-3">Carregando detalhes...</p>
            </div>
        </div>
        <div class="offcanvas-footer border-top p-3">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i>Fechar
                </button>
                <a href="#" id="view-full-sale" class="btn btn-primary flex-fill" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>Ver Completo
                </a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .sale-row:hover {
        background-color: rgba(91, 155, 213, 0.05);
    }

    .stats-card {
        cursor: pointer;
    }

    .stats-card:hover {
        transform: translateY(-2px);
    }

    .table-container .table th {
        background: var(--content-bg);
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .offcanvas-footer {
        margin-top: auto;
    }

    .badge {
        font-size: 0.75em;
    }
</style>
@endpush

@push('scripts')
<script>
    // Quick View Function
    function quickView(saleId) {
        const offcanvas = new bootstrap.Offcanvas(document.getElementById('quickViewOffcanvas'));
        const content = document.getElementById('quick-view-content');
        const saleNumber = document.getElementById('sale-number');
        const viewFullLink = document.getElementById('view-full-sale');

        // Update data
        saleNumber.textContent = saleId;
        viewFullLink.href = `/sales/${saleId}`;

        // Show loading
        content.innerHTML = `
            <div class="text-center py-5">
                <div class="loading"></div>
                <p class="text-muted mt-3">Carregando detalhes...</p>
            </div>
        `;

        offcanvas.show();

        // Fetch sale details
        fetch(`/api/sales/${saleId}/quick-view`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            content.innerHTML = generateQuickViewContent(data);
        })
        .catch(error => {
            console.error('Erro ao carregar detalhes:', error);
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Erro ao carregar detalhes da venda.
                </div>
            `;
        });
    }

    // Generate Quick View Content
    function generateQuickViewContent(sale) {
        let itemsHtml = '';
        if (sale.items && sale.items.length > 0) {
            itemsHtml = sale.items.map(item => `
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <div class="fw-semibold">${item.product_name || item.description}</div>
                        <small class="text-muted">Qtd: ${item.quantity}</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-semibold">${parseFloat(item.total_price || 0).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT</div>
                    </div>
                </div>
            `).join('');
        }

        return `
            <div class="mb-4">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-user me-2"></i>Informações do Cliente
                </h6>
                <div class="row">
                    <div class="col-12 mb-2">
                        <strong>Nome:</strong> ${sale.customer_name || 'Cliente Avulso'}
                    </div>
                    <div class="col-12 mb-2">
                        <strong>Telefone:</strong> ${sale.customer_phone || 'Não informado'}
                    </div>
                    ${sale.notes ? `<div class="col-12"><strong>Observações:</strong> ${sale.notes}</div>` : ''}
                </div>
            </div>

            <div class="mb-4">
                <h6 class="text-success mb-3">
                    <i class="fas fa-info-circle me-2"></i>Detalhes da Venda
                </h6>
                <div class="row">
                    <div class="col-6 mb-2">
                        <strong>Data:</strong><br>
                        <small>${sale.sale_date}</small>
                    </div>
                    <div class="col-6 mb-2">
                        <strong>Vendedor:</strong><br>
                        <small>${sale.user_name || 'Sistema'}</small>
                    </div>
                    <div class="col-6 mb-2">
                        <strong>Pagamento:</strong><br>
                        <small>${getPaymentMethodName(sale.payment_method)}</small>
                    </div>
                    <div class="col-6 mb-2">
                        <strong>Total:</strong><br>
                        <span class="fw-bold text-success">${parseFloat(sale.total_amount || 0).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="text-info mb-3">
                    <i class="fas fa-shopping-cart me-2"></i>Itens da Venda
                </h6>
                <div class="border rounded">
                    ${itemsHtml || '<p class="text-muted text-center py-3">Nenhum item encontrado</p>'}
                </div>
            </div>
        `;
    }

    // Helper function
    function getPaymentMethodName(method) {
        const methods = {
            'cash': 'Dinheiro',
            'card': 'Cartão',
            'transfer': 'Transferência',
            'credit': 'Crédito'
        };
        return methods[method] || method;
    }

    // Confirm Delete Function
    function confirmDelete(saleId, customerName) {
        if (confirm(`Deseja realmente cancelar a venda do cliente "${customerName}"?\n\nEsta ação irá restaurar o estoque dos produtos vendidos.`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/sales/${saleId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Export Functions
    function exportSales(format) {
        const queryString = new URLSearchParams(window.location.search).toString();
        window.location.href = `/sales/export/${format}?${queryString}`;
    }

    // Clear Search Function
    function clearSearch() {
        document.getElementById('search').value = '';
        document.getElementById('filter-form').submit();
    }

    // Print Table Function
    function printTable() {
        window.print();
    }

    // Refresh Table Function
    function refreshTable() {
        window.location.reload();
    }

    // Auto-submit form on input change
    document.addEventListener('DOMContentLoaded', function() {
        const dateInputs = document.querySelectorAll('#date_from, #date_to, #payment_method');
        dateInputs.forEach(input => {
            input.addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
        });
    });
</script>
@endpush