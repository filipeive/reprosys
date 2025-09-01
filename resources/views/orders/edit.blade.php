
@extends('layouts.app')

@section('title', 'Pedidos')
@section('page-title', 'Gestão de Pedidos')
@section('title-icon', 'fa-clipboard-list')
@section('breadcrumbs')
    <li class="breadcrumb-item active">Pedidos</li>
@endsection

@section('content')
    <!-- Offcanvas para Criar/Editar Pedido -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="orderFormOffcanvas" style="width: 800px;">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-clipboard-list me-2"></i><span id="form-title">Novo Pedido</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <form id="order-form" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <input type="hidden" name="order_id" id="order-id">

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
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" name="customer_email" id="customer-email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Prioridade *</label>
                                <select class="form-select" name="priority" id="priority" required>
                                    <option value="">Selecione</option>
                                    <option value="low">Baixa</option>
                                    <option value="medium">Média</option>
                                    <option value="high">Alta</option>
                                    <option value="urgent">Urgente</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seleção de Itens -->
                <div class="p-4 border-bottom">
                    <h6 class="mb-3"><i class="fas fa-box me-2"></i> Itens do Pedido</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <select class="form-select" id="product-select">
                                <option value="">Selecione um produto ou serviço...</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                        data-price="{{ $product->selling_price }}">
                                        {{ $product->name }} - MT {{ number_format($product->selling_price, 2, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- status --}}
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">Selecione</option>
                                    <option value="pending">Pendente</option>
                                    <option value="in_progress">Em Andamento</option>
                                    <option value="completed">Concluído</option>
                                    <option value="delivered">Entregue</option>
                                </select>
                            </div>
                        </div>
                        {{-- /status --}}
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
                                    <th>Item</th>
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
                                    <td colspan="3" class="text-end fw-bold">Total Estimado:</td>
                                    <td class="text-end fw-bold" id="cart-total">MT 0,00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Detalhes do Pedido -->
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Descrição *</label>
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
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Valor Estimado *</label>
                                <div class="input-group">
                                    <span class="input-group-text">MT</span>
                                    <input type="number" step="0.01" class="form-control" name="estimated_amount"
                                        id="estimated-amount" required>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Sinal Recebido</label>
                                <div class="input-group">
                                    <span class="input-group-text">MT</span>
                                    <input type="number" step="0.01" class="form-control" name="advance_payment"
                                        id="advance-payment">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Data de Entrega</label>
                                <input type="date" class="form-control" name="delivery_date" id="delivery-date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 form-check mt-4">
                                <input type="checkbox" class="form-check-input" id="create-debt" name="create_debt">
                                <label class="form-check-label fw-semibold">Criar Dívida para o Restante</label>
                            </div>
                            <div class="mb-3" id="debt-due-date-container" style="display: none;">
                                <label class="form-label fw-semibold">Vencimento da Dívida</label>
                                <input type="date" class="form-control" name="debt_due_date" id="debt-due-date">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="offcanvas-footer p-3 border-top">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" form="order-form" class="btn btn-primary flex-fill" id="save-order-btn">
                    <i class="fas fa-save me-2"></i> Salvar Pedido
                </button>
            </div>
        </div>
    </div>

    <!-- Offcanvas para Visualizar Pedido -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="orderViewOffcanvas" style="width: 700px;">
        <div class="offcanvas-header bg-info text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-eye me-2"></i>Detalhes do Pedido
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body" id="order-view-content">
            <div class="text-center py-5">
                <div class="loading-spinner mb-3"></div>
                <p class="text-muted">Carregando detalhes...</p>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-clipboard-list me-2"></i>
                Gestão de Pedidos
            </h2>
            <p class="text-muted mb-0">Controle e acompanhamento de pedidos da reprografia</p>
        </div>
        <div class="d-flex gap-2">
            {{-- <button type="button" class="btn btn-success" onclick="openCreateOrderOffcanvas()">
                <i class="fas fa-plus me-2"></i> Novo Pedido
            </button> --}}
            <a href="{{ route('orders.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i> Novo Pedido
            </button>
            <a href="{{ route('orders.report') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-bar me-2"></i> Relatório
            </a>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Pendentes</h6>
                            <h3 class="mb-0 text-primary fw-bold">{{ $stats['pending'] }}</h3>
                            <small class="text-muted">em espera</small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-clock fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Em Andamento</h6>
                            <h3 class="mb-0 text-cyan fw-bold">{{ $stats['in_progress'] }}</h3>
                            <small class="text-muted">em produção</small>
                        </div>
                        <div class="text-cyan">
                            <i class="fas fa-cog fa-spin fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Concluídos</h6>
                            <h3 class="mb-0 text-success fw-bold">{{ $stats['completed'] }}</h3>
                            <small class="text-muted">finalizados</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Atrasados</h6>
                            <h3 class="mb-0 text-warning fw-bold">{{ $stats['overdue'] }}</h3>
                            <small class="text-muted">fora do prazo</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                Filtros de Pedidos
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('orders.index') }}" id="filters-form">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente
                            </option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Em
                                Andamento</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluído
                            </option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Entregue
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
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Pedidos -->
    <div class="card fade-in">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Pedidos Registrados
                </h5>
                <span class="badge bg-primary">Total: {{ $orders->total() }}</span>
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
                            <th class="text-end">Valor Est.</th>
                            <th>Entrega</th>
                            <th>Prioridade</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td><strong class="text-primary">#{{ $order->id }}</strong></td>
                                <td>
                                    <div class="fw-semibold">{{ $order->customer_name }}</div>
                                    @if ($order->customer_phone)
                                        <small class="text-muted">{{ $order->customer_phone }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ Str::limit($order->description, 50) }}</div>
                                    @if ($order->items_count > 0)
                                        <small class="text-muted">{{ $order->items_count }} itens</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="fw-semibold">MT {{ number_format($order->estimated_amount, 2, ',', '.') }}
                                    </div>
                                    @if ($order->advance_payment > 0)
                                        <small class="text-success">Sinal: MT
                                            {{ number_format($order->advance_payment, 2, ',', '.') }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if ($order->delivery_date)
                                        <div class="{{ $order->isOverdue() ? 'text-danger' : 'text-muted' }}">
                                            {{ $order->delivery_date->format('d/m/Y') }}
                                        </div>
                                        @if ($order->isOverdue())
                                            <small class="text-danger">
                                                <i class="fas fa-exclamation-triangle"></i> Atrasado
                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted">Não definida</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $order->priority_badge }}">
                                        {{ $order->priority_text }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $order->status_badge }}">
                                        {{ $order->status_text }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Ações do Pedido">

                                        {{-- Ver detalhes --}}
                                        <button type="button" class="btn btn-outline-info"
                                            onclick="viewOrderDetails({{ $order->id }})" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        {{-- Editar --}}
                                        @if ($order->canBeCompleted())
                                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-warning"
                                                title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-outline-secondary" disabled
                                                title="Não pode ser editado">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif

                                        {{-- Abrir Pedido --}}
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary"
                                            title="Abrir Pedido">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>

                                        {{-- Cancelar Pedido (via AJAX) --}}
                                        @if ($order->canBeCancelled())
                                            <button type="button" class="btn btn-outline-danger"
                                                onclick="cancelOrder({{ $order->id }})" title="Cancelar Pedido">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-clipboard-list fa-3x mb-3 opacity-50"></i>
                                    <p>Nenhum pedido encontrado.</p>
                                    <button type="button" class="btn btn-primary" onclick="openCreateOrderOffcanvas()">
                                        <i class="fas fa-plus me-2"></i> Criar Primeiro Pedido
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando {{ $orders->firstItem() ?? 0 }} a {{ $orders->lastItem() ?? 0 }} de {{ $orders->total() }}
                </small>
                {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
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
                price: parseFloat(option.dataset.price),
                quantity: 1
            };

            const existing = cartItems.find(item => item.id === productId);
            if (existing) {
                existing.quantity += 1;
            } else {
                cartItems.push(product);
            }

            updateCart();
            select.value = '';
        }

        // Atualizar carrinho
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
                        <input type="number" class="form-control form-control-sm" value="${item.quantity}" min="1" style="width: 60px;" onchange="updateItemQuantity(${index}, this.value)">
                    </td>
                    <td class="text-end">MT ${item.price.toFixed(2).replace('.', ',')}</td>
                    <td class="text-end">MT ${itemTotal.toFixed(2).replace('.', ',')}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            totalEl.textContent = `MT ${total.toFixed(2).replace('.', ',')}`;
            document.getElementById('estimated-amount').value = total;
        }

        // Funções de manipulação do carrinho
        function updateItemQuantity(index, value) {
            const qty = parseInt(value);
            if (qty > 0) {
                cartItems[index].quantity = qty;
                updateCart();
            }
        }

        function removeItem(index) {
            cartItems.splice(index, 1);
            updateCart();
        }

        // Função para visualizar detalhes do pedido
        function viewOrderDetails(orderId) {
            const content = document.getElementById('order-view-content');
            content.innerHTML =
                '<div class="text-center py-5"><div class="loading-spinner"></div><p class="text-muted mt-3">Carregando...</p></div>';
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('orderViewOffcanvas'));
            offcanvas.show();
            fetch(`/orders/${orderId}/details`)
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

        // Função para abrir o offcanvas de novo pedido
        function openCreateOrderOffcanvas() {
            resetOrderForm();
            document.getElementById('form-title').textContent = 'Novo Pedido';
            document.getElementById('form-method').value = 'POST';
            document.getElementById('order-form').action = "{{ route('orders.store') }}";
            document.getElementById('order-id').value = '';
            //status
            document.querySelector('select[name="status"]').value = '';
            //status
            document.getElementById('create-debt').checked = false;
            document.getElementById('debt-due-date-container').style.display = 'none';
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('orderFormOffcanvas'));
            offcanvas.show();
        }

        // Função para editar pedido
        function openEditOrderOffcanvas(orderId) {
            resetOrderForm();
            document.getElementById('form-title').textContent = 'Editar Pedido';
            document.getElementById('form-method').value = 'PUT';
            document.getElementById('order-form').action = `/orders/${orderId}`;
            document.getElementById('order-id').value = orderId;

            fetch(`/orders/${orderId}/edit-data`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const d = data.data;
                        document.getElementById('customer-name').value = d.customer_name;
                        document.getElementById('customer-phone').value = d.customer_phone || '';
                        document.getElementById('customer-email').value = d.customer_email || '';
                        document.getElementById('priority').value = d.priority;
                        document.getElementById('description').value = d.description;
                        document.getElementById('notes').value = d.notes || '';
                        document.getElementById('estimated-amount').value = d.estimated_amount;
                        document.getElementById('advance-payment').value = d.advance_payment || '';
                        document.getElementById('delivery-date').value = d.delivery_date || '';
                        cartItems = d.items || [];
                        updateCart();
                    }
                })
                .catch(() => showToast('Erro ao carregar dados', 'error'));

            const offcanvas = new bootstrap.Offcanvas(document.getElementById('orderFormOffcanvas'));
            offcanvas.show();
        }

        // Resetar formulário
        function resetOrderForm() {
            document.getElementById('order-form').reset();
            cartItems = [];
            updateCart();
            clearValidation();
        }

        // Validar formulário
        function validateOrderForm() {
            clearValidation();
            let isValid = true;
            const name = document.getElementById('customer-name').value.trim();
            const description = document.getElementById('description').value.trim();
            const estimatedAmount = parseFloat(document.getElementById('estimated-amount').value);

            if (!name) {
                showFieldError('customer-name', 'Nome é obrigatório');
                isValid = false;
            }
            if (!description) {
                showFieldError('description', 'Descrição é obrigatória');
                isValid = false;
            }
            if (!estimatedAmount || estimatedAmount <= 0) {
                showFieldError('estimated-amount', 'Valor estimado deve ser maior que zero');
                isValid = false;
            }

            return isValid;
        }

        // Submit do formulário
        document.getElementById('order-form').addEventListener('submit', function(e) {
            e.preventDefault();
            if (!validateOrderForm()) return;

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('_method', document.getElementById('form-method').value);
            formData.append('customer_name', document.getElementById('customer-name').value);
            formData.append('customer_phone', document.getElementById('customer-phone').value);
            formData.append('customer_email', document.getElementById('customer-email').value);
            formData.append('priority', document.getElementById('priority').value);
            formData.append('description', document.getElementById('description').value);
            formData.append('notes', document.getElementById('notes').value);
            formData.append('estimated_amount', document.getElementById('estimated-amount').value);
            formData.append('advance_payment', document.getElementById('advance-payment').value || 0);
            formData.append('delivery_date', document.getElementById('delivery-date').value);
            formData.append('create_debt', document.getElementById('create-debt').checked ? 1 : 0);
            formData.append('debt_due_date', document.getElementById('debt-due-date').value);

            if (cartItems.length === 0) {
                showToast('Adicione pelo menos um item ao pedido', 'warning');
                return;
            }
            formData.append('items', JSON.stringify(cartItems));

            const url = this.action;
            const submitBtn = document.getElementById('save-order-btn');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Salvando...';

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
                        bootstrap.Offcanvas.getInstance(document.getElementById('orderFormOffcanvas')).hide();
                        showToast(data.message || 'Pedido salvo com sucesso!', 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const selector = `#${field.replace('_', '-')}`;
                                showFieldError(selector, data.errors[field][0]);
                            });
                        }
                        showToast(data.message || 'Erro ao salvar pedido.', 'error');
                    }
                })
                .catch(() => showToast('Erro de conexão.', 'error'))
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        });

        // Gerenciar criação de dívida
        document.getElementById('create-debt').addEventListener('change', function() {
            document.getElementById('debt-due-date-container').style.display = this.checked ? 'block' : 'none';
            if (this.checked) {
                document.getElementById('debt-due-date').value = new Date().toISOString().split('T')[0];
            }
        });

        // Funções de utilidade
        function showFieldError(fieldId, message) {
            const field = document.querySelector(fieldId);
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

        function cancelOrder(orderId) {
            if (!confirm('Tem certeza que deseja cancelar este pedido?')) return;

            fetch(`/orders/${orderId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        // Opcional: recarregar a página ou atualizar o status do pedido na tabela
                        location.reload();
                    } else {
                        alert(data.message || 'Erro ao cancelar o pedido.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Erro ao cancelar o pedido.');
                });
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

        .stats-card.info {
            border-left-color: #0891b2;
        }

        .stats-card.success {
            border-left-color: #059669;
        }

        .stats-card.warning {
            border-left-color: #ea580c;
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
