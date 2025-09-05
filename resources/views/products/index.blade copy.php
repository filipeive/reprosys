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
            <button class="btn btn-primary" onclick="openCreateProductOffcanvas()">
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
                <button type="submit" form="product-form" class="btn btn-primary flex-fill">
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

    <!-- Dropdown/Popover para Confirmação de Exclusão -->
    <div class="position-fixed" id="delete-confirmation-container" style="display: none; z-index: 9999;">
        <div class="card shadow-lg border-0" style="width: 350px;">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Exclusão
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-2">Deseja excluir o produto:</p>
                <div class="alert alert-light border">
                    <strong id="delete-product-name-popup"></strong>
                </div>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita.
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

    <!-- Toast para Confirmação de Exclusão -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
        <div id="deleteToast" class="toast hide" role="alert">
            <div class="toast-header bg-warning text-dark">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong class="me-auto">Confirmação Necessária</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                <p class="mb-2">Excluir produto: <strong id="delete-product-toast"></strong>?</p>
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-sm btn-secondary" data-bs-dismiss="toast">Não</button>
                    <form id="delete-form-toast" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Sim, Excluir</button>
                    </form>
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
                    <div class="invalid-feedback">Selecione o tipo de ajuste.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Quantidade</label>
                    <input type="number" class="form-control" id="adjustment-quantity" min="1" required>
                    <div class="invalid-feedback">Informe uma quantidade válida.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Motivo</label>
                    <input type="text" class="form-control" id="adjustment-reason" maxlength="200" required>
                    <div class="invalid-feedback">Informe o motivo do ajuste.</div>
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
                            <th style="width: 250px;" class="text-center">Ações</th>
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
                                        <!-- Botão para Offcanvas -->
                                        <button type="button" class="btn btn-outline-info"
                                            onclick="quickViewOffcanvas({{ $product->id }})"
                                            title="Visualização Lateral">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <!-- Botão para Collapse Inline -->
                                        <button type="button" class="btn btn-outline-secondary"
                                            onclick="quickViewInline({{ $product->id }})" title="Visualização Inline">
                                            <i class="fas fa-expand"></i>
                                        </button>

                                        <a href="{{ route('products.show', $product->id) }}"
                                            class="btn btn-outline-primary" title="Ver Detalhes">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>

                                        <button type="button" class="btn btn-outline-warning"
                                            onclick="editProduct({{ $product->id }})" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        @if ($product->type === 'product')
                                            <button type="button" class="btn btn-outline-success"
                                                onclick="openStockOffcanvas({{ $product->id }}, '{{ $product->name }}', {{ $product->stock_quantity }})"
                                                title="Ajustar Estoque">
                                                <i class="fas fa-cubes"></i>
                                            </button>
                                        @endif
                                        <!-- Botão para confirmação via Toast -->
                                        <button type="button" class="btn btn-outline-warning"
                                            onclick="confirmDeleteToast({{ $product->id }}, '{{ $product->name }}')"
                                            title="Excluir via Toast">
                                            <i class="fas fa-bell"></i>
                                        </button>

                                        <!-- Botão para confirmação via Popup -->
                                        <button type="button" class="btn btn-outline-danger"
                                            onclick="confirmDeletePopup({{ $product->id }}, '{{ $product->name }}', event)"
                                            title="Excluir Produto">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
                                        <button class="btn btn-primary" onclick="openCreateProductOffcanvas()">
                                            <i class="fas fa-plus me-2"></i>Adicionar Primeiro Produto
                                        </button>
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
        // ===== CRIAR/EDITAR PRODUTO OFFCANVAS =====
        function openCreateProductOffcanvas() {
            resetProductForm();
            document.getElementById('form-title').textContent = 'Novo Produto';
            document.getElementById('form-method').value = 'POST';
            document.getElementById('product-form').action = '{{ route('products.store') }}';
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('productFormOffcanvas'));
            offcanvas.show();
        }

        function editProduct(productId) {
            resetProductForm();
            document.getElementById('form-title').textContent = 'Editar Produto';
            document.getElementById('form-method').value = 'PUT';
            document.getElementById('product-form').action = `/products/${productId}`;
            document.getElementById('product-id').value = productId;

            // Buscar dados do produto
            const productRow = document.querySelector(`[data-product-id="${productId}"]`);
            if (productRow) {
                // Simular preenchimento dos dados (em produção, fazer requisição AJAX)
                populateProductForm(productId);
            }

            const offcanvas = new bootstrap.Offcanvas(document.getElementById('productFormOffcanvas'));
            offcanvas.show();
        }

        function resetProductForm() {
            document.getElementById('product-form').reset();
            document.getElementById('product-id').value = '';
            document.getElementById('product-active').checked = true;
            document.getElementById('product-specific-fields').style.display = 'none';
            clearValidation();
        }

        function populateProductForm(productId) {
            fetch(`/products/${productId}/edit-data`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const productData = data.data;
                        document.getElementById('product-name').value = productData.name;
                        document.getElementById('product-category').value = productData.category_id;
                        document.getElementById('product-type').value = productData.type;
                        document.getElementById('product-description').value = productData.description || '';
                        document.getElementById('selling-price').value = productData.selling_price;
                        document.getElementById('purchase-price').value = productData.purchase_price || '';
                        document.getElementById('product-unit').value = productData.unit || '';
                        document.getElementById('stock-quantity').value = productData.stock_quantity || '';
                        document.getElementById('min-stock-level').value = productData.min_stock_level || '';
                        document.getElementById('product-active').checked = productData.is_active;

                        if (productData.type === 'product') {
                            document.getElementById('product-specific-fields').style.display = 'block';
                        } else {
                            document.getElementById('product-specific-fields').style.display = 'none';
                        }
                    } else {
                        showToast('Erro ao carregar dados do produto', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar dados do produto:', error);
                    showToast('Erro ao carregar dados do produto', 'error');
                });
        }

        // ===== OFFCANVAS QUICK VIEW =====
        function quickViewOffcanvas(productId) {
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('quickViewOffcanvas'));
            const content = document.getElementById('quick-view-offcanvas-content');
            const productIdSpan = document.getElementById('product-id-offcanvas');

            // Atualizar dados
            productIdSpan.textContent = productId;
            document.getElementById('view-full-product-offcanvas').href = `/products/${productId}`;

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
                content.innerHTML = generateQuickViewContent(productId);
            }, 800);
        }

        // ===== INLINE QUICK VIEW =====
        function quickViewInline(productId) {
            const collapse = new bootstrap.Collapse(document.getElementById('inlineQuickView'), {
                show: true
            });
            const content = document.getElementById('quick-view-inline-content');
            const productIdSpan = document.getElementById('product-id-inline');

            // Atualizar dados
            productIdSpan.textContent = productId;
            document.getElementById('view-full-product-inline').href = `/products/${productId}`;

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
                content.innerHTML = generateQuickViewContent(productId);
            }, 800);
        }

        // ===== POPUP CONFIRMATION =====
        function confirmDeletePopup(productId, productName, event) {
            const container = document.getElementById('delete-confirmation-container');
            const productElement = document.getElementById('delete-product-name-popup');
            const form = document.getElementById('delete-form-popup');

            // Posicionar próximo ao botão clicado
            const rect = event.target.getBoundingClientRect();
            container.style.position = 'fixed';
            container.style.left = Math.min(rect.left - 200, window.innerWidth - 370) + 'px';
            container.style.top = (rect.top - 10) + 'px';
            container.style.display = 'block';

            // Configurar dados
            productElement.textContent = productName;
            form.action = `/products/${productId}`;

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
        function confirmDeleteToast(productId, productName) {
            const toast = new bootstrap.Toast(document.getElementById('deleteToast'));

            document.getElementById('delete-product-toast').textContent = productName;
            document.getElementById('delete-form-toast').action = `/products/${productId}`;

            toast.show();
        }

        // ===== STOCK OFFCANVAS =====
        function openStockOffcanvas(productId, productName, currentStock) {
            document.getElementById('stock-product-id').value = productId;
            document.getElementById('stock-product-name').textContent = productName;
            document.getElementById('current-stock').textContent = currentStock;

            // Limpar o formulário
            document.getElementById('stock-adjust-form').reset();
            clearValidation(); // função já existente no código

            const offcanvas = new bootstrap.Offcanvas(document.getElementById('stockAdjustOffcanvas'));
            offcanvas.show();
        }

        // ===== HELPER FUNCTION =====
        function generateQuickViewContent(productId) {
            const productRow = document.querySelector(`[data-product-id="${productId}"]`);
            if (!productRow) {
                return `
            <div class="text-center py-4 text-danger">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <p>Erro ao carregar detalhes do produto.</p>
            </div>
        `;
            }

            const cells = productRow.querySelectorAll('td');
            const productName = cells[1].textContent.trim();
            const category = cells[2].textContent.trim();
            const type = cells[3].textContent.trim();
            const price = cells[4].textContent.trim();
            const stock = cells[5].textContent.trim();
            const status = cells[6].textContent.trim();

            return `
        <div class="row g-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <h6 class="alert-heading">
                        <i class="fas fa-box me-2"></i>Produto/Serviço
                    </h6>
                    <p class="mb-1"><strong>Nome:</strong> ${productName}</p>
                    <p class="mb-0"><strong>Categoria:</strong> ${category}</p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title text-primary">
                            <i class="fas fa-info-circle me-2"></i>Informações
                        </h6>
                        <p class="card-text small mb-1"><strong>Tipo:</strong> ${type}</p>
                        <p class="card-text small mb-1"><strong>Status:</strong> ${status}</p>
                        <p class="card-text small mb-0"><strong>Estoque:</strong> ${stock !== 'N/A' ? stock : 'Não aplicável'}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title text-success">
                            <i class="fas fa-money-bill-wave me-2"></i>Preço
                        </h6>
                        <p class="card-text"><strong class="text-success fs-5">${price}</strong></p>
                        <small class="text-muted">Preço de venda atual</small>
                    </div>
                </div>
            </div>
        </div>
    `;
        }

        // ===== UTILITY FUNCTIONS =====
        function clearSearch() {
            const form = document.getElementById('filters-form');
            const searchInput = form.querySelector('input[name="search"]');
            searchInput.value = '';
            form.submit();
        }

        function exportProducts() {
            if (window.showToast) {
                showToast('Funcionalidade de exportação em desenvolvimento', 'info');
            } else {
                alert('Funcionalidade de exportação em desenvolvimento');
            }
        }

        function printList() {
            window.print();
        }

        function clearValidation() {
            document.querySelectorAll('.form-control, .form-select').forEach(field => {
                field.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(feedback => {
                feedback.textContent = '';
            });
        }

        function showFieldError(fieldId, message) {
            const field = document.querySelector(fieldId);
            if (field) {
                field.classList.add('is-invalid');
                const feedback = field.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = message;
                }
            }
        }

        // ===== FORM SUBMISSIONS =====
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit de filtros
            const form = document.getElementById('filters-form');
            const selects = form.querySelectorAll('select');

            selects.forEach(select => {
                select.addEventListener('change', () => form.submit());
            });

            // Mostrar/ocultar campos específicos para produtos
            const productTypeSelect = document.getElementById('product-type');
            if (productTypeSelect) {
                productTypeSelect.addEventListener('change', function() {
                    const productFields = document.getElementById('product-specific-fields');
                    if (this.value === 'product') {
                        productFields.style.display = 'block';
                    } else {
                        productFields.style.display = 'none';
                    }
                });
            }

            // Submit do formulário de produto
            document.getElementById('product-form').addEventListener('submit', function(e) {
                e.preventDefault();

                if (!validateProductForm()) {
                    return false;
                }

                const formData = new FormData(this);
                const method = document.getElementById('form-method').value;
                const url = this.action;

                // Para métodos PUT, adicionar o parâmetro _method
                if (method === 'PUT') {
                    formData.append('_method', 'PUT');
                }

                fetch(url, {
                        method: 'POST', // Sempre POST, o Laravel trata o _method
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            bootstrap.Offcanvas.getInstance(document.getElementById(
                                'productFormOffcanvas')).hide();
                            showToast(data.message || 'Produto salvo com sucesso', 'success');
                            // Recarregar página após 1 segundo
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            // Mostrar erros de validação
                            if (data.errors) {
                                Object.keys(data.errors).forEach(field => {
                                    const fieldName = field.replace('_', '-');
                                    showFieldError(`#product-${fieldName}`, data.errors[field][
                                        0
                                    ]);
                                });
                            }
                            showToast(data.message || 'Erro ao salvar produto', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        showToast('Erro ao salvar produto', 'error');
                    });
            });

            // Submit do formulário de ajuste de estoque (Offcanvas)
            document.getElementById('stock-adjust-form').addEventListener('submit', function(e) {
                e.preventDefault();
                if (!validateStockForm()) {
                    return false;
                }
                const productId = document.getElementById('stock-product-id').value;
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                formData.append('adjustment_type', document.getElementById('adjustment-type').value);
                formData.append('quantity', document.getElementById('adjustment-quantity').value);
                formData.append('reason', document.getElementById('adjustment-reason').value);

                fetch(`/products/${productId}/adjust-stock`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message, 'success');
                            bootstrap.Offcanvas.getInstance(document.getElementById(
                                'stockAdjustOffcanvas')).hide();
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            showToast(data.message || 'Erro ao ajustar estoque', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        showToast('Erro ao ajustar estoque', 'error');
                    });
            });

            function validateStockForm() {
                clearValidation();
                let isValid = true;

                const type = document.getElementById('adjustment-type').value;
                const quantity = document.getElementById('adjustment-quantity').value;
                const reason = document.getElementById('adjustment-reason').value;

                if (!type) {
                    showFieldError('#adjustment-type', 'Selecione o tipo de ajuste');
                    isValid = false;
                }
                if (!quantity || quantity < 1) {
                    showFieldError('#adjustment-quantity', 'Quantidade deve ser maior que 0');
                    isValid = false;
                }
                if (!reason.trim()) {
                    showFieldError('#adjustment-reason', 'Informe o motivo');
                    isValid = false;
                }

                return isValid;
            }

            function validateProductForm() {
                clearValidation();
                let isValid = true;

                const name = document.getElementById('product-name').value.trim();
                const category = document.getElementById('product-category').value;
                const type = document.getElementById('product-type').value;
                const sellingPrice = document.getElementById('selling-price').value;

                if (!name) {
                    showFieldError('#product-name', 'Nome é obrigatório');
                    isValid = false;
                }

                if (!category) {
                    showFieldError('#product-category', 'Categoria é obrigatória');
                    isValid = false;
                }

                if (!type) {
                    showFieldError('#product-type', 'Tipo é obrigatório');
                    isValid = false;
                }

                if (!sellingPrice || sellingPrice <= 0) {
                    showFieldError('#selling-price', 'Preço de venda deve ser maior que zero');
                    isValid = false;
                }

                return isValid;
            }
        });
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
