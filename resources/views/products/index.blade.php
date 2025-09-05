@extends('layouts.app')

@section('title', 'Produtos')
@section('page-title', 'Produtos e Serviços')
@section('title-icon', 'fa-box')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Produtos</li>
@endsection

@section('content')
    <!-- Header com botões de ação -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-box me-2"></i>
                Produtos e Serviços
            </h2>
            <p class="text-muted mb-0">Gerencie seus produtos e serviços da reprografia</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-action="create">
                <i class="fas fa-plus me-2"></i> Novo Produto
            </button>
            <a href="{{ route('categories.index') }}" class="btn btn-success">
                <i class="fas fa-tags me-2"></i> Categorias
            </a>
            <button class="btn btn-outline-primary" onclick="exportProducts()">
                <i class="fas fa-download me-2"></i> Exportar
            </button>
        </div>
    </div>

    <!-- Painel Lateral para Criar/Editar Produto -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="productFormOffcanvas" style="width: 600px;">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-box me-2"></i><span id="form-title">Novo Produto</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="product-form" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <input type="hidden" name="product_id" id="product-id">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome do Produto/Serviço *</label>
                    <input type="text" class="form-control" name="name" id="product-name" required>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Categoria *</label>
                            <select class="form-select" name="category_id" id="product-category" required>
                                <option value="">Selecione uma categoria</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipo *</label>
                            <select class="form-select" name="type" id="product-type" required>
                                <option value="">Selecione o tipo</option>
                                <option value="product">Produto</option>
                                <option value="service">Serviço</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Descrição</label>
                    <textarea class="form-control" name="description" id="product-description" rows="3"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Preço de Venda *</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="selling_price" id="selling-price"
                                    step="0.01" min="0" required>
                                <span class="input-group-text">MT</span>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Preço de Compra</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="purchase_price" id="purchase-price"
                                    step="0.01" min="0">
                                <span class="input-group-text">MT</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campos específicos para produtos -->
                <div id="product-specific-fields" style="display: none;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Unidade</label>
                                <input type="text" class="form-control" name="unit" id="product-unit"
                                    placeholder="unid, kg, m...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Estoque Inicial</label>
                                <input type="number" class="form-control" name="stock_quantity" id="stock-quantity"
                                    min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Estoque Mínimo</label>
                                <input type="number" class="form-control" name="min_stock_level" id="min-stock-level"
                                    min="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="product-active"
                            value="1" checked>
                        <label class="form-check-label fw-semibold" for="product-active">
                            Produto Ativo
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="offcanvas-footer p-3 border-top">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="submit" form="product-form" class="btn btn-primary flex-fill" id="save-product-btn">
                    <i class="fas fa-save me-2"></i>Salvar
                </button>
            </div>
        </div>
    </div>

    <!-- Painel Lateral para Quick View -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="quickViewOffcanvas">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-eye me-2"></i>Detalhes do Produto #<span id="product-id-offcanvas"></span>
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
                <a href="#" id="view-full-product-offcanvas" class="btn btn-primary flex-fill" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>Ver Completo
                </a>
            </div>
        </div>
    </div>

    <!-- Área Expansível In-line para Quick View -->
    <div class="collapse mb-4" id="inlineQuickView">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-eye me-2"></i>Visualização Rápida - Produto #<span id="product-id-inline"></span>
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
                    <a href="#" id="view-full-product-inline" class="btn btn-primary" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Completo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Offcanvas para Ajuste de Estoque -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="stockAdjustOffcanvas" style="width: 500px;">
        <div class="offcanvas-header bg-warning text-dark">
            <h5 class="offcanvas-title">
                <i class="fas fa-cubes me-2"></i>Ajustar Estoque
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="stock-adjust-form">
                @csrf
                <input type="hidden" id="stock-product-id">
                <div class="alert alert-info">
                    <strong>Produto:</strong> <span id="stock-product-name"></span><br>
                    <strong>Estoque Atual:</strong> <span id="current-stock"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tipo de Ajuste</label>
                    <select class="form-select" id="adjustment-type" required>
                        <option value="">Selecione</option>
                        <option value="increase">Entrada (+)</option>
                        <option value="decrease">Saída (-)</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Quantidade</label>
                    <input type="number" class="form-control" id="adjustment-quantity" min="1" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Motivo</label>
                    <input type="text" class="form-control" id="adjustment-reason" maxlength="200" required>
                    <div class="invalid-feedback"></div>
                </div>
            </form>
        </div>
        <div class="offcanvas-footer p-3 border-top">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="submit" form="stock-adjust-form" class="btn btn-warning flex-fill">
                    <i class="fas fa-save me-2"></i>Ajustar Estoque
                </button>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Total de Produtos</h6>
                            <h3 class="mb-0 text-primary fw-bold">{{ $products->where('type', 'product')->count() }}</h3>
                            <small class="text-muted">produtos ativos</small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-box fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Total de Serviços</h6>
                            <h3 class="mb-0 text-success fw-bold">{{ $products->where('type', 'service')->count() }}</h3>
                            <small class="text-muted">serviços disponíveis</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-concierge-bell fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Estoque Baixo</h6>
                            <h3 class="mb-0 text-warning fw-bold">{{ $lowStockCount ?? 0 }}</h3>
                            <small class="text-muted">produtos em falta</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Categorias</h6>
                            <h3 class="mb-0 text-info fw-bold">{{ $categories->count() ?? 0 }}</h3>
                            <small class="text-muted">categorias ativas</small>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4 fade-in">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filtros e Pesquisa
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}" id="filters-form">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Pesquisar Produto</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                placeholder="Nome do produto ou serviço...">
                            <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select class="form-select" name="type">
                            <option value="">Todos</option>
                            <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>Produto</option>
                            <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>Serviço</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Categoria</label>
                        <select class="form-select" name="category_id">
                            <option value="">Todas</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i>Limpar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Produtos -->
    <div class="card fade-in">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Lista de Produtos e Serviços
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary">Total: {{ $products->total() }}</span>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-cog"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" onclick="exportProducts()"><i
                                        class="fas fa-download me-2"></i>Exportar Lista</a></li>
                            <li><a class="dropdown-item" href="#" onclick="printList()"><i
                                        class="fas fa-print me-2"></i>Imprimir Lista</a></li>
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
                            <th>Produto/Serviço</th>
                            <th style="width: 120px;">Categoria</th>
                            <th style="width: 100px;">Tipo</th>
                            <th style="width: 120px;">Preço</th>
                            <th style="width: 120px;">Estoque</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 280px;" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr class="product-row" data-product-id="{{ $product->id }}">
                                <td>
                                    <span class="fw-bold text-primary">#{{ $product->id }}</span>
                                </td>

                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">{{ $product->name }}</span>
                                        @if ($product->description)
                                            <small class="text-muted">{{ Str::limit($product->description, 40) }}</small>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark">{{ $product->category->name ?? 'N/A' }}</span>
                                </td>

                                <td>
                                    @if ($product->type === 'product')
                                        <span class="badge bg-primary">
                                            <i class="fas fa-box me-1"></i>Produto
                                        </span>
                                    @else
                                        <span class="badge bg-info">
                                            <i class="fas fa-concierge-bell me-1"></i>Serviço
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <span class="fw-bold text-success fs-6">
                                        {{ number_format($product->selling_price, 2, ',', '.') }} MT
                                    </span>
                                </td>

                                <td class="text-center">
                                    @if ($product->type === 'product')
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold">{{ $product->stock_quantity }}
                                                {{ $product->unit }}</span>
                                            @if ($product->isLowStock())
                                                <span class="badge bg-warning">Baixo</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($product->is_active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Ativo
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-times me-1"></i>Inativo
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- Quick View Offcanvas -->
                                        <button type="button" class="btn btn-outline-info"
                                            data-action="quick-view-offcanvas" data-product-id="{{ $product->id }}"
                                            title="Visualização Lateral">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <!-- Quick View Inline -->
                                        <button type="button" class="btn btn-outline-secondary"
                                            data-action="quick-view-inline" data-product-id="{{ $product->id }}"
                                            title="Visualização Inline">
                                            <i class="fas fa-expand"></i>
                                        </button>

                                        <!-- Ver Detalhes Completos -->
                                        <a href="{{ route('products.show', $product->id) }}"
                                            class="btn btn-outline-primary" title="Ver Detalhes">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>

                                        <!-- Editar -->
                                        <button type="button" class="btn btn-outline-warning" data-action="edit"
                                            data-product-id="{{ $product->id }}" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Ajustar Estoque -->
                                        @if ($product->type === 'product')
                                            <button type="button" class="btn btn-outline-success"
                                                data-action="adjust-stock" data-product-id="{{ $product->id }}"
                                                title="Ajustar Estoque">
                                                <i class="fas fa-cubes"></i>
                                            </button>
                                        @endif

                                        <!-- Excluir -->
                                        @can('delete_products')
                                            <button type="button" class="btn btn-outline-danger" data-action="delete"
                                                data-product-id="{{ $product->id }}"
                                                data-product-name="{{ $product->name }}" title="Excluir Produto">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3 opacity-50"></i>
                                        <h5>Nenhum produto encontrado</h5>
                                        <p class="mb-3">Não há produtos que correspondam aos filtros aplicados.</p>
                                        @can('create_products')
                                            <button class="btn btn-primary" data-action="create">
                                                <i class="fas fa-plus me-2"></i>Adicionar Primeiro Produto
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if ($products->hasPages())
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Mostrando {{ $products->firstItem() }} a {{ $products->lastItem() }}
                            de {{ $products->total() }} produtos
                        </div>
                        {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Meta tag do CSRF token (certifique-se que existe no layout)
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }
    </script>

    <!-- Incluir o script da classe ProductManager -->
    <script src="{{ asset('js/product-manager.js') }}"></script>

    {{-- Ou se preferir inline: --}}
    <script>
        // ===== SISTEMA DE PRODUTOS - AJAX REFATORADO =====

        class ProductManager {
            constructor() {
                this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                this.apiEndpoints = {
                    store: '/products',
                    update: (id) => `/products/${id}`,
                    destroy: (id) => `/products/${id}`,
                    editData: (id) => `/products/${id}/edit-data`,
                    adjustStock: (id) => `/products/${id}/adjust-stock`,
                    categories: '/products/api/categories'
                };
                this.currentProductId = null;
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.setupFormHandlers();
                this.setupTypeToggle();
                this.initializeToastContainer();
            }

            // ===== CONFIGURAÇÃO DE EVENTOS =====
            setupEventListeners() {
                // Auto-submit de filtros
                const filterForm = document.getElementById('filters-form');
                if (filterForm) {
                    const selects = filterForm.querySelectorAll('select');
                    selects.forEach(select => {
                        select.addEventListener('change', () => filterForm.submit());
                    });
                }

                // Eventos globais
                document.addEventListener('click', (e) => {
                    this.handleGlobalClicks(e);
                });
            }

            setupFormHandlers() {
                const productForm = document.getElementById('product-form');
                if (productForm) {
                    productForm.addEventListener('submit', (e) => this.handleFormSubmit(e));
                }

                const stockForm = document.getElementById('stock-adjust-form');
                if (stockForm) {
                    stockForm.addEventListener('submit', (e) => this.handleStockAdjust(e));
                }
            }

            setupTypeToggle() {
                const typeSelect = document.getElementById('product-type');
                if (typeSelect) {
                    typeSelect.addEventListener('change', () => {
                        this.toggleProductFields(typeSelect.value === 'product');
                    });
                }
            }

            handleGlobalClicks(e) {
                const target = e.target.closest('[data-action]');
                if (!target) return;

                const action = target.dataset.action;
                const productId = target.dataset.productId;

                switch (action) {
                    case 'create':
                        this.openCreateModal();
                        break;
                    case 'edit':
                        this.openEditModal(productId);
                        break;
                    case 'quick-view-offcanvas':
                        this.quickViewOffcanvas(productId);
                        break;
                    case 'quick-view-inline':
                        this.quickViewInline(productId);
                        break;
                    case 'adjust-stock':
                        this.openStockModal(productId);
                        break;
                    case 'delete':
                        this.confirmDelete(productId, target.dataset.productName);
                        break;
                }
            }

            // ===== MODAIS E OFFCANVAS =====
            async openCreateModal() {
                try {
                    this.resetForm();
                    this.setFormMode('create');
                    this.showOffcanvas('productFormOffcanvas');
                } catch (error) {
                    this.handleError('Erro ao abrir formulário de criação', error);
                }
            }

            async openEditModal(productId) {
                if (!productId) {
                    this.showToast('ID do produto não encontrado', 'error');
                    return;
                }

                try {
                    this.showLoadingState(true);
                    const productData = await this.fetchProductData(productId);

                    this.resetForm();
                    this.setFormMode('edit', productId);
                    this.populateForm(productData);
                    this.showOffcanvas('productFormOffcanvas');

                } catch (error) {
                    this.handleError('Erro ao carregar dados do produto', error);
                } finally {
                    this.showLoadingState(false);
                }
            }

            async openStockModal(productId) {
                try {
                    const productData = await this.fetchProductData(productId);

                    document.getElementById('stock-product-id').value = productId;
                    document.getElementById('stock-product-name').textContent = productData.name;
                    document.getElementById('current-stock').textContent = productData.stock_quantity;

                    this.resetStockForm();
                    this.showOffcanvas('stockAdjustOffcanvas');

                } catch (error) {
                    this.handleError('Erro ao abrir formulário de ajuste', error);
                }
            }

            // ===== QUICK VIEW =====
            async quickViewOffcanvas(productId) {
                const offcanvas = this.showOffcanvas('quickViewOffcanvas');
                const content = document.getElementById('quick-view-offcanvas-content');

                this.setQuickViewLoading(content);
                document.getElementById('product-id-offcanvas').textContent = productId;
                document.getElementById('view-full-product-offcanvas').href = `/products/${productId}`;

                try {
                    const productData = await this.fetchProductData(productId);
                    content.innerHTML = this.generateQuickViewContent(productData);
                } catch (error) {
                    content.innerHTML = this.getErrorContent('Erro ao carregar detalhes do produto');
                    this.handleError('Erro no Quick View', error);
                }
            }

            async quickViewInline(productId) {
                const collapse = new bootstrap.Collapse(document.getElementById('inlineQuickView'), {
                    show: true
                });
                const content = document.getElementById('quick-view-inline-content');

                this.setQuickViewLoading(content);
                document.getElementById('product-id-inline').textContent = productId;
                document.getElementById('view-full-product-inline').href = `/products/${productId}`;

                // Scroll suave
                setTimeout(() => {
                    document.getElementById('inlineQuickView').scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }, 100);

                try {
                    const productData = await this.fetchProductData(productId);
                    content.innerHTML = this.generateQuickViewContent(productData);
                } catch (error) {
                    content.innerHTML = this.getErrorContent('Erro ao carregar informações');
                    this.handleError('Erro no Quick View Inline', error);
                }
            }

            // ===== OPERAÇÕES DE DADOS =====
            async fetchProductData(productId) {
                const response = await this.makeRequest(this.apiEndpoints.editData(productId), 'GET');

                if (!response.success) {
                    throw new Error(response.message || 'Erro ao buscar dados do produto');
                }

                return response.data;
            }

            async handleFormSubmit(e) {
                e.preventDefault();

                if (!this.validateForm()) {
                    return;
                }

                const form = e.target;
                const formData = new FormData(form);
                const method = document.getElementById('form-method').value;
                const isUpdate = method === 'PUT';

                // Adicionar dados específicos
                this.addFormSpecificData(formData, method);

                const submitBtn = document.getElementById('save-product-btn') || form.querySelector(
                    'button[type="submit"]');
                this.setButtonLoading(submitBtn, true);

                try {
                    const endpoint = isUpdate ?
                        this.apiEndpoints.update(this.currentProductId) :
                        this.apiEndpoints.store;

                    const response = await this.makeRequest(endpoint, 'POST', formData);

                    if (response.success) {
                        this.hideOffcanvas('productFormOffcanvas');
                        this.showToast(response.message || 'Produto salvo com sucesso!', 'success');

                        // Recarregar página após sucesso
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        this.handleFormErrors(response.errors || {});
                        this.showToast(response.message || 'Erro ao salvar produto', 'error');
                    }

                } catch (error) {
                    this.handleError('Erro ao salvar produto', error);
                } finally {
                    this.setButtonLoading(submitBtn, false);
                }
            }

            async handleStockAdjust(e) {
                e.preventDefault();

                if (!this.validateStockForm()) {
                    return;
                }

                const formData = new FormData();
                formData.append('_token', this.csrfToken);
                formData.append('adjustment_type', document.getElementById('adjustment-type').value);
                formData.append('quantity', document.getElementById('adjustment-quantity').value);
                formData.append('reason', document.getElementById('adjustment-reason').value);

                const productId = document.getElementById('stock-product-id').value;
                const submitBtn = e.target.querySelector('button[type="submit"]');

                this.setButtonLoading(submitBtn, true);

                try {
                    const response = await this.makeRequest(
                        this.apiEndpoints.adjustStock(productId),
                        'POST',
                        formData
                    );

                    if (response.success) {
                        this.showToast(response.message || 'Estoque ajustado com sucesso!', 'success');
                        this.hideOffcanvas('stockAdjustOffcanvas');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        this.showToast(response.message || 'Erro ao ajustar estoque', 'error');
                    }

                } catch (error) {
                    this.handleError('Erro ao ajustar estoque', error);
                } finally {
                    this.setButtonLoading(submitBtn, false);
                }
            }

            async confirmDelete(productId, productName) {
                const confirmed = await this.showConfirmDialog(
                    'Confirmar Exclusão',
                    `Deseja excluir o produto "${productName}"? Esta ação não pode ser desfeita.`,
                    'danger'
                );

                if (!confirmed) return;

                try {
                    const formData = new FormData();
                    formData.append('_token', this.csrfToken);
                    formData.append('_method', 'DELETE');

                    const response = await this.makeRequest(
                        this.apiEndpoints.destroy(productId),
                        'POST',
                        formData
                    );

                    if (response.success) {
                        this.showToast(response.message || 'Produto excluído com sucesso!', 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        this.showToast(response.message || 'Erro ao excluir produto', 'error');
                    }

                } catch (error) {
                    this.handleError('Erro ao excluir produto', error);
                }
            }

            // ===== UTILITÁRIOS DE REQUISIÇÃO =====
            async makeRequest(url, method = 'GET', body = null, additionalHeaders = {}) {
                const headers = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    ...additionalHeaders
                };

                // Adicionar CSRF token se não estiver no FormData
                if (this.csrfToken && !(body instanceof FormData)) {
                    headers['X-CSRF-TOKEN'] = this.csrfToken;
                }

                const config = {
                    method,
                    headers
                };

                if (body) {
                    config.body = body;
                    // Se não for FormData, definir Content-Type
                    if (!(body instanceof FormData)) {
                        headers['Content-Type'] = 'application/json';
                    }
                }

                try {
                    const response = await fetch(url, config);

                    // Verificar se a resposta é válida
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                    const contentType = response.headers.get('content-type');
                    if (!contentType?.includes('application/json')) {
                        throw new Error('Resposta não é JSON válido');
                    }

                    return await response.json();

                } catch (error) {
                    console.error('Erro na requisição:', error);
                    throw error;
                }
            }

            // ===== VALIDAÇÃO =====
            validateForm() {
                this.clearValidation();
                let isValid = true;

                const requiredFields = [{
                        id: 'product-name',
                        message: 'Nome é obrigatório'
                    },
                    {
                        id: 'product-category',
                        message: 'Categoria é obrigatória'
                    },
                    {
                        id: 'product-type',
                        message: 'Tipo é obrigatório'
                    },
                    {
                        id: 'selling-price',
                        message: 'Preço de venda é obrigatório'
                    }
                ];

                requiredFields.forEach(field => {
                    const element = document.getElementById(field.id);
                    if (!element?.value?.trim()) {
                        this.showFieldError(field.id, field.message);
                        isValid = false;
                    }
                });

                // Validação específica para preço
                const sellingPrice = parseFloat(document.getElementById('selling-price')?.value);
                if (sellingPrice <= 0) {
                    this.showFieldError('selling-price', 'Preço deve ser maior que zero');
                    isValid = false;
                }

                // Validações específicas para produtos
                const productType = document.getElementById('product-type')?.value;
                if (productType === 'product') {
                    const stockFields = [{
                            id: 'stock-quantity',
                            message: 'Estoque inicial é obrigatório'
                        },
                        {
                            id: 'min-stock-level',
                            message: 'Estoque mínimo é obrigatório'
                        }
                    ];

                    stockFields.forEach(field => {
                        const element = document.getElementById(field.id);
                        const value = parseInt(element?.value);
                        if (isNaN(value) || value < 0) {
                            this.showFieldError(field.id, field.message);
                            isValid = false;
                        }
                    });
                }

                return isValid;
            }

            validateStockForm() {
                this.clearValidation();
                let isValid = true;

                const adjustmentType = document.getElementById('adjustment-type')?.value;
                const quantity = parseInt(document.getElementById('adjustment-quantity')?.value);
                const reason = document.getElementById('adjustment-reason')?.value?.trim();

                if (!adjustmentType) {
                    this.showFieldError('adjustment-type', 'Selecione o tipo de ajuste');
                    isValid = false;
                }

                if (isNaN(quantity) || quantity <= 0) {
                    this.showFieldError('adjustment-quantity', 'Quantidade deve ser maior que zero');
                    isValid = false;
                }

                if (!reason) {
                    this.showFieldError('adjustment-reason', 'Motivo é obrigatório');
                    isValid = false;
                }

                return isValid;
            }

            // ===== MANIPULAÇÃO DO FORMULÁRIO =====
            setFormMode(mode, productId = null) {
                const isEdit = mode === 'edit';
                this.currentProductId = productId;

                document.getElementById('form-title').textContent = isEdit ? 'Editar Produto' : 'Novo Produto';
                document.getElementById('form-method').value = isEdit ? 'PUT' : 'POST';
                document.getElementById('product-form').action = isEdit ?
                    `/products/${productId}` :
                    '/products';
            }

            populateForm(data) {
                const fieldMap = {
                    'product-name': data.name,
                    'product-category': data.category_id,
                    'product-type': data.type,
                    'product-description': data.description || '',
                    'selling-price': data.selling_price,
                    'purchase-price': data.purchase_price || '',
                    'product-unit': data.unit || '',
                    'stock-quantity': data.stock_quantity || 0,
                    'min-stock-level': data.min_stock_level || 0
                };

                Object.entries(fieldMap).forEach(([fieldId, value]) => {
                    const element = document.getElementById(fieldId);
                    if (element) {
                        element.value = value;
                    }
                });

                // Checkbox para produto ativo
                const activeCheckbox = document.getElementById('product-active');
                if (activeCheckbox) {
                    activeCheckbox.checked = data.is_active;
                }

                // Mostrar/ocultar campos específicos
                this.toggleProductFields(data.type === 'product');
            }

            resetForm() {
                const form = document.getElementById('product-form');
                if (form) {
                    form.reset();
                    document.getElementById('product-active').checked = true;
                    this.toggleProductFields(false);
                    this.clearValidation();
                }
            }

            resetStockForm() {
                const form = document.getElementById('stock-adjust-form');
                if (form) {
                    form.reset();
                    this.clearValidation();
                }
            }

            toggleProductFields(show) {
                const productFields = document.getElementById('product-specific-fields');
                if (productFields) {
                    productFields.style.display = show ? 'block' : 'none';
                }
            }

            addFormSpecificData(formData, method) {
                if (method === 'PUT') {
                    formData.append('_method', 'PUT');
                }
                formData.append('_token', this.csrfToken);
            }

            // ===== UI E FEEDBACK =====
            showOffcanvas(offcanvasId) {
                const offcanvasElement = document.getElementById(offcanvasId);
                if (offcanvasElement) {
                    const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
                    offcanvas.show();
                    return offcanvas;
                }
            }

            hideOffcanvas(offcanvasId) {
                const offcanvasElement = document.getElementById(offcanvasId);
                if (offcanvasElement) {
                    const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if (offcanvas) {
                        offcanvas.hide();
                    }
                }
            }

            showToast(message, type = 'info', duration = 4000) {
                const container = this.getToastContainer();

                const typeConfig = {
                    success: {
                        bg: 'bg-success',
                        icon: 'fas fa-check-circle'
                    },
                    error: {
                        bg: 'bg-danger',
                        icon: 'fas fa-exclamation-circle',
                        duration: 7000
                    },
                    warning: {
                        bg: 'bg-warning text-dark',
                        icon: 'fas fa-exclamation-triangle'
                    },
                    info: {
                        bg: 'bg-info',
                        icon: 'fas fa-info-circle'
                    }
                };

                const config = typeConfig[type] || typeConfig.info;
                const toastDuration = config.duration || duration;

                const toast = document.createElement('div');
                toast.className = `toast align-items-center text-white ${config.bg} border-0 mb-2`;
                toast.setAttribute('role', 'alert');
                toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="${config.icon} me-2"></i>${message}
                </div>
                <button type="button" class="btn-close ${type === 'warning' ? 'btn-close-dark' : 'btn-close-white'} me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

                container.appendChild(toast);

                const bsToast = new bootstrap.Toast(toast, {
                    delay: toastDuration
                });
                bsToast.show();

                toast.addEventListener('hidden.bs.toast', () => toast.remove());
            }

            async showConfirmDialog(title, message, type = 'primary') {
                return new Promise((resolve) => {
                    // Criar modal dinamicamente se não existir
                    let modal = document.getElementById('confirmModal');
                    if (!modal) {
                        modal = this.createConfirmModal();
                        document.body.appendChild(modal);
                    }

                    const modalTitle = modal.querySelector('.modal-title');
                    const modalBody = modal.querySelector('.modal-body');
                    const confirmBtn = modal.querySelector('.btn-confirm');

                    modalTitle.textContent = title;
                    modalBody.innerHTML = `<p>${message}</p>`;

                    // Definir cor do botão baseado no tipo
                    confirmBtn.className = `btn btn-${type} btn-confirm`;

                    const bsModal = new bootstrap.Modal(modal);

                    // Event listeners
                    const handleConfirm = () => {
                        bsModal.hide();
                        resolve(true);
                    };

                    const handleCancel = () => {
                        bsModal.hide();
                        resolve(false);
                    };

                    // Remover listeners anteriores
                    confirmBtn.removeEventListener('click', handleConfirm);
                    modal.querySelector('.btn-secondary').removeEventListener('click', handleCancel);

                    // Adicionar novos listeners
                    confirmBtn.addEventListener('click', handleConfirm);
                    modal.querySelector('.btn-secondary').addEventListener('click', handleCancel);

                    // Resolver como false quando o modal for fechado sem confirmação
                    modal.addEventListener('hidden.bs.modal', () => resolve(false), {
                        once: true
                    });

                    bsModal.show();
                });
            }

            createConfirmModal() {
                const modal = document.createElement('div');
                modal.id = 'confirmModal';
                modal.className = 'modal fade';
                modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary">Cancelar</button>
                        <button type="button" class="btn btn-primary btn-confirm">Confirmar</button>
                    </div>
                </div>
            </div>
        `;
                return modal;
            }

            setButtonLoading(button, isLoading) {
                if (!button) return;

                if (isLoading) {
                    button.disabled = true;
                    button.dataset.originalText = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';
                } else {
                    button.disabled = false;
                    button.innerHTML = button.dataset.originalText || button.innerHTML;
                }
            }

            showLoadingState(show) {
                // Implementar overlay de loading global se necessário
                const loader = document.getElementById('global-loader');
                if (loader) {
                    loader.style.display = show ? 'block' : 'none';
                }
            }

            // ===== VALIDAÇÃO E ERROS =====
            showFieldError(fieldId, message) {
                const field = document.getElementById(fieldId);
                if (!field) return;

                field.classList.add('is-invalid');

                let feedback = field.parentNode.querySelector('.invalid-feedback');
                if (!feedback) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    field.parentNode.appendChild(feedback);
                }

                feedback.textContent = message;

                // Focus no primeiro campo com erro
                if (!document.querySelector('.is-invalid:focus')) {
                    field.focus();
                }
            }

            clearValidation() {
                document.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
                document.querySelectorAll('.invalid-feedback').forEach(el => {
                    el.textContent = '';
                });
            }

            handleFormErrors(errors) {
                Object.entries(errors).forEach(([field, messages]) => {
                    const fieldName = field.replace('_', '-');
                    this.showFieldError(`product-${fieldName}`, messages[0]);
                });
            }

            handleError(message, error = null) {
                console.error(message, error);
                this.showToast(message, 'error');
            }

            // ===== CONTENT GENERATORS =====
            generateQuickViewContent(product) {
                return `
            <div class="row g-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-box me-2"></i>${product.name}
                        </h6>
                        <p class="mb-1"><strong>Categoria:</strong> ${product.category?.name || 'N/A'}</p>
                        <p class="mb-0"><strong>Tipo:</strong> ${product.type === 'product' ? 'Produto' : 'Serviço'}</p>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-primary">
                                <i class="fas fa-info-circle me-2"></i>Informações
                            </h6>
                            <p class="card-text small mb-1"><strong>Status:</strong> ${product.is_active ? 'Ativo' : 'Inativo'}</p>
                            ${product.type === 'product' ? `
                                        <p class="card-text small mb-1"><strong>Estoque:</strong> ${product.stock_quantity} ${product.unit || ''}</p>
                                        <p class="card-text small mb-0"><strong>Estoque Mín:</strong> ${product.min_stock_level}</p>
                                    ` : '<p class="card-text small mb-0">Não aplicável para serviços</p>'}
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-success">
                                <i class="fas fa-money-bill-wave me-2"></i>Preços
                            </h6>
                            <p class="card-text"><strong class="text-success fs-5">MT ${this.formatCurrency(product.selling_price)}</strong></p>
                            ${product.purchase_price ? `<small class="text-muted">Compra: MT ${this.formatCurrency(product.purchase_price)}</small>` : ''}
                        </div>
                    </div>
                </div>
                
                ${product.description ? `
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title text-secondary">
                                            <i class="fas fa-file-text me-2"></i>Descrição
                                        </h6>
                                        <p class="card-text">${product.description}</p>
                                    </div>
                                </div>
                            </div>
                        ` : ''}
            </div>
        `;
            }

            setQuickViewLoading(container) {
                container.innerHTML = `
            <div class="text-center py-5">
                <div class="loading-spinner mb-3"></div>
                <p class="text-muted">Carregando detalhes...</p>
            </div>
        `;
            }

            getErrorContent(message) {
                return `
            <div class="text-center py-4 text-danger">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <p>${message}</p>
            </div>
        `;
            }

            // ===== UTILITÁRIOS =====
            formatCurrency(value) {
                return parseFloat(value).toFixed(2).replace('.', ',');
            }

            initializeToastContainer() {
                if (!document.getElementById('toast-container')) {
                    const container = document.createElement('div');
                    container.id = 'toast-container';
                    container.className = 'toast-container position-fixed top-0 end-0 p-3';
                    container.style.zIndex = '9999';
                    document.body.appendChild(container);
                }
            }

            getToastContainer() {
                return document.getElementById('toast-container');
            }
        }

        // ===== INICIALIZAÇÃO =====
        document.addEventListener('DOMContentLoaded', function() {
            window.productManager = new ProductManager();
        });

        // ===== FUNÇÕES GLOBAIS PARA COMPATIBILIDADE =====
        function openCreateProductOffcanvas() {
            window.productManager?.openCreateModal();
        }

        function editProduct(productId) {
            window.productManager?.openEditModal(productId);
        }

        function quickViewOffcanvas(productId) {
            window.productManager?.quickViewOffcanvas(productId);
        }

        function quickViewInline(productId) {
            window.productManager?.quickViewInline(productId);
        }

        function openStockOffcanvas(productId) {
            window.productManager?.openStockModal(productId);
        }

        function confirmDeletePopup(productId, productName) {
            window.productManager?.confirmDelete(productId, productName);
        }

        function confirmDeleteToast(productId, productName) {
            window.productManager?.confirmDelete(productId, productName);
        }

        function clearSearch() {
            const form = document.getElementById('filters-form');
            const searchInput = form?.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.value = '';
                form.submit();
            }
        }

        function exportProducts() {
            window.productManager?.showToast('Funcionalidade de exportação em desenvolvimento', 'info');
        }

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

        .product-row:hover {
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

        .btn-group .btn {
            margin: 0 1px;
            border-radius: 4px;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .badge {
            font-weight: 500;
            font-size: 0.75rem;
        }

        .offcanvas-end {
            width: 600px !important;
        }

        @media (max-width: 768px) {
            .offcanvas-end {
                width: 100% !important;
            }
        }
    </style>
@endpush
