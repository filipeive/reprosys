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
                            <label class="form-label fw-semibold">Nome *</label>
                            <input type="text" class="form-control" name="customer_name" id="customer-name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Telefone</label>
                            <input type="text" class="form-control" name="customer_phone" id="customer-phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" name="customer_email" id="customer-email">
                        </div>
                        <div class="col-md-6">
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

                <!-- Seleção de Itens -->
                <div class="p-4 border-bottom">
                    <h6 class="mb-3"><i class="fas fa-box me-2"></i> Itens do Pedido</h6>

                    <!-- Seleção de produtos -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <select class="form-select" id="product-select">
                                <option value="">Selecione um produto ou serviço...</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                        data-description="{{ $product->description }}"
                                        data-price="{{ $product->selling_price }}">
                                        {{ $product->name }} - MT {{ number_format($product->selling_price, 2, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control" id="item-quantity" placeholder="Qtd" min="1"
                                value="1">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary w-100" onclick="addSelectedItem()">
                                <i class="fas fa-plus me-2"></i>Adicionar
                            </button>
                        </div>
                    </div>

                    <!-- Status (apenas para edição) -->
                    <div class="row g-3 mb-3" id="status-container" style="display: none;">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="status" id="order-status">
                                <option value="pending">Pendente</option>
                                <option value="in_progress">Em Andamento</option>
                                <option value="completed">Concluído</option>
                                <option value="delivered">Entregue</option>
                            </select>
                        </div>
                    </div>

                    <!-- Carrinho -->
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" id="cart-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Preço Unit.</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items">
                                <!-- Itens serão adicionados via JS -->
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
                            <label class="form-label fw-semibold">Descrição *</label>
                            <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Observações</label>
                            <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Valor Estimado *</label>
                            <div class="input-group">
                                <span class="input-group-text">MT</span>
                                <input type="number" step="0.01" class="form-control" name="estimated_amount"
                                    id="estimated-amount" required readonly>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sinal Recebido</label>
                            <div class="input-group">
                                <span class="input-group-text">MT</span>
                                <input type="number" step="0.01" class="form-control" name="advance_payment"
                                    id="advance-payment">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Data de Entrega</label>
                            <input type="date" class="form-control" name="delivery_date" id="delivery-date"
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4">
                                <input type="checkbox" class="form-check-input" id="create-debt" name="create_debt"
                                    value="1">
                                <label class="form-check-label fw-semibold">Criar Dívida para o Restante</label>
                            </div>
                            <div class="mt-2" id="debt-due-date-container" style="display: none;">
                                <label class="form-label fw-semibold">Vencimento da Dívida</label>
                                <input type="date" class="form-control" name="debt_due_date" id="debt-due-date">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="offcanvas-footer p-3 border-top bg-light">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" form="order-form" class="btn btn-primary flex-fill" id="save-order-btn">
                    <i class="fas fa-save me-2"></i> <span id="save-btn-text">Salvar Pedido</span>
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
            <button type="button" class="btn btn-success" onclick="openCreateOrderOffcanvas()">
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
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Prioridade</label>
                        <select class="form-select" name="priority">
                            <option value="">Todas</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Baixa</option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Média</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Cliente</label>
                        <input type="text" class="form-control" name="customer" placeholder="Nome..."
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
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Limpar
                        </a>
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
                                    @if ($order->items->count() > 0)
                                        <small class="text-muted">{{ $order->items->count() }} itens</small>
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
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- Ver detalhes -->
                                        <button type="button" class="btn btn-outline-info"
                                            onclick="viewOrderDetails({{ $order->id }})" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <!-- Editar -->
                                        @if ($order->canBeCompleted())
                                            <button type="button" class="btn btn-outline-warning"
                                                onclick="openEditOrderOffcanvas({{ $order->id }})" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-outline-secondary" disabled
                                                title="Não pode ser editado">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif

                                        <!-- Duplicar -->
                                        <button type="button" class="btn btn-outline-primary"
                                            onclick="duplicateOrder({{ $order->id }})" title="Duplicar">
                                            <i class="fas fa-copy"></i>
                                        </button>

                                        <!-- Cancelar -->
                                        @if ($order->canBeCancelled())
                                            <button type="button" class="btn btn-outline-danger"
                                                onclick="cancelOrder({{ $order->id }})"
                                                data-url="{{ route('orders.destroy', $order->id) }}" title="Cancelar">
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

            @if ($orders->hasPages())
                <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Mostrando {{ $orders->firstItem() ?? 0 }} a {{ $orders->lastItem() ?? 0 }} de
                        {{ $orders->total() }}
                    </small>
                    {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Variáveis globais
        let cartItems = [];

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            initializeDebtToggle();
            initializeFormHandlers();
            initializeAutoFilters();
            initializePhoneMask();
        });

        // ==== GERENCIAMENTO DO CARRINHO ====
        function addSelectedItem() {
            const productSelect = document.getElementById('product-select');
            const quantityInput = document.getElementById('item-quantity');

            if (!productSelect.value) {
                showToast('Selecione um produto da lista', 'warning');
                return;
            }

            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const quantity = parseInt(quantityInput.value) || 1;

            if (quantity <= 0) {
                showToast('Quantidade deve ser maior que zero', 'warning');
                return;
            }

            const productId = parseInt(productSelect.value);
            const productName = selectedOption.dataset.name;
            const productDescription = selectedOption.dataset.description || '';
            const unitPrice = parseFloat(selectedOption.dataset.price);

            // Verificar se o produto já existe no carrinho
            const existingIndex = cartItems.findIndex(item => item.product_id === productId);

            if (existingIndex >= 0) {
                cartItems[existingIndex].quantity += quantity;
            } else {
                cartItems.push({
                    product_id: productId,
                    item_name: productName,
                    description: productDescription,
                    quantity: quantity,
                    unit_price: unitPrice
                });
            }

            updateCart();

            // Limpar seleção
            productSelect.value = '';
            quantityInput.value = '1';
        }

        function updateCart() {
            const tbody = document.getElementById('cart-items');
            const totalEl = document.getElementById('cart-total');
            const estimatedAmountEl = document.getElementById('estimated-amount');

            let total = 0;
            tbody.innerHTML = '';

            cartItems.forEach((item, index) => {
                const itemTotal = item.quantity * item.unit_price;
                total += itemTotal;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <strong>${item.item_name}</strong>
                        ${item.description ? `<br><small class="text-muted">${item.description}</small>` : ''}
                    </td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm" 
                               value="${item.quantity}" min="1" style="width: 70px;" 
                               onchange="updateItemQuantity(${index}, this.value)">
                    </td>
                    <td class="text-end">MT ${item.unit_price.toFixed(2).replace('.', ',')}</td>
                    <td class="text-end fw-semibold">MT ${itemTotal.toFixed(2).replace('.', ',')}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(${index})" title="Remover">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            // Se não houver itens, mostrar mensagem
            if (cartItems.length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.innerHTML = `
                    <td colspan="5" class="text-center text-muted py-3">
                        Nenhum item adicionado ao pedido
                    </td>
                `;
                tbody.appendChild(emptyRow);
            }

            totalEl.textContent = `MT ${total.toFixed(2).replace('.', ',')}`;
            estimatedAmountEl.value = total.toFixed(2);
        }

        function updateItemQuantity(index, value) {
            const quantity = parseInt(value);
            if (quantity > 0) {
                cartItems[index].quantity = quantity;
                updateCart();
            }
        }

        function removeItem(index) {
            if (confirm('Remover este item do pedido?')) {
                cartItems.splice(index, 1);
                updateCart();
            }
        }

        // ==== OFFCANVAS DE PEDIDOS ====
        function openCreateOrderOffcanvas() {
            resetOrderForm();
            document.getElementById('form-title').textContent = 'Novo Pedido';
            document.getElementById('form-method').value = 'POST';
            document.getElementById('order-form').action = "{{ route('orders.store') }}";
            document.getElementById('order-id').value = '';
            document.getElementById('save-btn-text').textContent = 'Salvar Pedido';
            document.getElementById('status-container').style.display = 'none';

            const offcanvas = new bootstrap.Offcanvas(document.getElementById('orderFormOffcanvas'));
            offcanvas.show();
        }

        async function openEditOrderOffcanvas(orderId) {
            try {
                const response = await fetch(`{{ route('orders.index') }}/${orderId}/edit-data`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (!result.success) {
                    showToast('Erro ao carregar dados do pedido', 'error');
                    return;
                }

                const order = result.data;

                // Configurar formulário para edição
                document.getElementById('form-title').textContent = 'Editar Pedido';
                document.getElementById('form-method').value = 'PUT';
                document.getElementById('order-form').action = `{{ route('orders.index') }}/${orderId}`;
                document.getElementById('order-id').value = orderId;
                document.getElementById('save-btn-text').textContent = 'Atualizar Pedido';
                document.getElementById('status-container').style.display = 'block';

                // Preencher dados do cliente
                document.getElementById('customer-name').value = order.customer_name || '';
                document.getElementById('customer-phone').value = order.customer_phone || '';
                document.getElementById('customer-email').value = order.customer_email || '';
                document.getElementById('priority').value = order.priority || '';
                document.getElementById('order-status').value = order.status || '';

                // Preencher detalhes do pedido
                document.getElementById('description').value = order.description || '';
                document.getElementById('notes').value = order.notes || '';
                document.getElementById('estimated-amount').value = order.estimated_amount || '';
                document.getElementById('advance-payment').value = order.advance_payment || '';
                document.getElementById('delivery-date').value = order.delivery_date || '';

                // Carregar itens do pedido
                cartItems = order.items.map(item => ({
                    product_id: item.id || null,
                    item_name: item.name,
                    description: item.description || '',
                    quantity: parseInt(item.quantity),
                    unit_price: parseFloat(item.price)
                }));

                updateCart();

                const offcanvas = new bootstrap.Offcanvas(document.getElementById('orderFormOffcanvas'));
                offcanvas.show();

            } catch (error) {
                console.error('Erro ao carregar pedido:', error);
                showToast('Erro ao carregar dados do pedido', 'error');
            }
        }

        async function viewOrderDetails(orderId) {
            const content = document.getElementById('order-view-content');
            content.innerHTML =
                '<div class="text-center py-5"><div class="loading-spinner"></div><p class="text-muted mt-3">Carregando detalhes...</p></div>';

            const offcanvas = new bootstrap.Offcanvas(document.getElementById('orderViewOffcanvas'));
            offcanvas.show();

            try {
                const response = await fetch(`{{ route('orders.index') }}/${orderId}/details`);
                const result = await response.json();

                if (result.success) {
                    content.innerHTML = result.html;
                } else {
                    content.innerHTML = '<div class="alert alert-danger">Erro ao carregar detalhes do pedido.</div>';
                }
            } catch (error) {
                console.error('Erro ao carregar detalhes:', error);
                content.innerHTML = '<div class="alert alert-danger">Erro de conexão ao carregar detalhes.</div>';
            }
        }

        // ==== AÇÕES DE PEDIDOS ====
        async function duplicateOrder(orderId) {
            if (!confirm('Deseja duplicar este pedido?')) return;

            try {
                const response = await fetch(`{{ route('orders.index') }}/${orderId}/edit-data`);
                const result = await response.json();

                if (!result.success) {
                    showToast('Erro ao carregar dados do pedido', 'error');
                    return;
                }

                const order = result.data;

                // Abrir formulário de criação com dados do pedido
                resetOrderForm();
                document.getElementById('form-title').textContent = 'Duplicar Pedido';
                document.getElementById('form-method').value = 'POST';
                document.getElementById('order-form').action = "{{ route('orders.store') }}";
                document.getElementById('order-id').value = '';
                document.getElementById('save-btn-text').textContent = 'Criar Pedido';
                document.getElementById('status-container').style.display = 'none';

                // Preencher dados (sem IDs)
                document.getElementById('customer-name').value = order.customer_name || '';
                document.getElementById('customer-phone').value = order.customer_phone || '';
                document.getElementById('customer-email').value = order.customer_email || '';
                document.getElementById('priority').value = order.priority || '';
                document.getElementById('description').value = order.description + ' (Cópia)';
                document.getElementById('notes').value = order.notes || '';

                // Carregar itens
                cartItems = order.items.map(item => ({
                    product_id: item.id || null,
                    item_name: item.name,
                    description: item.description || '',
                    quantity: parseInt(item.quantity),
                    unit_price: parseFloat(item.price)
                }));

                updateCart();

                const offcanvas = new bootstrap.Offcanvas(document.getElementById('orderFormOffcanvas'));
                offcanvas.show();

            } catch (error) {
                console.error('Erro ao duplicar pedido:', error);
                showToast('Erro ao duplicar pedido', 'error');
            }
        }

        async function cancelOrder(orderId) {
            const btn = document.querySelector(`[onclick="cancelOrder(${orderId})"]`);
            const url = btn.dataset.url;

            if (!confirm('Tem certeza que deseja cancelar este pedido?')) return;

            try {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Accept': 'application/json',
                    },
                });

                const result = await response.json();

                if (result.success) {
                    showToast(result.message || 'Pedido cancelado com sucesso!', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showToast(result.message || 'Erro ao cancelar pedido', 'error');
                }
            } catch (error) {
                console.error('Erro ao cancelar pedido:', error);
                showToast('Erro de conexão ao cancelar pedido', 'error');
            }
        }

        // ==== FORMULÁRIOS ====
        function initializeFormHandlers() {
            const form = document.getElementById('order-form');
            form.addEventListener('submit', handleFormSubmit);
        }

        function initializeDebtToggle() {
            const createDebtCheckbox = document.getElementById('create-debt');
            const debtContainer = document.getElementById('debt-due-date-container');

            createDebtCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    debtContainer.style.display = 'block';
                    // Definir data padrão para 30 dias após entrega (ou hoje se não tiver data de entrega)
                    const deliveryDate = document.getElementById('delivery-date').value;
                    const dueDate = new Date(deliveryDate || Date.now());
                    dueDate.setDate(dueDate.getDate() + 30);
                    document.getElementById('debt-due-date').value = dueDate.toISOString().split('T')[0];
                } else {
                    debtContainer.style.display = 'none';
                }
            });
        }

        async function handleFormSubmit(e) {
            e.preventDefault();

            if (!validateOrderForm()) {
                return;
            }

            const formData = new FormData();
            const method = document.getElementById('form-method').value;

            // Dados básicos
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('_method', method);
            formData.append('customer_name', document.getElementById('customer-name').value.trim());
            formData.append('customer_phone', document.getElementById('customer-phone').value.trim());
            formData.append('customer_email', document.getElementById('customer-email').value.trim());
            formData.append('priority', document.getElementById('priority').value);
            formData.append('description', document.getElementById('description').value.trim());
            formData.append('notes', document.getElementById('notes').value.trim());
            formData.append('estimated_amount', document.getElementById('estimated-amount').value);
            formData.append('advance_payment', document.getElementById('advance-payment').value || '0');
            formData.append('delivery_date', document.getElementById('delivery-date').value);
            formData.append('create_debt', document.getElementById('create-debt').checked ? '1' : '0');
            formData.append('debt_due_date', document.getElementById('debt-due-date').value);

            // Status (apenas para edição)
            if (method === 'PUT') {
                formData.append('status', document.getElementById('order-status').value);
                formData.append('internal_notes', document.getElementById('notes').value.trim());
            }

            // Itens do pedido
            if (cartItems.length === 0) {
                showToast('Adicione pelo menos um item ao pedido', 'warning');
                return;
            }

            formData.append('items', JSON.stringify(cartItems));

            const submitBtn = document.getElementById('save-order-btn');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Salvando...';

            try {
                const response = await fetch(e.target.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    bootstrap.Offcanvas.getInstance(document.getElementById('orderFormOffcanvas')).hide();
                    showToast(result.message || 'Pedido salvo com sucesso!', 'success');

                    if (result.redirect) {
                        setTimeout(() => window.location.href = result.redirect, 1500);
                    } else {
                        setTimeout(() => window.location.reload(), 1500);
                    }
                } else {
                    if (result.errors) {
                        handleFormErrors(result.errors);
                    }
                    showToast(result.message || 'Erro ao salvar pedido', 'error');
                }

            } catch (error) {
                console.error('Erro ao enviar formulário:', error);
                showToast('Erro de conexão ao salvar pedido', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }

        function validateOrderForm() {
            clearFormValidation();
            let isValid = true;

            // Nome obrigatório
            const customerName = document.getElementById('customer-name').value.trim();
            if (!customerName) {
                showFieldError('customer-name', 'Nome do cliente é obrigatório');
                isValid = false;
            }

            // Prioridade obrigatória
            const priority = document.getElementById('priority').value;
            if (!priority) {
                showFieldError('priority', 'Prioridade é obrigatória');
                isValid = false;
            }

            // Descrição obrigatória
            const description = document.getElementById('description').value.trim();
            if (!description) {
                showFieldError('description', 'Descrição é obrigatória');
                isValid = false;
            }

            // Valor estimado
            const estimatedAmount = parseFloat(document.getElementById('estimated-amount').value);
            if (!estimatedAmount || estimatedAmount <= 0) {
                showFieldError('estimated-amount', 'Valor estimado deve ser maior que zero');
                isValid = false;
            }

            // Sinal não pode ser maior que o valor total
            const advancePayment = parseFloat(document.getElementById('advance-payment').value) || 0;
            if (advancePayment > estimatedAmount) {
                showFieldError('advance-payment', 'Sinal não pode ser maior que o valor estimado');
                isValid = false;
            }

            // Pelo menos um item no carrinho
            if (cartItems.length === 0) {
                showToast('Adicione pelo menos um item ao pedido', 'warning');
                isValid = false;
            }

            // Validação de data de entrega (deve ser futura para novos pedidos)
            const deliveryDate = document.getElementById('delivery-date').value;
            const method = document.getElementById('form-method').value;
            if (method === 'POST' && deliveryDate) {
                const today = new Date().toISOString().split('T')[0];
                if (deliveryDate <= today) {
                    showFieldError('delivery-date', 'Data de entrega deve ser futura');
                    isValid = false;
                }
            }

            return isValid;
        }

        function handleFormErrors(errors) {
            Object.keys(errors).forEach(field => {
                const fieldName = field.replace('_', '-');
                showFieldError(fieldName, errors[field][0]);
            });
        }

        function showFieldError(fieldId, message) {
            const field = document.getElementById(fieldId);
            if (field) {
                field.classList.add('is-invalid');
                let feedback = field.parentNode.querySelector('.invalid-feedback');
                if (!feedback) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    field.parentNode.appendChild(feedback);
                }
                feedback.textContent = message;
                field.focus();
            }
        }

        function clearFormValidation() {
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.textContent = '';
            });
        }

        function resetOrderForm() {
            document.getElementById('order-form').reset();
            cartItems = [];
            updateCart();
            clearFormValidation();
            document.getElementById('create-debt').checked = false;
            document.getElementById('debt-due-date-container').style.display = 'none';
        }

        // ==== FILTROS AUTOMÁTICOS ====
        function initializeAutoFilters() {
            const form = document.getElementById('filters-form');
            const selects = form.querySelectorAll('select');

            selects.forEach(select => {
                select.addEventListener('change', () => {
                    setTimeout(() => form.submit(), 100);
                });
            });
        }

        // ==== MÁSCARA DE TELEFONE ====
        function initializePhoneMask() {
            const phoneInput = document.getElementById('customer-phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length >= 9) {
                        if (value.length === 9) {
                            value = value.replace(/(\d{2})(\d{3})(\d{4})/, '$1 $2 $3');
                        } else {
                            value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{3})/, '$1 $2 $3 $4');
                        }
                    }
                    e.target.value = value;
                });
            }
        }

        // ==== UTILIDADES ====
        function showToast(message, type = 'info') {
            const toastContainer = getOrCreateToastContainer();

            const bgClass = {
                'success': 'bg-success',
                'error': 'bg-danger',
                'warning': 'bg-warning text-dark',
                'info': 'bg-info'
            } [type] || 'bg-primary';

            const iconClass = {
                'success': 'fas fa-check-circle',
                'error': 'fas fa-exclamation-circle',
                'warning': 'fas fa-exclamation-triangle',
                'info': 'fas fa-info-circle'
            } [type] || 'fas fa-info-circle';

            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white ${bgClass} border-0 mb-2`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="${iconClass} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close ${type === 'warning' ? 'btn-close-dark' : 'btn-close-white'} me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            toastContainer.appendChild(toast);

            const bsToast = new bootstrap.Toast(toast, {
                delay: type === 'error' ? 7000 : 4000
            });

            bsToast.show();

            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }

        function getOrCreateToastContainer() {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'toast-container position-fixed top-0 end-0 p-3';
                container.style.zIndex = '9999';
                document.body.appendChild(container);
            }
            return container;
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

        .text-cyan {
            color: #0891b2 !important;
        }

        /* Melhorias responsivas */
        @media (max-width: 768px) {

            #orderFormOffcanvas,
            #orderViewOffcanvas {
                width: 100% !important;
            }

            .stats-card {
                margin-bottom: 1rem;
            }

            .btn-group-sm .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }
        }

        /* Toast container posicionamento */
        .toast-container {
            z-index: 9999;
        }

        /* Estilo para campos inválidos */
        .is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* Animação suave para offcanvas */
        .offcanvas {
            transition: transform 0.3s ease-in-out;
        }

        /* Badges personalizados */
        .badge {
            font-size: 0.75em;
            font-weight: 500;
        }

        /* Botões de ação hover */
        .btn-outline-info:hover,
        .btn-outline-warning:hover,
        .btn-outline-primary:hover,
        .btn-outline-danger:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease;
        }

        /* Loading state para botões */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Scrollbar personalizada */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
@endpush
