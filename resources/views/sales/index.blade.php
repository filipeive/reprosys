{{-- Atualização para o arquivo sales/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Gestão de Vendas')
@section('page-title', 'Gestão de Vendas')

@section('content')
    <!-- Statistics Cards Atualizados -->
    <div class="row g-3 mb-4">
    <div class="col-md-2 col-lg-2">
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
    </div>

    <div class="col-md-2 col-lg-2">
        <div class="stat-card success">
            <div class="stat-icon success">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($sales->sum('total_amount'), 0, ',', '.') }} MT</div>
                <div class="stat-label">Receita Real</div>
                <div class="stat-change positive">
                    <i class="fas fa-chart-line me-1"></i>após descontos
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-2 col-lg-2">
        <div class="stat-card info">
            <div class="stat-icon info">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($sales->sum('subtotal'), 0, ',', '.') }} MT</div>
                <div class="stat-label">Receita Potencial</div>
                <div class="stat-change neutral">
                    <i class="fas fa-tag me-1"></i>sem descontos
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-2 col-lg-2">
        <div class="stat-card warning">
            <div class="stat-icon warning">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($sales->sum('discount_amount'), 0, ',', '.') }} MT</div>
                <div class="stat-label">Total Descontos</div>
                <div class="stat-change negative">
                    <i class="fas fa-minus me-1"></i>em descontos dados
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-2 col-lg-2">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $sales->where('discount_amount', '>', 0)->count() }}</div>
                <div class="stat-label">Vendas c/ Desconto</div>
                <div class="stat-change info">
                    <i class="fas fa-tags me-1"></i>{{ $sales->count() > 0 ? number_format(($sales->where('discount_amount', '>', 0)->count() / $sales->count()) * 100, 1) : 0 }}%
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-2 col-lg-2">
        <div class="stat-card action-card d-flex flex-column justify-content-between">
            <div class="stat-content">
                <div class="stat-label mb-2 fw-semibold text-primary">
                   <i class="fas fa-plus-circle"></i> Ações Rápidas 
                </div>
                <div class="d-grid gap-2">
                    <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-cash-register me-2"></i>
                        Nova Venda (PDV)
                    </a>
                    <a href="{{ route('sales.manual-create') }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-edit me-2"></i>
                        Venda Manual
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


    <!-- Filter Card com novo filtro de desconto -->
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
                    <div class="col-md-3">
                        <label for="search" class="form-label fw-semibold">Pesquisar Cliente</label>
                        <input type="text" class="form-control" id="search" name="search"
                            value="{{ request('search') }}" placeholder="Nome ou telefone...">
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
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Dinheiro</option>
                            <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Cartão</option>
                            <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transferência</option>
                            <option value="credit" {{ request('payment_method') == 'credit' ? 'selected' : '' }}>Crédito</option>
                        </select>
                    </div>

                    <!-- Novo filtro de desconto -->
                    <div class="col-md-2">
                        <label for="has_discount" class="form-label fw-semibold">Desconto</label>
                        <select class="form-select" id="has_discount" name="has_discount">
                            <option value="">Todas</option>
                            <option value="1" {{ request('has_discount') == '1' ? 'selected' : '' }}>Com desconto</option>
                            <option value="0" {{ request('has_discount') == '0' ? 'selected' : '' }}>Sem desconto</option>
                        </select>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
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

    <!-- Tabela atualizada com informações de desconto -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Data & Hora</th>
                        <th class="text-center">Itens</th>
                        <th>Pagamento</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">Desconto</th>
                        <th class="text-end">Total Final</th>
                        <th>Vendedor</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr class="sale-row {{ $sale->hasDiscount() ? 'has-discount' : '' }}" data-sale-id="{{ $sale->id }}">
                            <td>
                                <span class="fw-bold text-primary">#{{ $sale->id }}</span>
                                @if($sale->hasDiscount())
                                    <i class="fas fa-tag text-warning ms-1" title="Venda com desconto"></i>
                                @endif
                            </td>

                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $sale->customer_name ?: 'Cliente Avulso' }}</div>
                                    @if ($sale->customer_phone)
                                        <small class="text-muted">{{ $sale->customer_phone }}</small>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $sale->sale_date->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $sale->sale_date->format('H:i') }}</small>
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="badge badge-secondary">{{ $sale->items->count() ?? 0 }}</span>
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
                                        <span class="badge badge-info">
                                            <i class="fas fa-exchange-alt me-1"></i>Transferência
                                        </span>
                                    @break
                                    @case('credit')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock me-1"></i>Crédito
                                        </span>
                                    @break
                                @endswitch
                            </td>

                            <td class="text-end">
                                <span class="text-muted small">
                                    {{ number_format($sale->subtotal, 2, ',', '.') }} MT
                                </span>
                            </td>

                            <td class="text-end">
                                @if($sale->discount_amount > 0)
                                    <span class="text-danger fw-bold">
                                        -{{ number_format($sale->discount_amount, 2, ',', '.') }} MT
                                    </span>
                                    <div class="small text-muted">
                                        ({{ number_format($sale->getTotalDiscountPercentage(), 1) }}%)
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
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
                                    {{-- <button type="button" class="btn btn-outline-info"
                                        onclick="quickView({{ $sale->id }})" title="Visualização Rápida">
                                        <i class="fas fa-eye"></i>
                                    </button> --}}

                                    <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-outline-primary"
                                        title="Ver Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($sale->hasDiscount())
                                        <button type="button" class="btn btn-outline-warning btn-sm"
                                            onclick="showDiscountDetails({{ $sale->id }})" 
                                            title="Detalhes do Desconto">
                                            <i class="fas fa-percentage"></i>
                                        </button>
                                    @endif

                                    @if (userCan('edit_sales'))
                                        <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-outline-warning"
                                            title="Editar Venda">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    @if (userCan('delete_sales') || $sale->user_id == auth()->id())
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
                                <td colspan="10" class="text-center py-5">
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
        </div>
    </div>

    <!-- Modal para Detalhes de Desconto -->
    <div class="modal fade" id="discountDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="fas fa-percentage me-2"></i>
                        Detalhes do Desconto - Venda #<span id="discount-sale-id"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="discount-details-content">
                    <!-- Conteúdo será carregado via JavaScript -->
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
    .has-discount {
        border-left: 3px solid #ffc107;
        background: rgba(255, 193, 7, 0.05);
    }

    .discount-badge {
        font-size: 0.7em;
        padding: 2px 6px;
    }
