@extends('layouts.app')

@section('title', 'Gestão de Dívidas')
@section('page-title', 'Gestão de Dívidas')
@section('title-icon', 'fa-credit-card')
@section('breadcrumbs')
    <li class="breadcrumb-item active">Dívidas</li>
@endsection

@section('content')
    <!-- Offcanvas para Criar/Editar Dívida -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="debtFormOffcanvas" style="width: 800px;">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-credit-card me-2"></i><span id="form-title">Nova Dívida</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <form id="debt-form" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <input type="hidden" name="debt_id" id="debt-id">

                <!-- Seleção do Tipo de Dívida -->
                <div class="p-4 border-bottom bg-light">
                    <h6 class="mb-3"><i class="fas fa-tags me-2"></i> Tipo de Dívida</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check p-3 border rounded bg-white debt-type-option" data-type="product">
                                <input class="form-check-input" type="radio" name="debt_type" id="debt-type-product"
                                    value="product" checked>
                                <label class="form-check-label w-100" for="debt-type-product">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-shopping-cart fa-2x text-primary me-3"></i>
                                        <div>
                                            <strong>Dívida de Produtos</strong>
                                            <div class="small text-muted">Cliente deve produtos/serviços</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check p-3 border rounded bg-white debt-type-option" data-type="money">
                                <input class="form-check-input" type="radio" name="debt_type" id="debt-type-money"
                                    value="money">
                                <label class="form-check-label w-100" for="debt-type-money">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-money-bill-wave fa-2x text-success me-3"></i>
                                        <div>
                                            <strong>Dívida de Dinheiro</strong>
                                            <div class="small text-muted">Funcionário deve valor em dinheiro</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações do Devedor -->
                <div class="p-4 border-bottom bg-light">
                    <h6 class="mb-3">
                        <i class="fas fa-user me-2"></i>
                        <span id="debtor-section-title">Informações do Cliente</span>
                    </h6>

                    <!-- Para Clientes (Dívida de Produtos) -->
                    <div class="row g-3" id="customer-fields">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nome do Cliente *</label>
                                <input type="text" class="form-control" name="customer_name" id="customer-name">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Telefone</label>
                                <input type="text" class="form-control" name="customer_phone" id="customer-phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Documento</label>
                                <input type="text" class="form-control" name="customer_document" id="customer-document">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Data da Dívida *</label>
                                <input type="date" class="form-control" name="debt_date" id="debt-date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Data de Vencimento</label>
                                <input type="date" class="form-control" name="due_date" id="due-date">
                            </div>
                        </div>
                    </div>

                    <!-- Para Funcionários (Dívida de Dinheiro) -->
                    <div class="row g-3 d-none" id="employee-fields">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Funcionário *</label>
                                <select class="form-select" name="employee_id" id="employee-select">
                                    <option value="">Selecione um funcionário...</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" data-name="{{ $employee->name }}"
                                            data-phone="{{ $employee->phone ?? '' }}" data-email="{{ $employee->email }}">
                                            {{ $employee->name }} - {{ $employee->email }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nome Completo *</label>
                                <input type="text" class="form-control" name="employee_name" id="employee-name">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Telefone</label>
                                <input type="text" class="form-control" name="employee_phone" id="employee-phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Documento</label>
                                <input type="text" class="form-control" name="employee_document"
                                    id="employee-document">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Data da Dívida *</label>
                                <input type="date" class="form-control" name="debt_date_money" id="debt-date-money">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Data de Vencimento</label>
                                <input type="date" class="form-control" name="due_date_money" id="due-date-money">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Valor da Dívida *</label>
                                <div class="input-group">
                                    <span class="input-group-text">MT</span>
                                    <input type="number" step="0.01" min="0.01" class="form-control"
                                        name="amount" id="debt-amount">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seleção de Produtos (apenas para dívida de produtos) -->
                <div class="p-4 border-bottom" id="products-section">
                    <h6 class="mb-3"><i class="fas fa-shopping-cart me-2"></i> Produtos da Dívida</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <select class="form-select" id="product-select">
                                <option value="">Selecione um produto ou serviço...</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                        data-type="{{ $product->type }}" data-unit="{{ $product->unit ?? 'unid' }}"
                                        data-price="{{ $product->selling_price }}"
                                        data-stock="{{ $product->stock_quantity ?? 0 }}">
                                        {{ $product->name }} - MT
                                        {{ number_format($product->selling_price, 2, ',', '.') }}
                                        @if ($product->type === 'product' && $product->stock_quantity)
                                            (Estoque: {{ $product->stock_quantity }} {{ $product->unit ?? 'unid' }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary w-100" onclick="addProductToCart()">
                                <i class="fas fa-plus me-2"></i>Adicionar
                            </button>
                        </div>
                    </div>

                    <!-- Carrinho de Produtos -->
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" id="products-cart-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto/Serviço</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Preço Unit.</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="products-cart-items">
                                <!-- Produtos serão adicionados aqui via JS -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total Geral:</td>
                                    <td class="text-end fw-bold" id="products-cart-total">MT 0,00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Descrição e Observações -->
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Descrição da Dívida *</label>
                                <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Observações</label>
                                <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Pagamento Inicial (Opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">MT</span>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        name="initial_payment" id="initial-payment">
                                </div>
                                <small class="text-muted">Deixe em branco se não houver pagamento inicial</small>
                            </div>
                        </div>
                    </div>

                    <!-- Campo hidden para os produtos (JSON) -->
                    <input type="hidden" name="products" id="products-json">
                </div>
            </form>
        </div>
        <div class="offcanvas-footer p-3 border-top">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="submit" form="debt-form" class="btn btn-primary flex-fill" id="save-debt-btn">
                    <i class="fas fa-save me-2"></i>Salvar Dívida
                </button>
            </div>
        </div>
    </div>

    <!-- Offcanvas para Visualizar Dívida -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="debtViewOffcanvas" style="width: 700px;">
        <div class="offcanvas-header bg-info text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-eye me-2"></i>Detalhes da Dívida
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body" id="debt-view-content">
            <div class="text-center py-5">
                <div class="loading-spinner mb-3"></div>
                <p class="text-muted">Carregando detalhes...</p>
            </div>
        </div>
    </div>

    <!-- Offcanvas para Registrar Pagamento -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="paymentOffcanvas" style="width: 500px;">
        <div class="offcanvas-header bg-success text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-money-bill me-2"></i> Registrar Pagamento
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="payment-form" method="POST">
                @csrf
                <input type="hidden" name="debt_id" id="payment-debt-id">

                <div class="alert alert-info">
                    <div><strong>Devedor:</strong> <span id="payment-debtor-name"></span></div>
                    <div><strong>Tipo:</strong> <span id="payment-debt-type"></span></div>
                    <div><strong>Valor Restante:</strong> <span id="payment-remaining-amount"></span></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Valor do Pagamento *</label>
                    <div class="input-group">
                        <span class="input-group-text">MT</span>
                        <input type="number" step="0.01" name="amount" id="payment-amount" class="form-control"
                            required>
                    </div>
                    <small class="text-muted">Máximo: <strong id="max-amount-text"></strong></small>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Forma de Pagamento *</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="">Selecione</option>
                        <option value="cash">Dinheiro</option>
                        <option value="card">Cartão</option>
                        <option value="transfer">Transferência</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="emola">E-mola</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Data do Pagamento *</label>
                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}"
                        required>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3" id="create-sale-option" style="display: none;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="create_sale" id="create-sale-checkbox"
                            checked>
                        <label class="form-check-label" for="create-sale-checkbox">
                            <strong>Criar venda automaticamente</strong>
                            <div class="small text-muted">Gera uma venda com os produtos desta dívida</div>
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Observações</label>
                    <textarea name="notes" class="form-control" rows="3" maxlength="500"></textarea>
                </div>
            </form>
        </div>
        <div class="offcanvas-footer p-3 border-top">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" form="payment-form" class="btn btn-success flex-fill">
                    <i class="fas fa-check me-2"></i> Registrar
                </button>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-credit-card me-2"></i>
                Gestão de Dívidas
            </h2>
            <p class="text-muted mb-0">Controle de dívidas de produtos e dinheiro</p>
        </div>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-plus me-2"></i> Nova Dívida
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="#" onclick="openCreateDebtOffcanvas('product')">
                            <i class="fas fa-shopping-cart me-2 text-primary"></i>
                            Dívida de Produtos
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" onclick="openCreateDebtOffcanvas('money')">
                            <i class="fas fa-money-bill-wave me-2 text-success"></i>
                            Dívida de Dinheiro
                        </a>
                    </li>
                </ul>
            </div>
            <a href="{{ route('debts.debtors-report') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-bar me-2"></i> Relatório
            </a>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Total em Aberto</h6>
                            <h3 class="mb-0 text-warning fw-bold">MT
                                {{ number_format($stats['total_active'], 2, ',', '.') }}</h3>
                            <small class="text-muted">todas as dívidas</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Produtos</h6>
                            <h3 class="mb-0 text-primary fw-bold">MT
                                {{ number_format($stats['product_debts']['total_active'], 2, ',', '.') }}</h3>
                            <small class="text-muted">{{ $stats['product_debts']['count_active'] }} dívidas</small>
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
                            <h6 class="text-muted mb-2 fw-semibold">Dinheiro</h6>
                            <h3 class="mb-0 text-success fw-bold">MT
                                {{ number_format($stats['money_debts']['total_active'], 2, ',', '.') }}</h3>
                            <small class="text-muted">{{ $stats['money_debts']['count_active'] }} dívidas</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Vencidas</h6>
                            <h3 class="mb-0 text-danger fw-bold">MT
                                {{ number_format($stats['total_overdue'], 2, ',', '.') }}</h3>
                            <small class="text-muted">em atraso</small>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-clock fa-2x"></i>
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
                Filtros de Dívidas
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('debts.index') }}" id="filters-form">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select class="form-select" name="debt_type">
                            <option value="">Todos</option>
                            <option value="product" {{ request('debt_type') === 'product' ? 'selected' : '' }}>Produtos
                            </option>
                            <option value="money" {{ request('debt_type') === 'money' ? 'selected' : '' }}>Dinheiro
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativa</option>
                            <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Vencida
                            </option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paga</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelada
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Devedor</label>
                        <input type="text" class="form-control" name="customer" placeholder="Nome do devedor..."
                            value="{{ request('customer') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Data Início</label>
                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Data Fim</label>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1">
                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" name="overdue_only" value="1"
                                {{ request('overdue_only') ? 'checked' : '' }}>
                            <label class="form-check-label small">Vencidas</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Dívidas -->
    <div class="card fade-in">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Dívidas Registradas
                </h5>
                <span class="badge bg-primary">Total: {{ $debts->total() }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Devedor</th>
                            <th>Descrição</th>
                            <th class="text-end">Valor Original</th>
                            <th class="text-end">Valor Pago</th>
                            <th class="text-end">Restante</th>
                            <th>Vencimento</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debts as $debt)
                            <tr class="{{ $debt->is_overdue ? 'table-warning' : '' }}">
                                <td><strong class="text-primary">#{{ $debt->id }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i
                                            class="fas {{ $debt->debt_type_icon }} me-2 text-{{ $debt->isProductDebt() ? 'primary' : 'success' }}"></i>
                                        <span
                                            class="badge bg-{{ $debt->isProductDebt() ? 'primary' : 'success' }} bg-opacity-10 text-{{ $debt->isProductDebt() ? 'primary' : 'success' }}">
                                            {{ $debt->debt_type_text }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $debt->debtor_name }}</div>
                                    @if ($debt->debtor_phone)
                                        <small class="text-muted">
                                            <i class="fas fa-phone me-1"></i> {{ $debt->debtor_phone }}
                                        </small>
                                    @endif
                                    @if ($debt->isMoneyDebt() && $debt->employee)
                                        <small class="text-muted d-block">{{ $debt->employee->email }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ Str::limit($debt->description, 50) }}</div>
                                    @if ($debt->generated_sale_id)
                                        <small class="text-success">
                                            <i class="fas fa-shopping-bag me-1"></i>Venda #{{ $debt->generated_sale_id }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong>{{ $debt->formatted_original_amount }}</strong>
                                </td>
                                <td class="text-end">
                                    @if ($debt->amount_paid > 0)
                                        <span class="text-success fw-bold">{{ $debt->formatted_amount_paid }}</span>
                                    @else
                                        <span class="text-muted">MT 0,00</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($debt->remaining_amount > 0)
                                        <strong class="text-{{ $debt->is_overdue ? 'danger' : 'warning' }}">
                                            {{ $debt->formatted_remaining_amount }}
                                        </strong>
                                    @else
                                        <span class="text-success">MT 0,00</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($debt->due_date)
                                        <div class="{{ $debt->is_overdue ? 'text-danger' : '' }}">
                                            {{ $debt->due_date->format('d/m/Y') }}
                                        </div>
                                        @if ($debt->is_overdue)
                                            <small class="text-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                {{ $debt->days_overdue }} dias
                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted">Sem prazo</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $debt->status_badge }}">{{ $debt->status_text }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info"
                                            onclick="viewDebtDetails({{ $debt->id }})" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if ($debt->canBeEdited())
                                            <button type="button" class="btn btn-outline-warning"
                                                onclick="openEditDebtOffcanvas({{ $debt->id }})"
                                                title="Editar Dívida">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        @if ($debt->canReceivePayment())
                                            <button type="button" class="btn btn-outline-success"
                                                onclick="openPaymentOffcanvas({{ $debt->id }}, '{{ $debt->debtor_name }}', '{{ $debt->debt_type_text }}', {{ $debt->remaining_amount }}, {{ $debt->isProductDebt() ? 'true' : 'false' }})"
                                                title="Registrar Pagamento">
                                                <i class="fas fa-money-bill"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('debts.show', $debt) }}" class="btn btn-outline-primary btn-sm"
                                            title="Ver Página Completa">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        @if ($debt->canBeCancelled())
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="cancelDebt({{ $debt->id }})" title="Cancelar Dívida">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-credit-card fa-3x mb-3 opacity-50"></i>
                                    <p>Nenhuma dívida encontrada.</p>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary"
                                            onclick="openCreateDebtOffcanvas('product')">
                                            <i class="fas fa-shopping-cart me-2"></i> Dívida de Produtos
                                        </button>
                                        <button type="button" class="btn btn-success"
                                            onclick="openCreateDebtOffcanvas('money')">
                                            <i class="fas fa-money-bill-wave me-2"></i> Dívida de Dinheiro
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando {{ $debts->firstItem() ?? 0 }} a {{ $debts->lastItem() ?? 0 }} de {{ $debts->total() }}
                </small>
                {{ $debts->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        // Variáveis globais
        let productsCart = [];
        let currentDebtType = 'product';

        // Inicialização quando página carrega
        document.addEventListener('DOMContentLoaded', function() {
            initializeDebtTypeToggle();
            initializeEmployeeSelect();
            initializeFilters();
            setDefaultDates();
        });

        // Configurar toggle de tipo de dívida
        function initializeDebtTypeToggle() {
            const debtTypeRadios = document.querySelectorAll('input[name="debt_type"]');
            const debtTypeOptions = document.querySelectorAll('.debt-type-option');

            debtTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    currentDebtType = this.value;
                    toggleDebtTypeFields();
                    updateDebtTypeSelection();
                });
            });

            debtTypeOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const type = this.dataset.type;
                    const radio = document.getElementById(`debt-type-${type}`);
                    if (radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change'));
                    }
                });
            });
        }

        // Alternar campos baseado no tipo de dívida
        function toggleDebtTypeFields() {
            const customerFields = document.getElementById('customer-fields');
            const employeeFields = document.getElementById('employee-fields');
            const productsSection = document.getElementById('products-section');
            const debtorSectionTitle = document.getElementById('debtor-section-title');

            if (currentDebtType === 'product') {
                customerFields.classList.remove('d-none');
                employeeFields.classList.add('d-none');
                productsSection.style.display = 'block';
                debtorSectionTitle.textContent = 'Informações do Cliente';

                // Limpar campos de funcionário
                clearEmployeeFields();
            } else {
                customerFields.classList.add('d-none');
                employeeFields.classList.remove('d-none');
                productsSection.style.display = 'none';
                debtorSectionTitle.textContent = 'Informações do Funcionário';

                // Limpar campos de cliente e produtos
                clearCustomerFields();
                clearProductsCart();
            }
        }

        // Atualizar seleção visual do tipo de dívida
        function updateDebtTypeSelection() {
            const options = document.querySelectorAll('.debt-type-option');
            options.forEach(option => {
                const type = option.dataset.type;
                if (type === currentDebtType) {
                    option.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
                } else {
                    option.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
                }
            });
        }

        // Configurar select de funcionários
        function initializeEmployeeSelect() {
            const employeeSelect = document.getElementById('employee-select');
            employeeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    document.getElementById('employee-name').value = selectedOption.dataset.name || '';
                    document.getElementById('employee-phone').value = selectedOption.dataset.phone || '';
                } else {
                    document.getElementById('employee-name').value = '';
                    document.getElementById('employee-phone').value = '';
                }
            });
        }

        // Definir datas padrão
        function setDefaultDates() {
            const today = new Date().toISOString().split('T')[0];
            const debtDateFields = ['debt-date', 'debt-date-money'];
            debtDateFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field && !field.value) {
                    field.value = today;
                }
            });
        }

        // Limpar campos de cliente
        function clearCustomerFields() {
            document.getElementById('customer-name').value = '';
            document.getElementById('customer-phone').value = '';
            document.getElementById('customer-document').value = '';
        }

        // Limpar campos de funcionário
        function clearEmployeeFields() {
            document.getElementById('employee-select').value = '';
            document.getElementById('employee-name').value = '';
            document.getElementById('employee-phone').value = '';
            document.getElementById('employee-document').value = '';
            document.getElementById('debt-amount').value = '';
        }

        // Limpar carrinho de produtos
        function clearProductsCart() {
            productsCart = [];
            updateProductsCart();
        }

        // Função para abrir offcanvas de nova dívida
        function openCreateDebtOffcanvas(debtType = 'product') {
            resetDebtForm();

            // Definir tipo de dívida
            currentDebtType = debtType;
            const radio = document.getElementById(`debt-type-${debtType}`);
            if (radio) {
                radio.checked = true;
            }

            toggleDebtTypeFields();
            updateDebtTypeSelection();

            document.getElementById('form-title').textContent = 'Nova Dívida';
            document.getElementById('form-method').value = 'POST';
            document.getElementById('debt-form').action = "{{ route('debts.store') }}";

            setDefaultDates();

            const offcanvas = new bootstrap.Offcanvas(document.getElementById('debtFormOffcanvas'));
            offcanvas.show();
        }

        // Resetar formulário
        function resetDebtForm() {
            document.getElementById('debt-form').reset();
            document.getElementById('debt-id').value = '';
            productsCart = [];
            updateProductsCart();
            clearValidation();

            // Reabilitar campos
            document.getElementById('product-select').disabled = false;
            document.querySelector('button[onclick="addProductToCart()"]').disabled = false;
        }

        // Função para adicionar produto ao carrinho
        function addProductToCart() {
            const select = document.getElementById('product-select');
            const productId = select.value;

            if (!productId) {
                showToast('Selecione um produto ou serviço', 'warning');
                return;
            }

            const option = select.options[select.selectedIndex];
            const product = {
                product_id: parseInt(productId),
                name: option.dataset.name,
                type: option.dataset.type,
                unit: option.dataset.unit || 'unid',
                unit_price: parseFloat(option.dataset.price),
                stock: parseInt(option.dataset.stock) || 0,
                quantity: 1
            };

            // Verificar se já existe no carrinho
            const existing = productsCart.find(item => item.product_id === product.product_id);
            if (existing) {
                if (product.type === 'product') {
                    const newTotal = existing.quantity + 1;
                    if (newTotal > product.stock) {
                        showToast(`Estoque insuficiente. Máximo: ${product.stock}`, 'error');
                        return;
                    }
                }
                existing.quantity += 1;
            } else {
                if (product.type === 'product' && product.stock < 1) {
                    showToast(`${product.name} está sem estoque`, 'error');
                    return;
                }
                productsCart.push(product);
            }

            updateProductsCart();
            select.value = '';
        }

        // Função para remover produto do carrinho
        function removeCartProduct(index) {
            productsCart.splice(index, 1);
            updateProductsCart();
        }

        // Atualizar carrinho de produtos
        function updateProductsCart() {
            const tbody = document.getElementById('products-cart-items');
            const totalEl = document.getElementById('products-cart-total');
            let total = 0;

            tbody.innerHTML = '';
            productsCart.forEach((item, index) => {
                const row = document.createElement('tr');
                const itemTotal = item.unit_price * item.quantity;
                total += itemTotal;

                row.innerHTML = `
            <td>
                <div class="fw-semibold">${item.name}</div>
                <small class="text-muted">${item.type === 'product' ? 'Produto' : 'Serviço'}</small>
            </td>
            <td class="text-center">
                <div class="d-flex align-items-center justify-content-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="decreaseProductQuantity(${index})">-</button>
                    <input type="number" class="form-control form-control-sm text-center mx-1" value="${item.quantity}" min="1" max="${item.type === 'product' ? item.stock : '999'}" style="width: 60px;" onchange="updateProductQuantity(${index}, this.value)">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="increaseProductQuantity(${index})">+</button>
                </div>
            </td>
            <td class="text-end">MT ${item.unit_price.toFixed(2).replace('.', ',')}</td>
            <td class="text-end fw-semibold">MT ${itemTotal.toFixed(2).replace('.', ',')}</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeCartProduct(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
                tbody.appendChild(row);
            });

            totalEl.textContent = `MT ${total.toFixed(2).replace('.', ',')}`;
            document.getElementById('products-json').value = JSON.stringify(productsCart);
        }

        // Funções para quantidade de produtos
        function increaseProductQuantity(index) {
            const item = productsCart[index];
            if (item.type === 'product' && item.quantity >= item.stock) {
                showToast(`Estoque máximo atingido: ${item.stock}`, 'warning');
                return;
            }
            item.quantity += 1;
            updateProductsCart();
        }

        function decreaseProductQuantity(index) {
            if (productsCart[index].quantity > 1) {
                productsCart[index].quantity -= 1;
                updateProductsCart();
            }
        }

        function updateProductQuantity(index, value) {
            const qty = parseInt(value);
            if (isNaN(qty) || qty < 1) return;

            const item = productsCart[index];
            if (item.type === 'product' && qty > item.stock) {
                showToast(`Estoque insuficiente. Máximo: ${item.stock}`, 'error');
                document.querySelector(`input[onchange="updateProductQuantity(${index}, this.value)"]`).value = item
                    .quantity;
                return;
            }
            item.quantity = qty;
            updateProductsCart();
        }

        // Função para visualizar detalhes da dívida
        function viewDebtDetails(debtId) {
            const content = document.getElementById('debt-view-content');
            content.innerHTML =
                '<div class="text-center py-5"><div class="loading-spinner"></div><p class="text-muted mt-3">Carregando...</p></div>';

            const offcanvas = new bootstrap.Offcanvas(document.getElementById('debtViewOffcanvas'));
            offcanvas.show();

            fetch(`/debts/${debtId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        content.innerHTML = data.html;
                    } else {
                        content.innerHTML =
                            '<div class="alert alert-danger">Erro ao carregar detalhes da dívida.</div>';
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    content.innerHTML = '<div class="alert alert-danger">Erro de conexão ao carregar detalhes.</div>';
                });
        }

        // Função para editar dívida
        function openEditDebtOffcanvas(debtId) {
            resetDebtForm();
            document.getElementById('form-title').textContent = 'Editar Dívida';
            document.getElementById('form-method').value = 'PUT';
            document.getElementById('debt-form').action = `/debts/${debtId}`;
            document.getElementById('debt-id').value = debtId;

            // Carregar dados da dívida
            fetch(`/debts/${debtId}/edit-data`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const debt = data.data;
                        populateEditForm(debt);

                        const offcanvas = new bootstrap.Offcanvas(document.getElementById('debtFormOffcanvas'));
                        offcanvas.show();
                    } else {
                        showToast('Erro ao carregar dados da dívida', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showToast('Erro de conexão', 'error');
                });
        }

        // Preencher formulário de edição
        function populateEditForm(debt) {
            // Definir tipo de dívida
            currentDebtType = debt.debt_type;
            const radio = document.getElementById(`debt-type-${debt.debt_type}`);
            if (radio) {
                radio.checked = true;
            }

            toggleDebtTypeFields();
            updateDebtTypeSelection();

            // Preencher campos comuns
            document.getElementById('description').value = debt.description;
            document.getElementById('notes').value = debt.notes || '';

            if (debt.debt_type === 'product') {
                // Preencher campos de cliente
                document.getElementById('customer-name').value = debt.customer_name;
                document.getElementById('customer-phone').value = debt.customer_phone || '';
                document.getElementById('customer-document').value = debt.customer_document || '';
                document.getElementById('debt-date').value = debt.debt_date;
                document.getElementById('due-date').value = debt.due_date || '';

                // Carregar produtos (apenas visualização - não editáveis)
                if (debt.items && debt.items.length > 0) {
                    productsCart = debt.items.map(item => ({
                        product_id: item.product_id,
                        name: item.product.name,
                        type: item.product.type,
                        unit: item.product.unit || 'unid',
                        unit_price: parseFloat(item.unit_price),
                        stock: item.product.stock_quantity || 0,
                        quantity: item.quantity
                    }));
                    updateProductsCart();

                    // Desabilitar edição de produtos
                    disableProductEditing();
                }
            } else {
                // Preencher campos de funcionário
                if (debt.employee_id) {
                    document.getElementById('employee-select').value = debt.employee_id;
                }
                document.getElementById('employee-name').value = debt.employee_name;
                document.getElementById('employee-phone').value = debt.employee_phone || '';
                document.getElementById('employee-document').value = debt.employee_document || '';
                document.getElementById('debt-date-money').value = debt.debt_date;
                document.getElementById('due-date-money').value = debt.due_date || '';
            }
        }

        // Desabilitar edição de produtos para dívidas existentes
        function disableProductEditing() {
            document.getElementById('product-select').disabled = true;
            document.querySelector('button[onclick="addProductToCart()"]').disabled = true;
            document.querySelectorAll('#products-cart-items button').forEach(btn => btn.disabled = true);
            document.querySelectorAll('#products-cart-items input').forEach(input => input.disabled = true);
        }

        // Validar formulário de dívida
        function validateDebtForm() {
            clearValidation();
            let isValid = true;

            const description = document.getElementById('description').value.trim();

            if (!description) {
                showFieldError('description', 'Descrição é obrigatória');
                isValid = false;
            }

            if (currentDebtType === 'product') {
                const customerName = document.getElementById('customer-name').value.trim();
                const debtDate = document.getElementById('debt-date').value;

                if (!customerName) {
                    showFieldError('customer-name', 'Nome do cliente é obrigatório');
                    isValid = false;
                }

                if (!debtDate) {
                    showFieldError('debt-date', 'Data da dívida é obrigatória');
                    isValid = false;
                }

                if (productsCart.length === 0) {
                    showToast('Adicione pelo menos um produto ao carrinho', 'warning');
                    isValid = false;
                }

                const dueDate = document.getElementById('due-date').value;
                if (dueDate && debtDate && new Date(dueDate) < new Date(debtDate)) {
                    showFieldError('due-date', 'Data de vencimento deve ser posterior à data da dívida');
                    isValid = false;
                }
            } else {
                const employeeId = document.getElementById('employee-select').value;
                const employeeName = document.getElementById('employee-name').value.trim();
                const debtAmount = document.getElementById('debt-amount').value;
                const debtDate = document.getElementById('debt-date-money').value;

                if (!employeeId) {
                    showFieldError('employee-select', 'Funcionário é obrigatório');
                    isValid = false;
                }

                if (!employeeName) {
                    showFieldError('employee-name', 'Nome é obrigatório');
                    isValid = false;
                }

                if (!debtAmount || parseFloat(debtAmount) <= 0) {
                    showFieldError('debt-amount', 'Valor da dívida é obrigatório e deve ser maior que zero');
                    isValid = false;
                }

                if (!debtDate) {
                    showFieldError('debt-date-money', 'Data da dívida é obrigatória');
                    isValid = false;
                }

                const dueDate = document.getElementById('due-date-money').value;
                if (dueDate && debtDate && new Date(dueDate) < new Date(debtDate)) {
                    showFieldError('due-date-money', 'Data de vencimento deve ser posterior à data da dívida');
                    isValid = false;
                }
            }

            return isValid;
        }

        // Submit do formulário de dívida
        document.getElementById('debt-form').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateDebtForm()) return;

            const submitBtn = document.getElementById('save-debt-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';

            // Sincronizar datas baseado no tipo
            if (currentDebtType === 'money') {
                document.getElementById('debt-date').value = document.getElementById('debt-date-money').value;
                document.getElementById('due-date').value = document.getElementById('due-date-money').value;
            }

            const formData = new FormData(this);
            const url = this.action;

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Offcanvas.getInstance(document.getElementById('debtFormOffcanvas')).hide();
                        showToast(data.message || 'Dívida salva com sucesso!', 'success');

                        if (data.redirect) {
                            setTimeout(() => window.location.href = data.redirect, 1000);
                        } else {
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const fieldId = field.replace('_', '-');
                                showFieldError(fieldId, data.errors[field][0]);
                            });
                        }
                        showToast(data.message || 'Erro ao salvar dívida.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showToast('Erro de conexão.', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        });

        // Função para abrir offcanvas de pagamento
        function openPaymentOffcanvas(debtId, debtorName, debtType, remainingAmount, isProductDebt) {
            document.getElementById('payment-debt-id').value = debtId;
            document.getElementById('payment-debtor-name').textContent = debtorName;
            document.getElementById('payment-debt-type').textContent = debtType;
            document.getElementById('payment-remaining-amount').textContent =
                `MT ${remainingAmount.toFixed(2).replace('.', ',')}`;
            document.getElementById('payment-amount').max = remainingAmount;
            document.getElementById('max-amount-text').textContent = `MT ${remainingAmount.toFixed(2).replace('.', ',')}`;
            document.getElementById('payment-form').action = `/debts/${debtId}/add-payment`;
            document.getElementById('payment-form').reset();
            document.querySelector('input[name="payment_date"]').value = new Date().toISOString().split('T')[0];

            // Mostrar opção de criar venda apenas para dívidas de produtos
            const createSaleOption = document.getElementById('create-sale-option');
            if (isProductDebt) {
                createSaleOption.style.display = 'block';
                document.getElementById('create-sale-checkbox').checked = true;
            } else {
                createSaleOption.style.display = 'none';
            }

            clearValidation();

            const offcanvas = new bootstrap.Offcanvas(document.getElementById('paymentOffcanvas'));
            offcanvas.show();
        }

        // Validar formulário de pagamento
        function validatePaymentForm() {
            clearValidation();
            let isValid = true;

            const amount = parseFloat(document.getElementById('payment-amount').value);
            const maxAmount = parseFloat(document.getElementById('payment-amount').max);
            const paymentMethod = document.querySelector('select[name="payment_method"]').value;
            const paymentDate = document.querySelector('input[name="payment_date"]').value;

            if (!amount || amount <= 0) {
                showFieldError('payment-amount', 'Valor do pagamento é obrigatório');
                isValid = false;
            } else if (amount > maxAmount) {
                showFieldError('payment-amount', `Valor máximo: MT ${maxAmount.toFixed(2)}`);
                isValid = false;
            }

            if (!paymentMethod) {
                showFieldError('select[name="payment_method"]', 'Forma de pagamento é obrigatória');
                isValid = false;
            }

            if (!paymentDate) {
                showFieldError('input[name="payment_date"]', 'Data do pagamento é obrigatória');
                isValid = false;
            } else if (new Date(paymentDate) > new Date()) {
                showFieldError('input[name="payment_date"]', 'Data não pode ser futura');
                isValid = false;
            }

            return isValid;
        }

        // Submit do formulário de pagamento
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validatePaymentForm()) return;

            const formData = new FormData(this);
            const url = this.action;

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Offcanvas.getInstance(document.getElementById('paymentOffcanvas')).hide();
                        showToast(data.message || 'Pagamento registrado com sucesso!', 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const selector = field === 'payment_method' ?
                                    'select[name="payment_method"]' :
                                    `input[name="${field}"], #${field.replace('_', '-')}`;
                                showFieldError(selector, data.errors[field][0]);
                            });
                        }
                        showToast(data.message || 'Erro ao registrar pagamento.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showToast('Erro de conexão.', 'error');
                });
        });

        // Função para cancelar dívida
        function cancelDebt(debtId) {
            if (!confirm(
                    'Tem certeza que deseja cancelar esta dívida? Para dívidas de produtos, o estoque será devolvido.')) {
                return;
            }

            const url = `/debts/${debtId}/cancel`;
            const button = document.querySelector(`[onclick="cancelDebt(${debtId})"]`);

            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Dívida cancelada com sucesso!', 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showToast(data.message || 'Erro ao cancelar dívida.', 'error');
                    }
                })
                .catch(() => showToast('Erro de conexão.', 'error'))
                .finally(() => {
                    button.disabled = false;
                    button.innerHTML = originalText;
                });
        }

        // Configurar filtros automáticos
        function initializeFilters() {
            const form = document.getElementById('filters-form');
            const selects = form.querySelectorAll('select');
            selects.forEach(select => {
                select.addEventListener('change', () => form.submit());
            });
        }

        // Funções utilitárias
        function showFieldError(fieldSelector, message) {
            const field = document.querySelector(fieldSelector) || document.getElementById(fieldSelector);
            if (field) {
                field.classList.add('is-invalid');
                const feedback = field.parentNode.querySelector('.invalid-feedback') ||
                    field.nextElementSibling?.classList.contains('invalid-feedback') ?
                    field.nextElementSibling : null;
                if (feedback) {
                    feedback.textContent = message;
                }
            }
        }

        function clearValidation() {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }

        function showToast(message, type = 'info') {
            const bgClass = type === 'success' ? 'bg-success' :
                type === 'error' ? 'bg-danger' :
                type === 'warning' ? 'bg-warning' : 'bg-primary';

            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white ${bgClass} border-0`;
            toast.style = 'position: fixed; top: 20px; right: 20px; z-index: 10000; width: 350px;';
            toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast, {
                delay: 5000
            });
            bsToast.show();

            toast.addEventListener('hidden.bs.toast', () => toast.remove());
        }
    </script>
@endpush
@push('styles')
    <style>
        /* Cards de estatísticas */
        .stats-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stats-card.primary {
            border-left-color: #1e3a8a;
        }

        .stats-card.warning {
            border-left-color: #ea580c;
        }

        .stats-card.danger {
            border-left-color: #dc2626;
        }

        .stats-card.success {
            border-left-color: #059669;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* Animação de fade-in */
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

        /* Opções de tipo de dívida */
        .debt-type-option {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb !important;
        }

        .debt-type-option:hover {
            border-color: #6366f1 !important;
            background-color: #f8fafc !important;
        }

        .debt-type-option.border-primary {
            border-color: #1e40af !important;
            background-color: rgba(59, 130, 246, 0.1) !important;
        }

        /* Tabelas responsivas */
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
            border-top: none;
            font-size: 0.875rem;
        }

        .table td {
            vertical-align: middle;
            font-size: 0.875rem;
        }

        /* Badges */
        .badge {
            font-size: 0.75em;
            font-weight: 500;
            padding: 0.375em 0.75em;
        }

        /* Loading spinner */
        .loading-spinner {
            width: 30px;
            height: 30px;
            border: 3px solid #f3f4f6;
            border-top: 3px solid #0d6efd;
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

        /* Offcanvas customization */
        .offcanvas {
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
        }

        .offcanvas-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .offcanvas-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }

        /* Formulários */
        .form-control:focus,
        .form-select:focus {
            border-color: #1e40af;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }

        /* Carrinho de produtos */
        #products-cart-table {
            font-size: 0.875rem;
        }

        #products-cart-table input[type="number"] {
            border: 1px solid #ced4da;
            font-size: 0.75rem;
        }

        #products-cart-table .btn-sm {
            padding: 0.125rem 0.25rem;
            font-size: 0.75rem;
            line-height: 1.2;
        }

        /* Botões de grupo */
        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 0.2rem;
        }

        .btn-group .btn+.btn {
            margin-left: -1px;
        }

        /* Progress bars */
        .progress {
            background-color: #e9ecef;
            border-radius: 0.375rem;
            overflow: hidden;
        }

        .progress-bar {
            transition: width 0.6s ease;
        }

        /* Cards personalizados */
        .card {
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            padding: 1rem 1.25rem;
        }

        .card-header h5,
        .card-header h6 {
            margin-bottom: 0;
            color: #495057;
        }

        /* Alertas customizados */
        .alert {
            border: 1px solid transparent;
            border-radius: 0.375rem;
            padding: 0.75rem 1.25rem;
        }

        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        /* Texto e tipografia */
        .text-muted {
            color: #6c757d !important;
        }

        .fw-semibold {
            font-weight: 600 !important;
        }

        .small {
            font-size: 0.875em;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .offcanvas-end {
                width: 100% !important;
            }

            .stats-card {
                margin-bottom: 1rem;
            }

            .btn-group-sm .btn {
                padding: 0.2rem 0.4rem;
                font-size: 0.7rem;
            }

            .table-responsive {
                font-size: 0.8rem;
            }
        }

        /* Estados especiais */
        .table-warning {
            --bs-table-accent-bg: #fff3cd;
            --bs-table-striped-bg: #fcf4d6;
            --bs-table-hover-bg: #faeeba;
            --bs-table-border-color: #f5e5a3;
        }

        /* Dropdown menus */
        .dropdown-menu {
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            transition: background-color 0.15s ease-in-out;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        /* Toasts */
        .toast {
            border: 0;
            border-radius: 0.375rem;
            backdrop-filter: blur(10px);
        }

        /* Customizações específicas para tipos de dívida */
        .debt-type-product .text-primary {
            color: #1e40af !important;
        }

        .debt-type-money .text-success {
            color: #059669 !important;
        }

        /* Estados de dívidas */
        .debt-active {
            border-left: 3px solid #fbbf24;
        }

        .debt-overdue {
            border-left: 3px solid #ef4444;
            background-color: #fef2f2;
        }

        .debt-paid {
            border-left: 3px solid #10b981;
            background-color: #f0fdf4;
        }

        .debt-cancelled {
            border-left: 3px solid #6b7280;
            background-color: #f9fafb;
        }
    </style>
@endpush
