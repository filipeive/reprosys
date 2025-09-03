@extends('layouts.app')

@section('title', 'Nova Venda')
@section('title-icon', 'fa-cash-register')
@section('page-title', 'Nova Venda')

@php
    $titleIcon = 'fa-cash-register';
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('sales.index') }}"> <i class="fas fa-shopping-cart"></i> Vendas</a>
    </li>
    <li class="breadcrumb-item active">Nova Venda</li>
@endsection

@section('content')
    <div class="row">
        <!-- Produtos -->
        <div class="col-lg-7 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-boxes me-2"></i> Produtos & Serviços
                    </h5>
                    <div class="d-flex align-items-center">
                        <div class="input-group" style="width: 250px;">
                            <input type="text" class="form-control" id="product-search"
                                placeholder="Pesquisar produtos...">
                            <button class="btn btn-light" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3" style="max-height: 600px; overflow-y: auto;">
                    <div class="row g-3" id="products-container">
                        @foreach ($products as $product)
                            <div class="col-sm-6 col-lg-4 product-item" data-name="{{ strtolower($product->name) }}">
                                <div class="card product-card h-100 border-light">
                                    <div class="card-body text-center p-3">
                                        <div class="product-icon mb-2">
                                            @if ($product->type === 'product')
                                                <i class="fas fa-box text-primary fa-2x"></i>
                                            @else
                                                <i class="fas fa-tools text-info fa-2x"></i>
                                            @endif
                                        </div>
                                        <h6 class="card-title fw-bold mb-1">{{ $product->name }}</h6>
                                        <p class="text-success fw-bold mb-2">
                                            MZN {{ number_format($product->selling_price, 2, ',', '.') }}
                                        </p>

                                        @if ($product->type === 'product')
                                            <small class="text-muted d-block mb-2">
                                                <i class="fas fa-cubes me-1"></i>Stock: {{ $product->stock_quantity }}
                                            </small>
                                        @else
                                            <small class="text-info d-block mb-2">
                                                <i class="fas fa-concierge-bell me-1"></i>Serviço
                                            </small>
                                        @endif

                                        <button class="btn btn-outline-primary btn-sm add-product-btn"
                                            data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                            data-price="{{ $product->selling_price }}" data-type="{{ $product->type }}"
                                            data-stock="{{ $product->stock_quantity }}">
                                            <i class="fas fa-plus me-1"></i> Adicionar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Carrinho -->
        <div class="col-lg-5 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-cart me-2"></i> Carrinho
                    </h5>
                    <button type="button" class="btn btn-outline-light btn-sm" id="clear-cart">
                        <i class="fas fa-trash me-1"></i> Limpar
                    </button>
                </div>
                <div class="card-body">
                    <form id="sale-form" action="{{ route('sales.store') }}" method="POST">
                        @csrf

                        <!-- Dados do Cliente -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="customer_name" class="form-label">Nome do Cliente</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name"
                                    placeholder="Digite o nome do cliente">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="customer_phone" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="customer_phone" name="customer_phone"
                                    placeholder="(xx) xxxxx-xxxx">
                            </div>
                        </div>

                        <!-- Itens do Carrinho -->
                        <div class="cart-items mb-3">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-list me-1"></i> Itens no Carrinho
                            </h6>
                            <div id="cart-items-list" style="max-height: 250px; overflow-y: auto;">
                                <div class="text-center text-muted py-4" id="empty-cart-message">
                                    <i class="fas fa-shopping-cart fa-2x mb-2 opacity-50"></i>
                                    <p class="mb-1">Carrinho vazio</p>
                                    <small>Clique nos produtos para adicionar</small>
                                </div>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="bg-light p-3 rounded mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Total de Itens:</span>
                                <span id="total-items" class="fw-bold">0</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <strong>Total Geral:</strong>
                                <strong class="text-success fs-5" id="total-amount">MZN 0,00</strong>
                            </div>
                        </div>

                        <!-- Pagamento -->
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Método de Pagamento *</label>
                            <select class="form-control" id="payment_method" name="payment_method" required>
                                <option value="">Selecione...</option>
                                <option value="cash">💵 Dinheiro</option>
                                <option value="card">💳 Cartão</option>
                                <option value="transfer">🏦 Transferência</option>
                                <option value="credit">🤝 Crédito</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Observações</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"
                                placeholder="Observações sobre a venda..."></textarea>
                        </div>

                        <!-- Botões -->
                        <button type="submit" class="btn btn-success w-100" id="finalize-sale" disabled>
                            <i class="fas fa-check me-2"></i> Finalizar Venda
                        </button>
                        <button type="button" class="btn btn-warning w-100 mt-2" id="save-as-order">
                            <i class="fas fa-clipboard-list me-2"></i> Salvar como Pedido
                        </button>
                        <button type="button" class="btn btn-danger w-100 mt-2" id="save-as-debt">
                            <i class="fas fa-hand-holding-usd me-2"></i> Registrar como Dívida
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Offcanvas para Criar Pedido -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="orderCreationOffcanvas">
        <div class="offcanvas-header bg-warning text-dark">
            <h5 class="offcanvas-title">
                <i class="fas fa-clipboard-list me-2"></i> Criar Pedido
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="order-form" action="{{ route('orders.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="order_customer_name" class="form-label">Nome do Cliente *</label>
                    <input type="text" class="form-control" id="order_customer_name" name="customer_name" required>
                </div>

                <div class="mb-3">
                    <label for="order_customer_phone" class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="order_customer_phone" name="customer_phone">
                </div>

                <div class="mb-3">
                    <label for="order_description" class="form-label">Descrição do Pedido *</label>
                    <textarea class="form-control" id="order_description" name="description" rows="3" required></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="delivery_date" class="form-label">Data de Entrega</label>
                        <input type="date" class="form-control" id="delivery_date" name="delivery_date">
                    </div>
                    <div class="col-md-6">
                        <label for="priority" class="form-label">Prioridade</label>
                        <select class="form-control" id="priority" name="priority" required>
                            <option value="low">Baixa</option>
                            <option value="medium" selected>Média</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="estimated_amount" class="form-label">Valor Estimado</label>
                        <input type="number" class="form-control" id="estimated_amount" name="estimated_amount"
                            step="0.01" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="advance_payment" class="form-label">Sinal Recebido</label>
                        <input type="number" class="form-control" id="advance_payment" name="advance_payment"
                            step="0.01" value="0">
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="create_debt" name="create_debt">
                    <label class="form-check-label" for="create_debt">Registrar valor em aberto como dívida</label>
                </div>

                <div class="mb-3" id="debt_due_date_container" style="display: none;">
                    <label for="debt_due_date" class="form-label">Data de Vencimento da Dívida</label>
                    <input type="date" class="form-control" id="debt_due_date" name="debt_due_date">
                </div>

                <input type="hidden" name="items" id="order-items-input">
            </form>

            <div class="alert alert-info small mt-3">
                <i class="fas fa-info-circle me-2"></i>
                O pedido será criado com status <strong>Pendente</strong>. Você poderá convertê-lo em venda quando estiver
                pronto.
            </div>
        </div>
        <div class="offcanvas-footer">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-warning flex-fill" id="submit-order-form">
                    <i class="fas fa-save me-2"></i> Criar Pedido
                </button>
            </div>
        </div>
    </div>

    <!-- Offcanvas para Criar Dívida -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="debtCreationOffcanvas">
        <div class="offcanvas-header bg-danger text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-hand-holding-usd me-2"></i> Criar Dívida
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="debt-form">
                @csrf

                <div class="mb-3">
                    <label for="debt_customer_name" class="form-label">Nome do Cliente *</label>
                    <input type="text" class="form-control" id="debt_customer_name" required>
                </div>

                <div class="mb-3">
                    <label for="debt_customer_phone" class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="debt_customer_phone">
                </div>

                <div class="mb-3">
                    <label for="debt_description" class="form-label">Descrição *</label>
                    <input type="text" class="form-control" id="debt_description" value="Venda a crédito" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="debt_date" class="form-label">Data da Dívida</label>
                        <input type="date" class="form-control" id="debt_date" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="due_date" class="form-label">Vencimento</label>
                        <input type="date" class="form-control" id="due_date">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="debt_notes" class="form-label">Observações</label>
                    <textarea class="form-control" id="debt_notes" rows="2"></textarea>
                </div>

                <input type="hidden" id="debt-items-input">
            </form>

            <div class="alert alert-warning small mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                O estoque será baixado imediatamente. O cliente deverá pagar posteriormente.
            </div>
        </div>
        <div class="offcanvas-footer">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger flex-fill" id="submit-debt-form">
                    <i class="fas fa-save me-2"></i> Criar Dívida
                </button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .product-card {
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
            border-color: var(--primary-blue);
        }

        .cart-item {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 12px;
            margin-bottom: 8px;
            background: var(--content-bg);
            transition: all 0.2s ease;
        }

        .cart-item:hover {
            box-shadow: var(--shadow-sm);
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .quantity-btn {
            width: 28px;
            height: 28px;
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            border-radius: var(--border-radius);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: all 0.2s ease;
            color: var(--text-secondary);
        }

        .quantity-btn:hover {
            background: var(--content-bg);
            border-color: var(--primary-blue);
            color: var(--primary-blue);
        }

        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 2px 4px;
            height: 28px;
            background: var(--card-bg);
            color: var(--text-primary);
        }

        .remove-btn {
            background: none;
            border: none;
            color: var(--danger-red);
            cursor: pointer;
            padding: 4px;
            border-radius: var(--border-radius);
            transition: all 0.2s ease;
        }

        .remove-btn:hover {
            background: rgba(220, 53, 69, 0.1);
            transform: scale(1.1);
        }

        .product-item.hidden {
            display: none !important;
        }

        .add-product-btn:hover {
            background: var(--primary-blue);
            color: white;
            border-color: var(--primary-blue);
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Ajustes para tema escuro */
        [data-bs-theme="dark"] .product-card {
            background: var(--card-bg);
        }

        [data-bs-theme="dark"] .cart-item {
            background: var(--card-bg);
        }

        [data-bs-theme="dark"] .quantity-btn,
        [data-bs-theme="dark"] .quantity-input {
            background: var(--content-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let cart = [];

            // Usar o sistema de toast profissional do layout
            function showToast(message, type = 'success') {
                if (window.FDSMULTSERVICES && window.FDSMULTSERVICES.Toast) {
                    window.FDSMULTSERVICES.Toast.show(message, type);
                } else if (window.ProfessionalToast) {
                    window.ProfessionalToast.show(message, type);
                } else {
                    console.warn('Sistema de toast não encontrado, usando alert');
                    alert(message);
                }
            }

            // Aplicar máscara no telefone
            const phoneInput = document.getElementById('customer_phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length >= 2) {
                        value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
                    }
                    e.target.value = value;
                });
            }

            // Pesquisa de produtos
            document.getElementById('product-search').addEventListener('input', function() {
                const search = this.value.toLowerCase();
                document.querySelectorAll('.product-item').forEach(function(item) {
                    const productName = item.getAttribute('data-name');
                    if (productName.includes(search)) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });
            });

            // Adicionar produto ao carrinho
            document.querySelectorAll('.add-product-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = parseInt(this.getAttribute('data-id'));
                    const productName = this.getAttribute('data-name');
                    const productPrice = parseFloat(this.getAttribute('data-price'));
                    const productType = this.getAttribute('data-type');
                    const stockQuantity = parseInt(this.getAttribute('data-stock'));

                    // Verificar stock para produtos
                    if (productType === 'product') {
                        const currentItem = cart.find(item => item.product_id === productId);
                        const quantityInCart = currentItem ? currentItem.quantity : 0;

                        if (quantityInCart >= stockQuantity) {
                            showToast(`Stock insuficiente para ${productName}`, 'error');
                            return;
                        }
                    }

                    // Adicionar ou incrementar no carrinho
                    const existingIndex = cart.findIndex(item => item.product_id === productId);

                    if (existingIndex !== -1) {
                        cart[existingIndex].quantity++;
                    } else {
                        cart.push({
                            product_id: productId,
                            name: productName,
                            unit_price: productPrice,
                            quantity: 1,
                            type: productType,
                            stock: stockQuantity
                        });
                    }

                    updateCartDisplay();
                    showToast(`${productName} adicionado ao carrinho!`, 'success');
                });
            });

            // Atualizar exibição do carrinho
            function updateCartDisplay() {
                const cartItemsList = document.getElementById('cart-items-list');
                const emptyMessage = document.getElementById('empty-cart-message');

                if (cart.length === 0) {
                    emptyMessage.style.display = 'block';
                    cartItemsList.querySelectorAll('.cart-item').forEach(item => item.remove());
                    document.getElementById('finalize-sale').disabled = true;
                    updateTotals();
                    return;
                }

                emptyMessage.style.display = 'none';
                cartItemsList.querySelectorAll('.cart-item').forEach(item => item.remove());

                cart.forEach((item, index) => {
                    const itemTotal = item.unit_price * item.quantity;
                    const icon = item.type === 'service' ? 'fa-tools text-info' : 'fa-box text-primary';

                    const cartItemHtml = `
                    <div class="cart-item fade-in" data-index="${index}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas ${icon} me-2"></i>
                                    <strong class="small">${item.name}</strong>
                                </div>
                                <small class="text-muted">
                                    MZN ${item.unit_price.toFixed(2).replace('.', ',')} x ${item.quantity}
                                </small>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="quantity-controls me-2">
                                    <button type="button" class="quantity-btn decrease-qty" data-index="${index}">-</button>
                                    <input type="number" class="quantity-input" value="${item.quantity}" 
                                           min="1" data-index="${index}">
                                    <button type="button" class="quantity-btn increase-qty" data-index="${index}">+</button>
                                </div>
                                <div class="text-end me-2" style="min-width: 80px;">
                                    <strong class="text-success small">MZN ${itemTotal.toFixed(2).replace('.', ',')}</strong>
                                </div>
                                <button type="button" class="remove-btn" data-index="${index}" title="Remover">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                    cartItemsList.insertAdjacentHTML('beforeend', cartItemHtml);
                });

                document.getElementById('finalize-sale').disabled = false;
                updateTotals();
            }

            // Atualizar totais
            function updateTotals() {
                let totalItems = 0;
                let totalAmount = 0;

                cart.forEach(item => {
                    totalItems += item.quantity;
                    totalAmount += item.unit_price * item.quantity;
                });

                document.getElementById('total-items').textContent = totalItems;
                document.getElementById('total-amount').textContent = 'MZN ' + totalAmount.toFixed(2).replace('.',
                    ',');
            }

            // Event delegation para botões dinâmicos
            document.getElementById('cart-items-list').addEventListener('click', function(e) {
                const target = e.target.closest('button');
                if (!target) return;

                const index = parseInt(target.getAttribute('data-index'));

                if (target.classList.contains('increase-qty')) {
                    const item = cart[index];
                    if (item.type === 'product' && item.quantity >= item.stock) {
                        showToast(`Stock insuficiente para ${item.name}`, 'error');
                        return;
                    }
                    cart[index].quantity++;
                    updateCartDisplay();
                } else if (target.classList.contains('decrease-qty')) {
                    if (cart[index].quantity > 1) {
                        cart[index].quantity--;
                        updateCartDisplay();
                    }
                } else if (target.classList.contains('remove-btn')) {
                    const removedItem = cart.splice(index, 1)[0];
                    updateCartDisplay();
                    showToast(`${removedItem.name} removido do carrinho!`, 'success');
                }
            });

            // Alterar quantidade diretamente
            document.getElementById('cart-items-list').addEventListener('change', function(e) {
                if (e.target.classList.contains('quantity-input')) {
                    const index = parseInt(e.target.getAttribute('data-index'));
                    const newQuantity = parseInt(e.target.value);
                    const item = cart[index];

                    if (isNaN(newQuantity) || newQuantity < 1) {
                        showToast('Quantidade inválida!', 'error');
                        e.target.value = item.quantity;
                        return;
                    }

                    if (item.type === 'product' && newQuantity > item.stock) {
                        showToast(`Stock insuficiente para ${item.name}`, 'error');
                        e.target.value = item.quantity;
                        return;
                    }

                    cart[index].quantity = newQuantity;
                    updateCartDisplay();
                }
            });

            // Limpar carrinho
            document.getElementById('clear-cart').addEventListener('click', function() {
                if (cart.length > 0) {
                    if (confirm('Tem certeza que deseja limpar o carrinho?')) {
                        cart = [];
                        updateCartDisplay();
                        showToast('Carrinho limpo!', 'success');
                    }
                }
            });

            // Submeter formulário principal
            document.getElementById('sale-form').addEventListener('submit', function(e) {
                if (cart.length === 0) {
                    showToast('Adicione pelo menos um produto ao carrinho!', 'error');
                    e.preventDefault();
                    return false;
                }

                if (!document.getElementById('payment_method').value) {
                    showToast('Selecione o método de pagamento!', 'error');
                    e.preventDefault();
                    return false;
                }

                // Adicionar dados do carrinho ao formulário
                const cartData = JSON.stringify(cart);

                // Remover input anterior se existir
                const existingInput = document.getElementById('cart-data-input');
                if (existingInput) {
                    existingInput.remove();
                }

                // Criar novo input
                const input = document.createElement('input');
                input.type = 'hidden';
                input.id = 'cart-data-input';
                input.name = 'items';
                input.value = cartData;
                this.appendChild(input);

                const submitBtn = document.getElementById('finalize-sale');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processando...';
            });

            // ===== GERENCIAMENTO DE PEDIDOS =====
            document.getElementById('save-as-order').addEventListener('click', function() {
                if (cart.length === 0) {
                    showToast('Adicione itens ao carrinho para criar um pedido!', 'error');
                    return;
                }

                const totalAmount = cart.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);

                // Preencher campos automaticamente
                document.getElementById('order_customer_name').value = document.getElementById(
                    'customer_name').value || '';
                document.getElementById('order_customer_phone').value = document.getElementById(
                    'customer_phone').value || '';

                // Gerar descrição baseada nos itens
                const itemsDescription = cart.map(item => `${item.name} (${item.quantity}x)`).join(', ');
                document.getElementById('order_description').value = `Pedido: ${itemsDescription}`;

                document.getElementById('estimated_amount').value = totalAmount.toFixed(2);
                document.getElementById('advance_payment').value = '0';

                // Resetar dívida
                document.getElementById('create_debt').checked = false;
                document.getElementById('debt_due_date_container').style.display = 'none';

                // Converter carrinho para formato esperado pelo OrderController
                const orderItems = cart.map(item => ({
                    product_id: item.product_id || null,
                    item_name: item.name,
                    description: `Produto: ${item.name}`,
                    quantity: item.quantity,
                    unit_price: item.unit_price
                }));

                document.getElementById('order-items-input').value = JSON.stringify(orderItems);

                // Mostrar offcanvas
                const offcanvas = new bootstrap.Offcanvas(document.getElementById(
                'orderCreationOffcanvas'));
                offcanvas.show();
            });

            // ===== CONTROLE DE DÍVIDA NO PEDIDO =====
            document.getElementById('create_debt').addEventListener('change', function() {
                const container = document.getElementById('debt_due_date_container');
                container.style.display = this.checked ? 'block' : 'none';

                if (this.checked && !document.getElementById('debt_due_date').value) {
                    // Definir data de vencimento para 30 dias após a entrega
                    const deliveryDate = document.getElementById('delivery_date').value;
                    if (deliveryDate) {
                        const dueDate = new Date(deliveryDate);
                        dueDate.setDate(dueDate.getDate() + 30);
                        document.getElementById('debt_due_date').value = dueDate.toISOString().split('T')[
                        0];
                    } else {
                        const dueDate = new Date();
                        dueDate.setDate(dueDate.getDate() + 30);
                        document.getElementById('debt_due_date').value = dueDate.toISOString().split('T')[
                        0];
                    }
                }
            });

            // ===== SUBMETER FORMULÁRIO DE PEDIDO =====
            document.getElementById('submit-order-form').addEventListener('click', function() {
                const form = document.getElementById('order-form');
                const formData = new FormData(form);
                const errorMessages = [];

                // Validação
                if (!formData.get('customer_name').trim()) {
                    errorMessages.push('Nome do cliente é obrigatório.');
                }
                if (!formData.get('description').trim()) {
                    errorMessages.push('Descrição do pedido é obrigatória.');
                }
                if (!formData.get('estimated_amount') || formData.get('estimated_amount') <= 0) {
                    errorMessages.push('Valor estimado deve ser maior que zero.');
                }

                if (errorMessages.length > 0) {
                    showToast(errorMessages.join(' '), 'error');
                    return;
                }

                // Desabilitar botão
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Criando...';

                // Enviar via fetch
                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro na requisição');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showToast(data.message || 'Pedido criado com sucesso!', 'success');

                            // Limpar carrinho
                            cart = [];
                            updateCartDisplay();

                            // Fechar offcanvas
                            const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById(
                                'orderCreationOffcanvas'));
                            if (offcanvas) {
                                offcanvas.hide();
                            }

                            // Redirecionar após pequeno delay
                            setTimeout(() => {
                                window.location.href = data.redirect ||
                                    "{{ route('orders.index') }}";
                            }, 1500);
                        } else {
                            throw new Error(data.message || 'Erro ao criar pedido');
                        }
                    })
                    .catch(err => {
                        console.error('Erro:', err);
                        showToast(err.message || 'Erro de conexão. Tente novamente.', 'error');
                    })
                    .finally(() => {
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-save me-2"></i> Criar Pedido';
                    });
            });

            // ===== GERENCIAMENTO DE DÍVIDAS =====
            document.getElementById('save-as-debt').addEventListener('click', function() {
                if (cart.length === 0) {
                    showToast('Adicione itens ao carrinho!', 'error');
                    return;
                }

                // Preencher dados
                document.getElementById('debt_customer_name').value = document.getElementById(
                    'customer_name').value || '';
                document.getElementById('debt_customer_phone').value = document.getElementById(
                    'customer_phone').value || '';
                document.getElementById('debt_date').value = new Date().toISOString().split('T')[0];

                // Data de vencimento padrão: 30 dias
                const dueDate = new Date();
                dueDate.setDate(dueDate.getDate() + 30);
                document.getElementById('due_date').value = dueDate.toISOString().split('T')[0];

                // Gerar descrição baseada nos itens
                const itemsDescription = cart.map(item => `${item.name} (${item.quantity}x)`).join(', ');
                document.getElementById('debt_description').value = `Venda a crédito: ${itemsDescription}`;

                // Preparar dados dos produtos para o controller
                const debtProducts = cart.map(item => ({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    unit_price: item.unit_price
                }));

                document.getElementById('debt-items-input').value = JSON.stringify(debtProducts);

                const offcanvas = new bootstrap.Offcanvas(document.getElementById('debtCreationOffcanvas'));
                offcanvas.show();
            });

            // ===== SUBMETER FORMULÁRIO DE DÍVIDA =====
            document.getElementById('submit-debt-form').addEventListener('click', function() {
                const errorMessages = [];

                // Validações
                const customerName = document.getElementById('debt_customer_name').value.trim();
                const debtDate = document.getElementById('debt_date').value;
                const dueDate = document.getElementById('due_date').value;
                const description = document.getElementById('debt_description').value.trim();

                if (!customerName) errorMessages.push('Nome do cliente é obrigatório.');
                if (!debtDate) errorMessages.push('Data da dívida é obrigatória.');
                if (!description) errorMessages.push('Descrição é obrigatória.');
                if (dueDate && new Date(dueDate) < new Date(debtDate)) {
                    errorMessages.push('Data de vencimento deve ser posterior à data da dívida.');
                }

                if (errorMessages.length > 0) {
                    showToast(errorMessages.join(' '), 'error');
                    return;
                }

                // Preparar dados
                const formData = new FormData();
                formData.append('customer_name', customerName);
                formData.append('customer_phone', document.getElementById('debt_customer_phone').value);
                formData.append('debt_date', debtDate);
                formData.append('due_date', dueDate);
                formData.append('description', description);
                formData.append('notes', document.getElementById('debt_notes').value);
                formData.append('products', document.getElementById('debt-items-input').value);

                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Criando...';

                fetch('{{ route('debts.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro na requisição');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showToast(data.message || 'Dívida criada com sucesso!', 'success');

                            // Limpar carrinho
                            cart = [];
                            updateCartDisplay();

                            // Fechar offcanvas
                            const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById(
                                'debtCreationOffcanvas'));
                            if (offcanvas) {
                                offcanvas.hide();
                            }

                            // Redirecionar após pequeno delay
                            setTimeout(() => {
                                window.location.href = data.redirect ||
                                    "{{ route('debts.index') }}";
                            }, 1500);
                        } else {
                            throw new Error(data.message || 'Erro ao criar dívida');
                        }
                    })
                    .catch(err => {
                        console.error('Erro:', err);
                        showToast(err.message || 'Erro de conexão. Tente novamente.', 'error');
                    })
                    .finally(() => {
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-save me-2"></i> Criar Dívida';
                    });
            });

            // Inicializar display do carrinho
            updateCartDisplay();
        });
    </script>
@endpush
