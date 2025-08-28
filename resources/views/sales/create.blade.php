@extends('layouts.app')

@section('title', 'Nova Venda')
@section('title-icon', 'fa-cash-register')
@section('page-title', 'Nova Venda')

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('sales.index') }}">Vendas</a>
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
                                            @if($product->type === 'product')
                                                <i class="fas fa-box text-primary fa-2x"></i>
                                            @else
                                                <i class="fas fa-tools text-info fa-2x"></i>
                                            @endif
                                        </div>
                                        <h6 class="card-title fw-bold mb-1">{{ $product->name }}</h6>
                                        <p class="text-success fw-bold mb-2">
                                            MZN {{ number_format($product->selling_price, 2, ',', '.') }}
                                        </p>
                                        
                                        @if($product->type === 'product')
                                            <small class="text-muted d-block mb-2">
                                                <i class="fas fa-cubes me-1"></i>Stock: {{ $product->stock_quantity }}
                                            </small>
                                        @else
                                            <small class="text-info d-block mb-2">
                                                <i class="fas fa-concierge-bell me-1"></i>Serviço
                                            </small>
                                        @endif
                                        
                                        <button class="btn btn-outline-primary btn-sm add-product-btn"
                                                data-id="{{ $product->id }}"
                                                data-name="{{ $product->name }}"
                                                data-price="{{ $product->selling_price }}"
                                                data-type="{{ $product->type }}"
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
                                <input type="text" class="form-control" id="customer_name" 
                                       name="customer_name" placeholder="Digite o nome do cliente">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="customer_phone" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="customer_phone" 
                                       name="customer_phone" placeholder="(xx) xxxxx-xxxx">
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
                        
                        <!-- Botão Finalizar -->
                        <button type="submit" class="btn btn-success w-100" id="finalize-sale" disabled>
                            <i class="fas fa-check me-2"></i> Finalizar Venda
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .product-card {
        transition: all 0.3s ease;
        border: 1px solid #dee2e6;
        cursor: pointer;
    }
    
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-color: #007bff;
    }
    
    .cart-item {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 8px;
        background: #f8f9fa;
        transition: all 0.2s ease;
    }
    
    .cart-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .quantity-btn {
        width: 28px;
        height: 28px;
        border: 1px solid #ccc;
        background: white;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        transition: all 0.2s ease;
    }
    
    .quantity-btn:hover {
        background: #f8f9fa;
        border-color: #007bff;
        color: #007bff;
    }
    
    .quantity-input {
        width: 50px;
        text-align: center;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 2px 4px;
        height: 28px;
    }
    
    .remove-btn {
        background: none;
        border: none;
        color: #dc3545;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    
    .remove-btn:hover {
        background: #f8d7da;
        transform: scale(1.1);
    }
    
    .product-item.hidden {
        display: none !important;
    }
    
    .add-product-btn:hover {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }
    
    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let cart = [];

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

    // Função para mostrar notificações
    function showNotification(type, message) {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        } else {
            alert(message);
        }
    }

    // Adicionar produto ao carrinho
    document.querySelectorAll('.add-product-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = parseInt(this.getAttribute('data-id'));
            const productName = this.getAttribute('data-name');
            const productPrice = parseFloat(this.getAttribute('data-price'));
            const productType = this.getAttribute('data-type');
            const stockQuantity = parseInt(this.getAttribute('data-stock'));

            console.log('Produto clicado:', { productId, productName, productPrice, productType, stockQuantity });

            // Verificar stock para produtos
            if (productType === 'product') {
                const currentItem = cart.find(item => item.product_id === productId);
                const quantityInCart = currentItem ? currentItem.quantity : 0;
                
                if (quantityInCart >= stockQuantity) {
                    showNotification('error', `Stock insuficiente para ${productName}`);
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
            
            console.log('Carrinho atualizado:', cart);
            updateCartDisplay();
            showNotification('success', `${productName} adicionado ao carrinho!`);
        });
    });

    // Atualizar exibição do carrinho
    function updateCartDisplay() {
        const cartItemsList = document.getElementById('cart-items-list');
        const emptyMessage = document.getElementById('empty-cart-message');
        
        console.log('Atualizando carrinho, items:', cart.length);
        
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
        document.getElementById('total-amount').textContent = 'MZN ' + totalAmount.toFixed(2).replace('.', ',');
        
        console.log('Totais atualizados:', { totalItems, totalAmount });
    }

    // Event delegation para botões dinâmicos
    document.getElementById('cart-items-list').addEventListener('click', function(e) {
        const target = e.target.closest('button');
        if (!target) return;

        const index = parseInt(target.getAttribute('data-index'));
        
        if (target.classList.contains('increase-qty')) {
            const item = cart[index];
            if (item.type === 'product' && item.quantity >= item.stock) {
                showNotification('error', `Stock insuficiente para ${item.name}`);
                return;
            }
            cart[index].quantity++;
            updateCartDisplay();
        }
        else if (target.classList.contains('decrease-qty')) {
            if (cart[index].quantity > 1) {
                cart[index].quantity--;
                updateCartDisplay();
            }
        }
        else if (target.classList.contains('remove-btn')) {
            const removedItem = cart.splice(index, 1)[0];
            updateCartDisplay();
            showNotification('success', `${removedItem.name} removido do carrinho!`);
        }
    });

    // Alterar quantidade diretamente
    document.getElementById('cart-items-list').addEventListener('change', function(e) {
        if (e.target.classList.contains('quantity-input')) {
            const index = parseInt(e.target.getAttribute('data-index'));
            const newQuantity = parseInt(e.target.value);
            const item = cart[index];
            
            if (isNaN(newQuantity) || newQuantity < 1) {
                showNotification('error', 'Quantidade inválida!');
                e.target.value = item.quantity;
                return;
            }
            
            if (item.type === 'product' && newQuantity > item.stock) {
                showNotification('error', `Stock insuficiente para ${item.name}`);
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
                showNotification('success', 'Carrinho limpo!');
            }
        }
    });

    // Submeter formulário
    document.getElementById('sale-form').addEventListener('submit', function(e) {
        if (cart.length === 0) {
            showNotification('error', 'Adicione pelo menos um produto ao carrinho!');
            e.preventDefault();
            return false;
        }
        
        if (!document.getElementById('payment_method').value) {
            showNotification('error', 'Selecione o método de pagamento!');
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
        
        document.getElementById('finalize-sale').disabled = true;
        document.getElementById('finalize-sale').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processando...';
        
        console.log('Enviando dados:', { cart: cartData });
    });

    // Inicializar
    updateCartDisplay();
});
</script>
@endpush