</style>
@endpush

@push('scripts')
<script>
    // Função atualizada para Quick View com informações de desconto
    function generateQuickViewContent(sale) {
        let itemsHtml = '';
        if (sale.items && sale.items.length > 0) {
            itemsHtml = sale.items.map(item => {
                const hasItemDiscount = item.discount_amount > 0;
                const discountInfo = hasItemDiscount ? 
                    `<div class="text-warning small">
                        <i class="fas fa-tag me-1"></i>
                        Desconto: ${parseFloat(item.discount_amount).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                        ${item.discount_percentage ? `(${parseFloat(item.discount_percentage).toFixed(1)}%)` : ''}
                    </div>` : '';
                
                return `
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom ${hasItemDiscount ? 'bg-light' : ''}">
                        <div>
                            <div class="fw-semibold">${item.product_name}</div>
                            <small class="text-muted">
                                Categoria: ${item.category} | 
                                Tipo: ${item.type === 'product' ? 'Produto' : 'Serviço'} | 
                                Qtd: ${item.quantity}
                            </small>
                            ${hasItemDiscount ? `
                                <div class="small">
                                    <span class="text-muted text-decoration-line-through">
                                        ${parseFloat(item.original_unit_price).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                                    </span>
                                    <span class="text-success ms-1">
                                        ${parseFloat(item.unit_price).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                                    </span>
                                </div>
                            ` : ''}
                            ${discountInfo}
                        </div>
                        <div class="text-end">
                            <div class="fw-semibold">
                                ${parseFloat(item.total_price || 0).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                            </div>
                            ${hasItemDiscount ? `
                                <small class="text-success">
                                    Economia: ${parseFloat(item.savings).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                                </small>
                            ` : ''}
                        </div>
                    </div>
                `;
            }).join('');
        }

        const hasSaleDiscount = parseFloat(sale.discount_amount) > 0;
        const discountSection = hasSaleDiscount ? `
            <div class="alert alert-warning">
                <h6 class="alert-heading">
                    <i class="fas fa-percentage me-2"></i>Resumo dos Descontos
                </h6>
                <div class="row">
                    <div class="col-6">
                        <strong>Subtotal (sem desconto):</strong><br>
                        <span class="text-muted">${parseFloat(sale.subtotal).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT</span>
                    </div>
                    <div class="col-6">
                        <strong>Desconto Total:</strong><br>
                        <span class="text-danger">-${parseFloat(sale.discount_amount).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT</span>
                    </div>
                    <div class="col-6 mt-2">
                        <strong>Percentual de Desconto:</strong><br>
                        <span class="text-warning">${parseFloat(sale.discount_percentage || 0).toFixed(1)}%</span>
                    </div>
                    <div class="col-6 mt-2">
                        <strong>Total Final:</strong><br>
                        <span class="text-success fw-bold">${parseFloat(sale.total_amount).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT</span>
                    </div>
                </div>
                ${sale.discount_reason ? `
                    <div class="mt-2">
                        <strong>Motivo do Desconto:</strong><br>
                        <em>${sale.discount_reason}</em>
                    </div>
                ` : ''}
            </div>
        ` : '';

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
                    <strong>Status:</strong><br>
                    <small>${sale.has_discount ? '<span class="badge bg-warning">Com Desconto</span>' : '<span class="badge bg-success">Sem Desconto</span>'}</small>
                </div>
            </div>
        </div>

        ${discountSection}

        <div class="mb-4">
            <h6 class="text-info mb-3">
                <i class="fas fa-shopping-cart me-2"></i>Itens da Venda
            </h6>
            <div class="border rounded">
                ${itemsHtml}
            </div>
        </div>
    `;
    }

    // Função para mostrar detalhes específicos do desconto
    function showDiscountDetails(saleId) {
        fetch(`/sales/api/sales/${saleId}/quick-view`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('Erro ao carregar detalhes do desconto: ' + data.error);
                    return;
                }

                document.getElementById('discount-sale-id').textContent = saleId;
                
                const discountContent = generateDiscountDetailsContent(data);
                document.getElementById('discount-details-content').innerHTML = discountContent;

                const modal = new bootstrap.Modal(document.getElementById('discountDetailsModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao carregar detalhes do desconto.');
            });
    }

    // Gerar conteúdo detalhado do desconto
    function generateDiscountDetailsContent(sale) {
        let itemDiscountDetails = '';
        
        if (sale.items && sale.items.length > 0) {
            const itemsWithDiscount = sale.items.filter(item => item.discount_amount > 0);
            
            if (itemsWithDiscount.length > 0) {
                itemDiscountDetails = `
                    <div class="mb-4">
                        <h6 class="text-warning">
                            <i class="fas fa-tags me-2"></i>Descontos por Item
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Qtd</th>
                                        <th>Preço Original</th>
                                        <th>Preço Final</th>
                                        <th>Desconto</th>
                                        <th>% Desc.</th>
                                        <th>Economia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${itemsWithDiscount.map(item => `
                                        <tr>
                                            <td>${item.product_name}</td>
                                            <td>${item.quantity}</td>
                                            <td class="text-muted text-decoration-line-through">
                                                ${parseFloat(item.original_unit_price).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                                            </td>
                                            <td class="text-success">
                                                ${parseFloat(item.unit_price).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                                            </td>
                                            <td class="text-danger">
                                                -${parseFloat(item.discount_amount).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                                            </td>
                                            <td class="text-warning">
                                                ${parseFloat(item.discount_percentage || 0).toFixed(1)}%
                                            </td>
                                            <td class="text-success fw-bold">
                                                ${parseFloat(item.savings).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            }
        }

        const saleDiscountDetails = parseFloat(sale.discount_amount) > 0 ? `
            <div class="mb-4">
                <h6 class="text-primary">
                    <i class="fas fa-calculator me-2"></i>Resumo Financeiro
                </h6>
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="text-muted small">Subtotal Original</div>
                                <div class="fs-6 fw-bold">${parseFloat(sale.subtotal).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-danger small">Total Descontos</div>
                                <div class="fs-6 fw-bold text-danger">-${parseFloat(sale.discount_amount).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-warning small">% de Desconto</div>
                                <div class="fs-6 fw-bold text-warning">${parseFloat(sale.discount_percentage || 0).toFixed(1)}%</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-success small">Total Final</div>
                                <div class="fs-5 fw-bold text-success">${parseFloat(sale.total_amount).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT</div>
                            </div>
                        </div>
                    </div>
                </div>
                ${sale.discount_reason ? `
                    <div class="mt-3">
                        <div class="alert alert-info">
                            <strong>Motivo do Desconto:</strong><br>
                            ${sale.discount_reason}
                        </div>
                    </div>
                ` : ''}
            </div>
        ` : '';

        return itemDiscountDetails + saleDiscountDetails;
    }

    // Funcões existentes continuam...
    function quickView(saleId) {
        const offcanvas = new bootstrap.Offcanvas(document.getElementById('quickViewOffcanvas'));
        const content = document.getElementById('quick-view-content');
        const saleNumber = document.getElementById('sale-number');
        const viewFullLink = document.getElementById('view-full-sale');

        saleNumber.textContent = saleId;
        viewFullLink.href = `/sales/${saleId}`;

        content.innerHTML = `
            <div class="text-center py-5">
                <div class="loading"></div>
                <p class="text-muted mt-3">Carregando detalhes...</p>
            </div>
        `;

        offcanvas.show();

        fetch(`/sales/api/sales/${saleId}/quick-view`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    content.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>${data.error}
                        </div>
                    `;
                } else {
                    content.innerHTML = generateQuickViewContent(data);
                }
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

    function getPaymentMethodName(method) {
        const methods = {
            'cash': 'Dinheiro',
            'card': 'Cartão',
            'transfer': 'Transferência',
            'credit': 'Crédito'
        };
        return methods[method] || method;
    }

    function confirmDelete(saleId, customerName) {
        if (confirm(
                `Deseja realmente cancelar a venda do cliente "${customerName}"?\n\nEsta ação irá restaurar o estoque dos produtos vendidos.`
                )) {
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

    // Auto-submit form on input change
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('#date_from, #date_to, #payment_method, #has_discount');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
        });
    });
</script>
@endpush