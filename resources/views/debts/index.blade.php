@extends('layouts.app')

@section('title', 'Dívidas')
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

                <!-- Informações do Cliente -->
                <div class="p-4 border-bottom bg-light">
                    <h6 class="mb-3"><i class="fas fa-user me-2"></i> Informações do Cliente</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nome do Cliente *</label>
                                <input type="text" class="form-control" name="customer_name" id="customer-name" required>
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
                                <input type="date" class="form-control" name="debt_date" id="debt-date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Data de Vencimento</label>
                                <input type="date" class="form-control" name="due_date" id="due-date">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seleção de Produtos -->
                <div class="p-4 border-bottom">
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
                                        {{ $product->name }} - MT {{ number_format($product->selling_price, 2, ',', '.') }}
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
                    <div><strong>Cliente:</strong> <span id="payment-customer-name"></span></div>
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
            <p class="text-muted mb-0">Controle e recebimento de dívidas da reprografia</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" onclick="openCreateDebtOffcanvas()">
                <i class="fas fa-plus me-2"></i> Nova Dívida
            </button>
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
                            <small class="text-muted">valor pendente</small>
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
                            <h6 class="text-muted mb-2 fw-semibold">Dívidas Ativas</h6>
                            <h3 class="mb-0 text-primary fw-bold">{{ $stats['count_active'] }}</h3>
                            <small class="text-muted">em aberto</small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-file-invoice-dollar fa-2x"></i>
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
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Pagas Este Mês</h6>
                            <h3 class="mb-0 text-success fw-bold">{{ $stats['count_paid_this_month'] }}</h3>
                            <small class="text-muted">recebimentos</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                    <div class="col-md-3">
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
                        <label class="form-label fw-semibold">Cliente</label>
                        <input type="text" class="form-control" name="customer" placeholder="Nome do cliente..."
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
                    <div class="col-md-2">
                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" name="overdue_only" value="1"
                                {{ request('overdue_only') ? 'checked' : '' }}>
                            <label class="form-check-label">Apenas Vencidas</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Filtrar
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
                            <th>Cliente</th>
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
                            <tr
                                class="{{ $debt->status === 'overdue' || ($debt->status === 'active' && $debt->due_date && $debt->due_date->isPast()) ? 'table-warning' : '' }}">
                                <td><strong class="text-primary">#{{ $debt->id }}</strong></td>
                                <td>
                                    <div class="fw-semibold">{{ $debt->customer_name }}</div>
                                    @if ($debt->customer_phone)
                                        <small class="text-muted">
                                            <i class="fas fa-phone me-1"></i> {{ $debt->customer_phone }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ Str::limit($debt->description, 50) }}</div>
                                    @if ($debt->sale_id)
                                        <small class="text-muted">Venda #{{ $debt->sale_id }}</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong>MT {{ number_format($debt->original_amount, 2, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    @php
                                        $paidAmount = $debt->original_amount - $debt->remaining_amount;
                                    @endphp
                                    @if ($paidAmount > 0)
                                        <span class="text-success fw-bold">MT
                                            {{ number_format($paidAmount, 2, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">MT 0,00</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($debt->remaining_amount > 0)
                                        @php
                                            $isOverdue =
                                                $debt->status === 'overdue' ||
                                                ($debt->status === 'active' &&
                                                    $debt->due_date &&
                                                    $debt->due_date->isPast());
                                        @endphp
                                        <strong class="text-{{ $isOverdue ? 'danger' : 'warning' }}">
                                            MT {{ number_format($debt->remaining_amount, 2, ',', '.') }}
                                        </strong>
                                    @else
                                        <span class="text-success">MT 0,00</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($debt->due_date)
                                        @php
                                            $isOverdue =
                                                $debt->status === 'overdue' ||
                                                ($debt->status === 'active' && $debt->due_date->isPast());
                                            $daysOverdue = $isOverdue ? $debt->due_date->diffInDays(now()) : 0;
                                        @endphp
                                        <div class="{{ $isOverdue ? 'text-danger' : '' }}">
                                            {{ $debt->due_date->format('d/m/Y') }}
                                        </div>
                                        @if ($isOverdue)
                                            <small class="text-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                {{ $daysOverdue }} dias
                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted">Sem prazo</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusBadge = match ($debt->status) {
                                            'active' => 'bg-warning',
                                            'paid' => 'bg-success',
                                            'cancelled' => 'bg-secondary',
                                            'overdue' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                        $statusText = match ($debt->status) {
                                            'active' => 'Ativa',
                                            'paid' => 'Paga',
                                            'cancelled' => 'Cancelada',
                                            'overdue' => 'Vencida',
                                            default => ucfirst($debt->status),
                                        };
                                    @endphp
                                    <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info"
                                            onclick="viewDebtDetails({{ $debt->id }})" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if ($debt->status !== 'paid' && $debt->status !== 'cancelled')
                                            <button type="button" class="btn btn-outline-warning"
                                                onclick="openEditDebtOffcanvas({{ $debt->id }})"
                                                title="Editar Dívida">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        @if ($debt->status === 'active' && $debt->remaining_amount > 0)
                                            <button type="button" class="btn btn-outline-success"
                                                onclick="openPaymentOffcanvas({{ $debt->id }}, '{{ $debt->customer_name }}', {{ $debt->remaining_amount }})"
                                                title="Registrar Pagamento">
                                                <i class="fas fa-money-bill"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('debts.show', $debt) }}" class="btn btn-outline-primary btn-sm"
                                            title="Ver Página Completa">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        @if ($debt->status === 'active')
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
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-credit-card fa-3x mb-3 opacity-50"></i>
                                    <p>Nenhuma dívida encontrada.</p>
                                    <button type="button" class="btn btn-primary" onclick="openCreateDebtOffcanvas()">
                                        <i class="fas fa-plus me-2"></i> Registrar Primeira Dívida
                                    </button>
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
        let productsCart = [];

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

            // Atualizar campo hidden com os produtos em formato JSON
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

        // Função para abrir offcanvas de nova dívida
        function openCreateDebtOffcanvas() {
            resetDebtForm();
            document.getElementById('form-title').textContent = 'Nova Dívida';
            document.getElementById('form-method').value = 'POST';
            document.getElementById('debt-form').action = "{{ route('debts.store') }}";
            document.getElementById('debt-date').value = new Date().toISOString().split('T')[0];

            const offcanvas = new bootstrap.Offcanvas(document.getElementById('debtFormOffcanvas'));
            offcanvas.show();
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

                        // Preencher campos básicos
                        document.getElementById('customer-name').value = debt.customer_name;
                        document.getElementById('customer-phone').value = debt.customer_phone || '';
                        document.getElementById('customer-document').value = debt.customer_document || '';
                        document.getElementById('debt-date').value = debt.debt_date;
                        document.getElementById('due-date').value = debt.due_date || '';
                        document.getElementById('description').value = debt.description;
                        document.getElementById('notes').value = debt.notes || '';

                        // Carregar produtos no carrinho (apenas para visualização - produtos não editáveis após criação)
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

                            // Desabilitar edição de produtos para dívidas existentes
                            document.getElementById('product-select').disabled = true;
                            document.querySelector('button[onclick="addProductToCart()"]').disabled = true;
                            document.querySelectorAll('#products-cart-items button').forEach(btn => btn.disabled =
                            true);
                            document.querySelectorAll('#products-cart-items input').forEach(input => input.disabled =
                                true);
                        }

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

        // Resetar formulário
        function resetDebtForm() {
            document.getElementById('debt-form').reset();
            document.getElementById('debt-id').value = '';
            productsCart = [];
            updateProductsCart();
            clearValidation();

            // Reabilitar campos de produtos
            document.getElementById('product-select').disabled = false;
            document.querySelector('button[onclick="addProductToCart()"]').disabled = false;
        }

        // Validar formulário de dívida
        function validateDebtForm() {
            clearValidation();
            let isValid = true;

            const customerName = document.getElementById('customer-name').value.trim();
            const debtDate = document.getElementById('debt-date').value;
            const description = document.getElementById('description').value.trim();

            if (!customerName) {
                showFieldError('customer-name', 'Nome do cliente é obrigatório');
                isValid = false;
            }

            if (!debtDate) {
                showFieldError('debt-date', 'Data da dívida é obrigatória');
                isValid = false;
            }

            if (!description) {
                showFieldError('description', 'Descrição é obrigatória');
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
        function openPaymentOffcanvas(debtId, customerName, remainingAmount) {
            document.getElementById('payment-debt-id').value = debtId;
            document.getElementById('payment-customer-name').textContent = customerName;
            document.getElementById('payment-remaining-amount').textContent =
                `MT ${remainingAmount.toFixed(2).replace('.', ',')}`;
            document.getElementById('payment-amount').max = remainingAmount;
            document.getElementById('max-amount-text').textContent = `MT ${remainingAmount.toFixed(2).replace('.', ',')}`;
            document.getElementById('payment-form').action = `/debts/${debtId}/add-payment`;
            document.getElementById('payment-form').reset();
            document.querySelector('input[name="payment_date"]').value = new Date().toISOString().split('T')[0];
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
            if (!confirm('Tem certeza que deseja cancelar esta dívida? O estoque será devolvido.')) {
                return;
            }

            const url = `/debts/${debtId}/cancel`;
            const button = document.querySelector(`[onclick="cancelDebt(${debtId})"]`);

            // Desabilitar botão
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

        // Auto-submit nos filtros
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filters-form');
            const selects = form.querySelectorAll('select');
            selects.forEach(select => {
                select.addEventListener('change', () => form.submit());
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .stats-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
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
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
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

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

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

        .offcanvas {
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
        }

        .offcanvas-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }

        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
            border-top: none;
        }

        .badge {
            font-size: 0.75em;
            font-weight: 500;
        }

        #products-cart-table input[type="number"] {
            border: 1px solid #ced4da;
        }

        #products-cart-table .btn-sm {
            padding: 0.125rem 0.25rem;
            font-size: 0.75rem;
            line-height: 1.2;
        }
    </style>
@endpush
