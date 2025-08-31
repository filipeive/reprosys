@extends('layouts.app')

@section('title', 'Dívidas')
@section('page-title', 'Gestão de Dívidas')
@section('title-icon', 'fa-credit-card')
@section('breadcrumbs')
    <li class="breadcrumb-item active">Dívidas</li>
@endsection

@section('content')
    <!-- Offcanvas para Criar Dívida com Itens -->
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
                                <label class="form-label fw-semibold">Nome *</label>
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
                                <label class="form-label fw-semibold">Data da Dívida *</label>
                                <input type="date" class="form-control" name="debt_date" id="debt-date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Vencimento</label>
                                <input type="date" class="form-control" name="due_date" id="due-date">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seleção de Itens -->
                <div class="p-4 border-bottom">
                    <h6 class="mb-3"><i class="fas fa-shopping-cart me-2"></i> Itens da Dívida</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <select class="form-select" id="product-select">
                                <option value="">Selecione um produto ou serviço...</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                        data-type="{{ $product->type }}" data-unit="{{ $product->unit }}"
                                        data-price="{{ $product->selling_price }}"
                                        data-stock="{{ $product->stock_quantity }}">
                                        {{ $product->name }} - MT {{ number_format($product->selling_price, 2, ',', '.') }}
                                        @if ($product->type === 'product')
                                            (Estoque: {{ $product->stock_quantity }} {{ $product->unit }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary w-100" onclick="addItemToCart()">
                                <i class="fas fa-plus me-2"></i>Adicionar
                            </button>
                        </div>
                    </div>

                    <!-- Carrinho -->
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" id="cart-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto/Serviço</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Preço</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items">
                                <!-- Itens serão adicionados aqui -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="text-end fw-bold" id="cart-total">MT 0,00</td>
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
                                <label class="form-label fw-semibold">Descrição *</label>
                                <textarea class="form-control" name="description" id="description" rows="2" required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Observações</label>
                                <textarea class="form-control" name="notes" id="notes" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
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
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
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
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
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
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
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
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
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
                            <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Parcial
                            </option>
                            <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Vencida
                            </option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paga</option>
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
                            <tr class="{{ $debt->is_overdue ? 'table-warning' : '' }}">
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
                                    @if ($debt->paid_amount > 0)
                                        <span class="text-success fw-bold">MT
                                            {{ number_format($debt->paid_amount, 2, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">MT 0,00</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($debt->remaining_amount > 0)
                                        <strong class="text-{{ $debt->is_overdue ? 'danger' : 'warning' }}">
                                            MT {{ number_format($debt->remaining_amount, 2, ',', '.') }}
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
                                                {{ abs($debt->days_overdue) }} dias
                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted">Sem prazo</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $debt->status_badge }}">
                                        {{ $debt->status_text }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info"
                                            onclick="viewDebtDetails({{ $debt->id }})" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-warning"
                                            onclick="openEditDebtOffcanvas({{ $debt->id }})" title="Editar Dívida">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if ($debt->canAddPayment())
                                            <button type="button" class="btn btn-outline-success"
                                                onclick="openPaymentOffcanvas({{ $debt->id }}, '{{ $debt->customer_name }}', {{ $debt->remaining_amount }})"
                                                title="Registrar Pagamento">
                                                <i class="fas fa-money-bill"></i>
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
        let cartItems = [];

        // Função para adicionar item ao carrinho
        function addItemToCart() {
            const select = document.getElementById('product-select');
            const productId = select.value;
            if (!productId) {
                showToast('Selecione um produto ou serviço', 'warning');
                return;
            }

            const option = select.options[select.selectedIndex];
            const product = {
                id: productId,
                name: option.dataset.name,
                type: option.dataset.type,
                unit: option.dataset.unit || 'unid',
                price: parseFloat(option.dataset.price),
                stock: parseInt(option.dataset.stock) || 0,
                quantity: 1
            };

            // Verificar se já existe no carrinho
            const existing = cartItems.find(item => item.id === productId);
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
                cartItems.push(product);
            }

            updateCart();
            select.value = '';
        }

        // Função para remover item do carrinho
        function removeCartItem(index) {
            cartItems.splice(index, 1);
            updateCart();
        }

        // Atualizar o carrinho
        function updateCart() {
            const tbody = document.getElementById('cart-items');
            const totalEl = document.getElementById('cart-total');
            let total = 0;

            tbody.innerHTML = '';
            cartItems.forEach((item, index) => {
                const row = document.createElement('tr');
                const itemTotal = item.price * item.quantity;
                total += itemTotal;

                row.innerHTML = `
            <td>${item.name}</td>
            <td class="text-center">
                <div class="d-flex align-items-center justify-content-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="decreaseQuantity(${index})">-</button>
                    <input type="number" class="form-control form-control-sm text-center mx-1" value="${item.quantity}" min="1" max="${item.type === 'product' ? item.stock : '999'}" style="width: 60px;" onchange="updateQuantity(${index}, this.value)">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="increaseQuantity(${index})">+</button>
                </div>
            </td>
            <td class="text-end">MT ${item.price.toFixed(2).replace('.', ',')}</td>
            <td class="text-end">MT ${itemTotal.toFixed(2).replace('.', ',')}</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeCartItem(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
                tbody.appendChild(row);
            });

            totalEl.textContent = `MT ${total.toFixed(2).replace('.', ',')}`;
            document.getElementById('original-amount').value = total;
        }

        // Funções para quantidade
        function increaseQuantity(index) {
            const item = cartItems[index];
            if (item.type === 'product' && item.quantity >= item.stock) {
                showToast(`Estoque máximo atingido: ${item.stock}`, 'warning');
                return;
            }
            item.quantity += 1;
            updateCart();
        }

        function decreaseQuantity(index) {
            if (cartItems[index].quantity > 1) {
                cartItems[index].quantity -= 1;
                updateCart();
            }
        }

        function updateQuantity(index, value) {
            const qty = parseInt(value);
            if (isNaN(qty) || qty < 1) return;
            const item = cartItems[index];
            if (item.type === 'product' && qty > item.stock) {
                showToast(`Estoque insuficiente. Máximo: ${item.stock}`, 'error');
                item.quantity = item.stock;
            } else {
                item.quantity = qty;
            }
            updateCart();
        }
        // Função para visualizar detalhes da dívida
        function viewDebtDetails(debtId) {
            const content = document.getElementById('debt-view-content');
            content.innerHTML =
                '<div class="text-center py-5"><div class="loading-spinner"></div><p class="text-muted mt-3">Carregando...</p></div>';
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('debtViewOffcanvas'));
            offcanvas.show();
            fetch(`/debts/${debtId}/details`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        content.innerHTML = data.html;
                    } else {
                        content.innerHTML = '<div class="alert alert-danger">Erro ao carregar detalhes.</div>';
                    }
                })
                .catch(() => {
                    content.innerHTML = '<div class="alert alert-danger">Erro de conexão.</div>';
                });
        }

        // Função para abrir o offcanvas de nova dívida
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

            fetch(`/debts/${debtId}/edit-data`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const d = data.data;
                        document.getElementById('customer-name').value = d.customer_name;
                        document.getElementById('customer-phone').value = d.customer_phone || '';
                        document.getElementById('debt-date').value = d.debt_date;
                        document.getElementById('due-date').value = d.due_date || '';
                        document.getElementById('description').value = d.description;
                        document.getElementById('notes').value = d.notes || '';
                        document.getElementById('original-amount').value = d.original_amount;

                        // Carregar itens no carrinho
                        cartItems = d.items.map(item => ({
                            id: item.product_id,
                            name: item.product_name,
                            type: item.type,
                            unit: item.unit,
                            price: parseFloat(item.price),
                            stock: item.stock || 0,
                            quantity: item.quantity
                        }));
                        updateCart();

                        const offcanvas = new bootstrap.Offcanvas(document.getElementById('debtFormOffcanvas'));
                        offcanvas.show();
                    } else {
                        showToast('Erro ao carregar dados da dívida', 'error');
                    }
                })
                .catch(() => showToast('Erro de conexão', 'error'));
        }


        // Resetar formulário
        function resetDebtForm() {
            document.getElementById('debt-form').reset();
            document.getElementById('debt-id').value = '';
            clearValidation();
        }

        // Validar formulário
        function validateDebtForm() {
            clearValidation();
            let isValid = true;
            const name = document.getElementById('customer-name').value.trim();
            const amount = parseFloat(document.getElementById('original-amount').value);
            const date = document.getElementById('debt-date').value;
            const description = document.getElementById('description').value.trim();

            if (!name) {
                showFieldError('customer-name', 'Nome é obrigatório');
                isValid = false;
            }
            if (!amount || amount <= 0) {
                showFieldError('original-amount', 'Valor deve ser maior que zero');
                isValid = false;
            }
            if (!date) {
                showFieldError('debt-date', 'Data é obrigatória');
                isValid = false;
            }
            if (!description) {
                showFieldError('description', 'Descrição é obrigatória');
                isValid = false;
            }

            return isValid;
        }

        // Submit do formulário de dívida
        document.getElementById('debt-form').addEventListener('submit', function(e) {
            e.preventDefault();
            if (!validateDebtForm()) return;
            if (cartItems.length === 0) {
                showToast('Adicione pelo menos um item ao carrinho', 'warning');
                return;
            }

            const submitBtn = document.getElementById('save-debt-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('_method', document.getElementById('form-method').value);
            formData.append('customer_name', document.getElementById('customer-name').value);
            formData.append('customer_phone', document.getElementById('customer-phone').value);
            formData.append('debt_date', document.getElementById('debt-date').value);
            formData.append('due_date', document.getElementById('due-date').value);
            formData.append('description', document.getElementById('description').value);
            formData.append('notes', document.getElementById('notes').value);
            formData.append('original_amount', document.getElementById('original-amount').value);
            formData.append('items', JSON.stringify(cartItems));

            const url = this.action;

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Offcanvas.getInstance(document.getElementById('debtFormOffcanvas')).hide();
                        showToast(data.message || 'Dívida salva com sucesso!', 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const selector = `#${field.replace('_', '-')}`;
                                showFieldError(selector, data.errors[field][0]);
                            });
                        }
                        showToast(data.message || 'Erro ao salvar dívida.', 'error');
                    }
                })
                .catch(() => showToast('Erro de conexão.', 'error'))
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        });

        // Função para abrir o offcanvas de pagamento
        function openPaymentOffcanvas(debtId, customerName, remainingAmount) {
            document.getElementById('payment-debt-id').value = debtId;
            document.getElementById('payment-customer-name').textContent = customerName;
            document.getElementById('payment-remaining-amount').textContent = `MT ${remainingAmount.toFixed(2)}`;
            document.getElementById('payment-amount').max = remainingAmount;
            document.getElementById('max-amount-text').textContent = `MT ${remainingAmount.toFixed(2)}`;
            document.getElementById('payment-form').action = `/debts/${debtId}/add-payment`;
            document.getElementById('payment-form').reset();
            clearValidation();
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('paymentOffcanvas'));
            offcanvas.show();
        }

        // Validar pagamento
        function validatePaymentForm() {
            clearValidation();
            let isValid = true;
            const amount = parseFloat(document.getElementById('payment-amount').value);
            const maxAmount = parseFloat(document.getElementById('payment-amount').max);
            const paymentMethod = document.querySelector('select[name="payment_method"]').value;
            const paymentDate = document.querySelector('input[name="payment_date"]').value;

            if (!amount || amount <= 0) {
                showFieldError('payment-amount', 'Valor é obrigatório');
                isValid = false;
            } else if (amount > maxAmount) {
                showFieldError('payment-amount', `Máximo: MT ${maxAmount.toFixed(2)}`);
                isValid = false;
            }

            if (!paymentMethod) {
                showFieldError('select[name="payment_method"]', 'Forma de pagamento é obrigatória');
                isValid = false;
            }

            if (!paymentDate) {
                showFieldError('input[name="payment_date"]', 'Data é obrigatória');
                isValid = false;
            } else if (new Date(paymentDate) > new Date()) {
                showFieldError('input[name="payment_date"]', 'Data não pode ser futura');
                isValid = false;
            }

            return isValid;
        }

        // Submit do pagamento
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
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Offcanvas.getInstance(document.getElementById('paymentOffcanvas')).hide();
                        showToast(data.message || 'Pago com sucesso!', 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const selector = field === 'payment_method' ?
                                    'select[name="payment_method"]' : `#${field.replace('_', '-')}`;
                                showFieldError(selector, data.errors[field][0]);
                            });
                        }
                        showToast(data.message || 'Erro ao pagar.', 'error');
                    }
                })
                .catch(() => showToast('Erro de conexão.', 'error'));
        });

        // Funções de utilidade
        function showFieldError(fieldSelector, message) {
            const field = document.querySelector(fieldSelector);
            if (field) {
                field.classList.add('is-invalid');
                const feedback = field.parentNode.querySelector('.invalid-feedback') || field.nextElementSibling;
                if (feedback) feedback.textContent = message;
            }
        }

        function clearValidation() {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }

        function showToast(message, type = 'info') {
            const bg = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-primary';
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white ${bg} border-0`;
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

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.role = 'alert';
            toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>`;
            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast, {
                delay: 3000
            });
            bsToast.show();
            toast.addEventListener('hidden.bs.toast', () => toast.remove());
        }
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
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush
