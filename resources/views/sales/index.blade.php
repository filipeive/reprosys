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

    <!-- Painel Lateral Deslizante (Sidebar) para Quick View -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="quickViewOffcanvas">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-eye me-2"></i>Detalhes da Venda #<span id="sale-number-offcanvas"></span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body" id="quick-view-offcanvas-content">
            <div class="text-center py-5">
                <div class="loading-spinner mb-3"></div>
                <p class="text-muted">Carregando detalhes...</p>
            </div>
        </div>
        <div class="offcanvas-footer p-3 border-top">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i>Fechar
                </button>
                <a href="#" id="view-full-sale-offcanvas" class="btn btn-primary flex-fill" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>Ver Completo
                </a>
            </div>
        </div>
    </div>

    <!-- Dropdown/Popover para Confirmação de Exclusão -->
    <div class="position-fixed" id="delete-confirmation-container" style="display: none; z-index: 9999;">
        <div class="card shadow-lg border-0" style="width: 350px;">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Cancelamento
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-2">Deseja cancelar a venda do cliente:</p>
                <div class="alert alert-light border">
                    <strong id="delete-customer-name-popup"></strong>
                </div>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    O estoque será restaurado automaticamente.
                </small>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-secondary flex-fill" onclick="hideDeleteConfirmation()">
                        Cancelar
                    </button>
                    <form id="delete-form-popup" method="POST" style="flex: 1;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger w-100">
                            Confirmar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Área Expansível In-line para Quick View -->
    <div class="collapse mb-4" id="inlineQuickView">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-eye me-2"></i>Visualização Rápida - Venda #<span id="sale-number-inline"></span>
                </h5>
                <button class="btn btn-sm btn-outline-light" data-bs-toggle="collapse" data-bs-target="#inlineQuickView">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="card-body" id="quick-view-inline-content">
                <div class="text-center py-4">
                    <div class="loading-spinner mb-3"></div>
                    <p class="text-muted">Carregando informações...</p>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <button class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#inlineQuickView">
                        <i class="fas fa-times me-2"></i>Fechar
                    </button>
                    <a href="#" id="view-full-sale-inline" class="btn btn-primary" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Completo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast para Confirmação de Exclusão -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
        <div id="deleteToast" class="toast hide" role="alert">
            <div class="toast-header bg-warning text-dark">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong class="me-auto">Confirmação Necessária</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                <p class="mb-2">Cancelar venda do cliente: <strong id="delete-customer-toast"></strong>?</p>
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-sm btn-secondary" data-bs-dismiss="toast">Não</button>
                    <form id="delete-form-toast" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Sim, Cancelar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
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

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Valor Total</h6>
                            <h3 class="mb-0 text-success fw-bold">
                                {{ number_format($sales->sum('total_amount'), 2, ',', '.') }} MT</h3>
                            <small class="text-muted">em vendas</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Vendas Hoje</h6>
                            <h3 class="mb-0 text-warning fw-bold">
                                {{ $sales->where('sale_date', now()->toDateString())->count() }}</h3>
                            <small class="text-muted">vendas de hoje</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Média por Venda</h6>
                            <h3 class="mb-0 text-info fw-bold">
                                {{ $sales->count() > 0 ? number_format($sales->avg('total_amount'), 2, ',', '.') : '0,00' }}
                                MT</h3>
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
                                value="{{ request('search') }}" placeholder="Nome ou telefone do cliente...">
                            @if (request('search'))
                                <button class="btn btn-outline-secondary" type="button" id="clear-search"
                                    title="Limpar pesquisa">
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
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-cog"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" onclick="exportSales()"><i
                                        class="fas fa-download me-2"></i>Exportar Lista</a></li>
                            <li><a class="dropdown-item" href="#" onclick="printList()"><i
                                        class="fas fa-print me-2"></i>Imprimir Lista</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="{{ route('sales.report') }}"><i
                                        class="fas fa-chart-bar me-2"></i>Relatório Detalhado</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
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
                                        @if ($sale->notes)
                                            <small class="text-muted">{{ Str::limit($sale->notes, 35) }}</small>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    @if ($sale->customer_phone)
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
                                        <!-- Botão para Offcanvas -->
                                        <button type="button" class="btn btn-outline-info"
                                            onclick="quickViewOffcanvas({{ $sale->id }})"
                                            title="Visualização Lateral">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <!-- Botão para Collapse Inline -->
                                        <button type="button" class="btn btn-outline-secondary"
                                            onclick="quickViewInline({{ $sale->id }})" title="Visualização Inline">
                                            <i class="fas fa-expand"></i>
                                        </button>

                                        <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-outline-primary"
                                            title="Ver Detalhes">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>

                                        <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-outline-warning"
                                            title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Botão para confirmação via Toast -->
                                        <button type="button" class="btn btn-outline-warning"
                                            onclick="confirmDeleteToast({{ $sale->id }}, '{{ $sale->customer_name ?: 'Cliente Avulso' }}')"
                                            title="Cancelar via Toast">
                                            <i class="fas fa-bell"></i>
                                        </button>

                                        <!-- Botão para confirmação via Popup -->
                                        <button type="button" class="btn btn-outline-danger"
                                            onclick="confirmDeletePopup({{ $sale->id }}, '{{ $sale->customer_name ?: 'Cliente Avulso' }}', event)"
                                            title="Cancelar Venda">
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
                @if ($sales->hasPages())
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
    @endsection

    @push('scripts')
        <script>
            // ===== OFFCANVAS QUICK VIEW =====
            function quickViewOffcanvas(saleId) {
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('quickViewOffcanvas'));
                const content = document.getElementById('quick-view-offcanvas-content');
                const saleNumber = document.getElementById('sale-number-offcanvas');

                // Atualizar dados
                saleNumber.textContent = saleId;
                document.getElementById('view-full-sale-offcanvas').href = `/sales/${saleId}`;

                // Mostrar loading
                content.innerHTML = `
        <div class="text-center py-5">
            <div class="loading-spinner mb-3"></div>
            <p class="text-muted">Carregando detalhes...</p>
        </div>
    `;

                offcanvas.show();

                // Simular carregamento
                setTimeout(() => {
                    content.innerHTML = generateQuickViewContent(saleId);
                }, 800);
            }

            // ===== INLINE QUICK VIEW =====
            function quickViewInline(saleId) {
                const collapse = new bootstrap.Collapse(document.getElementById('inlineQuickView'), {
                    show: true
                });
                const content = document.getElementById('quick-view-inline-content');
                const saleNumber = document.getElementById('sale-number-inline');

                // Atualizar dados
                saleNumber.textContent = saleId;
                document.getElementById('view-full-sale-inline').href = `/sales/${saleId}`;

                // Mostrar loading
                content.innerHTML = `
        <div class="text-center py-4">
            <div class="loading-spinner mb-3"></div>
            <p class="text-muted">Carregando informações...</p>
        </div>
    `;

                // Scroll suave para o elemento
                setTimeout(() => {
                    document.getElementById('inlineQuickView').scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }, 100);

                // Simular carregamento
                setTimeout(() => {
                    content.innerHTML = generateQuickViewContent(saleId);
                }, 800);
            }

            // ===== POPUP CONFIRMATION =====
            function confirmDeletePopup(saleId, customerName, event) {
                const container = document.getElementById('delete-confirmation-container');
                const customerElement = document.getElementById('delete-customer-name-popup');
                const form = document.getElementById('delete-form-popup');

                // Posicionar próximo ao botão clicado
                const rect = event.target.getBoundingClientRect();
                container.style.position = 'fixed';
                container.style.left = Math.min(rect.left - 200, window.innerWidth - 370) + 'px';
                container.style.top = (rect.top - 10) + 'px';
                container.style.display = 'block';

                // Configurar dados
                customerElement.textContent = customerName;
                form.action = `/sales/${saleId}`;

                // Fechar ao clicar fora
                setTimeout(() => {
                    document.addEventListener('click', hideDeleteConfirmationOnOutsideClick);
                }, 100);
            }

            function hideDeleteConfirmation() {
                document.getElementById('delete-confirmation-container').style.display = 'none';
                document.removeEventListener('click', hideDeleteConfirmationOnOutsideClick);
            }

            function hideDeleteConfirmationOnOutsideClick(event) {
                const container = document.getElementById('delete-confirmation-container');
                if (!container.contains(event.target)) {
                    hideDeleteConfirmation();
                }
            }

            // ===== TOAST CONFIRMATION =====
            function confirmDeleteToast(saleId, customerName) {
                const toast = new bootstrap.Toast(document.getElementById('deleteToast'));

                document.getElementById('delete-customer-toast').textContent = customerName;
                document.getElementById('delete-form-toast').action = `/sales/${saleId}`;

                toast.show();
            }

            // ===== HELPER FUNCTION =====
            function generateQuickViewContent(saleId) {
                const saleRow = document.querySelector(`[data-sale-id="${saleId}"]`);
                if (!saleRow) {
                    return `
            <div class="text-center py-4 text-danger">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <p>Erro ao carregar detalhes da venda.</p>
            </div>
        `;
                }

                const cells = saleRow.querySelectorAll('td');
                const customerName = cells[1].textContent.trim();
                const customerPhone = cells[2].textContent.trim();
                const saleDate = cells[3].textContent.trim();
                const itemsCount = cells[4].textContent.trim();
                const paymentMethod = cells[5].textContent.trim();
                const totalAmount = cells[6].textContent.trim();
                const seller = cells[7].textContent.trim();

                return `
        <div class="row g-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <h6 class="alert-heading">
                        <i class="fas fa-user me-2"></i>Cliente
                    </h6>
                    <p class="mb-1"><strong>Nome:</strong> ${customerName || 'Cliente Avulso'}</p>
                    <p class="mb-0"><strong>Telefone:</strong> ${customerPhone !== '-' ? customerPhone : 'Não informado'}</p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title text-success">
                            <i class="fas fa-shopping-cart me-2"></i>Venda
                        </h6>
                        <p class="card-text small mb-1"><strong>Data:</strong> ${saleDate}</p>
                        <p class="card-text small mb-1"><strong>Itens:</strong> ${itemsCount}</p>
                        <p class="card-text small mb-0"><strong>Vendedor:</strong> ${seller || 'N/A'}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title text-primary">
                            <i class="fas fa-credit-card me-2"></i>Pagamento
                        </h6>
                        <p class="card-text small mb-1"><strong>Método:</strong> ${paymentMethod}</p>
                        <p class="card-text"><strong class="text-success fs-5">${totalAmount}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    `;
            }

            // Função para exportar vendas
            function exportSales() {
                if (window.showToast) {
                    showToast('Funcionalidade de exportação em desenvolvimento', 'info');
                }
            }

            // Função para imprimir lista
            function printList() {
                window.print();
            }
        </script>
    @endpush

    @push('styles')
        <style>
            .stats-card {
                transition: all 0.3s ease;
                border-left: 4px solid transparent;
            }

            .stats-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
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

            .stats-card.info {
                border-left-color: #0891b2;
            }

            .sale-row:hover {
                background-color: rgba(13, 110, 253, 0.05);
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
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            .offcanvas-footer {
                margin-top: auto;
            }

            #delete-confirmation-container {
                max-width: 350px;
                animation: slideIn 0.3s ease-out;
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
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
        </style>
    @endpush
