@extends('adminlte::page')

@section('title', 'Nova Venda - Sistema Reprografia')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-cash-register text-primary"></i> 
            Nova Venda
        </h1>
        <div class="breadcrumb-item active">
            <i class="fas fa-print text-info"></i> Sistema Reprografia
        </div>
    </div>
@stop

@section('content')
    <!-- Notificações -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <i class="fas fa-check mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <h5><i class="fas fa-exclamation-triangle mr-2"></i>Erros no formulário:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Produtos -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-boxes mr-2"></i> Produtos & Serviços
                    </h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" class="form-control" id="product-search" 
                                   placeholder="Pesquisar produtos...">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="products-container">
                        @foreach ($products as $product)
                            <div class="col-md-6 col-lg-4 mb-3 product-item" data-name="{{ strtolower($product->name) }}">
                                <div class="card product-card h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="product-icon mb-2">
                                            @if($product->type === 'product')
                                                <i class="fas fa-box text-primary fa-2x"></i>
                                            @else
                                                <i class="fas fa-tools text-info fa-2x"></i>
                                            @endif
                                        </div>
                                        <h6 class="card-title font-weight-bold">{{ $product->name }}</h6>
                                        <p class="text-success font-weight-bold mb-2">
                                            MT {{ number_format($product->selling_price, 2, ',', '.') }}
                                        </p>
                                        
                                        @if($product->type === 'product')
                                            <small class="text-muted d-block mb-2">
                                                <i class="fas fa-cubes mr-1"></i>Stock: {{ $product->stock_quantity }}
                                            </small>
                                        @else
                                            <small class="text-info d-block mb-2">
                                                <i class="fas fa-concierge-bell mr-1"></i>Serviço
                                            </small>
                                        @endif
                                        
                                        <button class="btn btn-outline-primary btn-sm add-product-btn"
                                                data-id="{{ $product->id }}"
                                                data-name="{{ $product->name }}"
                                                data-price="{{ $product->selling_price }}"
                                                data-type="{{ $product->type }}"
                                                data-stock="{{ $product->stock_quantity }}">
                                            <i class="fas fa-plus mr-1"></i> Adicionar
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
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title">
                        <i class="fas fa-shopping-cart mr-2"></i> Carrinho
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-outline-light btn-sm" id="clear-cart">
                            <i class="fas fa-trash mr-1"></i> Limpar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="sale-form" action="{{ route('sales.store') }}" method="POST">
                        @csrf
                        
                        <!-- Cliente -->
                        <div class="form-group">
                            <label for="customer_name">Nome do Cliente</label>
                            <input type="text" class="form-control" id="customer_name" 
                                   name="customer_name" placeholder="Digite o nome do cliente">
                        </div>
                        
                        <div class="form-group">
                            <label for="customer_phone">Telefone</label>
                            <input type="text" class="form-control" id="customer_phone" 
                                   name="customer_phone" placeholder="(xx) xxxxx-xxxx">
                        </div>
                        
                        <!-- Itens do Carrinho -->
                        <div class="cart-items mb-3">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-list mr-1"></i> Itens no Carrinho
                            </h6>
                            <div id="cart-items-list">
                                <div class="text-center text-muted py-4" id="empty-cart-message">
                                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                    <p>Carrinho vazio</p>
                                    <small>Clique nos produtos para adicionar</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total -->
                        <div class="bg-light p-3 rounded mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Total de Itens:</span>
                                <span id="total-items">0</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <strong>Total Geral:</strong>
                                <strong class="text-success" id="total-amount">MT 0,00</strong>
                            </div>
                        </div>
                        
                        <!-- Pagamento -->
                        <div class="form-group">
                            <label for="payment_method">Método de Pagamento</label>
                            <select class="form-control" id="payment_method" name="payment_method" required>
                                <option value="">Selecione...</option>
                                <option value="cash">Dinheiro</option>
                                <option value="card">Cartão</option>
                                <option value="transfer">Transferência</option>
                                <option value="credit">Crédito</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Observações</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" 
                                      placeholder="Observações sobre a venda..."></textarea>
                        </div>
                        
                        <!-- Botão Finalizar -->
                        <button type="submit" class="btn btn-success btn-block" id="finalize-sale" disabled>
                            <i class="fas fa-check mr-2"></i> Finalizar Venda
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .product-card {
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
        }
        
        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .cart-item {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            background: #f8f9fa;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .quantity-btn {
            width: 25px;
            height: 25px;
            border: 1px solid #ccc;
            background: white;
            cursor: pointer;
        }
        
        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid #ccc;
            padding: 2px;
        }
        
        .remove-btn {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
        }
        
        .remove-btn:hover {
            color: #c82333;
        }
        
        .product-item.hidden {
            display: none;
        }
        
        .alert {
            border-radius: 5px;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card-header {
            border-radius: 10px 10px 0 0;
        }
        
        .btn {
            border-radius: 5px;
        }
        
        .form-control {
            border-radius: 5px;
        }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {
            let cart = [];

            // Aplicar máscara no telefone
            $('#customer_phone').mask('(00) 00000-0000');

            // Pesquisa de produtos
            $('#product-search').on('input', function() {
                const search = $(this).val().toLowerCase();
                $('.product-item').each(function() {
                    const productName = $(this).data('name');
                    if (productName.includes(search)) {
                        $(this).removeClass('hidden');
                    } else {
                        $(this).addClass('hidden');
                    }
                });
            });

            // Função para mostrar notificações
            function showNotification(type, message) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const icon = type === 'success' ? 'fa-check' : 'fa-exclamation-triangle';
                
                const alert = $(`
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        <i class="fas ${icon} mr-2"></i>${message}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                `);
                
                $('body').prepend(alert);
                
                setTimeout(() => {
                    alert.fadeOut(() => alert.remove());
                }, 3000);
            }

            // Adicionar produto ao carrinho
            $('.add-product-btn').click(function() {
                const productId = parseInt($(this).data('id'));
                const productName = $(this).data('name');
                const productPrice = parseFloat($(this).data('price'));
                const productType = $(this).data('type');
                const stockQuantity = parseInt($(this).data('stock'));

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

            // Atualizar exibição do carrinho
            function updateCartDisplay() {
                const cartItemsList = $('#cart-items-list');
                const emptyMessage = $('#empty-cart-message');
                
                console.log('Atualizando carrinho, items:', cart.length);
                
                if (cart.length === 0) {
                    emptyMessage.show();
                    cartItemsList.find('.cart-item').remove();
                    $('#finalize-sale').prop('disabled', true);
                    updateTotals();
                    return;
                }
                
                emptyMessage.hide();
                cartItemsList.find('.cart-item').remove();
                
                cart.forEach((item, index) => {
                    const itemTotal = item.unit_price * item.quantity;
                    const icon = item.type === 'service' ? 'fa-tools text-info' : 'fa-box text-primary';
                    
                    const cartItemHtml = `
                        <div class="cart-item" data-index="${index}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas ${icon} mr-2"></i>
                                        <strong>${item.name}</strong>
                                    </div>
                                    <small class="text-muted">
                                        MT ${item.unit_price.toFixed(2).replace('.', ',')} x ${item.quantity}
                                    </small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="quantity-controls mr-2">
                                        <button type="button" class="quantity-btn decrease-qty" data-index="${index}">-</button>
                                        <input type="number" class="quantity-input" value="${item.quantity}" 
                                               min="1" data-index="${index}">
                                        <button type="button" class="quantity-btn increase-qty" data-index="${index}">+</button>
                                    </div>
                                    <div class="text-right mr-2">
                                        <strong class="text-success">MT ${itemTotal.toFixed(2).replace('.', ',')}</strong>
                                    </div>
                                    <button type="button" class="remove-btn" data-index="${index}" title="Remover">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    cartItemsList.append(cartItemHtml);
                });
                
                $('#finalize-sale').prop('disabled', false);
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
                
                $('#total-items').text(totalItems);
                $('#total-amount').text('MT ' + totalAmount.toFixed(2).replace('.', ','));
                
                console.log('Totais atualizados:', { totalItems, totalAmount });
            }

            // Aumentar quantidade
            $(document).on('click', '.increase-qty', function() {
                const index = parseInt($(this).data('index'));
                const item = cart[index];
                
                if (item.type === 'product' && item.quantity >= item.stock) {
                    showNotification('error', `Stock insuficiente para ${item.name}`);
                    return;
                }
                
                cart[index].quantity++;
                updateCartDisplay();
            });

            // Diminuir quantidade
            $(document).on('click', '.decrease-qty', function() {
                const index = parseInt($(this).data('index'));
                if (cart[index].quantity > 1) {
                    cart[index].quantity--;
                    updateCartDisplay();
                }
            });

            // Alterar quantidade diretamente
            $(document).on('change', '.quantity-input', function() {
                const index = parseInt($(this).data('index'));
                const newQuantity = parseInt($(this).val());
                const item = cart[index];
                
                if (isNaN(newQuantity) || newQuantity < 1) {
                    showNotification('error', 'Quantidade inválida!');
                    $(this).val(item.quantity);
                    return;
                }
                
                if (item.type === 'product' && newQuantity > item.stock) {
                    showNotification('error', `Stock insuficiente para ${item.name}`);
                    $(this).val(item.quantity);
                    return;
                }
                
                cart[index].quantity = newQuantity;
                updateCartDisplay();
            });

            // Remover item
            $(document).on('click', '.remove-btn', function() {
                const index = parseInt($(this).data('index'));
                const removedItem = cart.splice(index, 1)[0];
                updateCartDisplay();
                showNotification('success', `${removedItem.name} removido do carrinho!`);
            });

            // Limpar carrinho
            $('#clear-cart').click(function() {
                if (cart.length > 0) {
                    if (confirm('Tem certeza que deseja limpar o carrinho?')) {
                        cart = [];
                        updateCartDisplay();
                        showNotification('success', 'Carrinho limpo!');
                    }
                }
            });

            // Submeter formulário
            $('#sale-form').submit(function(e) {
                if (cart.length === 0) {
                    showNotification('error', 'Adicione pelo menos um produto ao carrinho!');
                    e.preventDefault();
                    return false;
                }
                
                if (!$('#payment_method').val()) {
                    showNotification('error', 'Selecione o método de pagamento!');
                    e.preventDefault();
                    return false;
                }
                
                // Adicionar dados do carrinho ao formulário
                const cartData = JSON.stringify(cart);
                $('<input>').attr({
                    type: 'hidden',
                    name: 'items',
                    value: cartData
                }).appendTo(this);
                
                $('#finalize-sale').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Processando...');
                
                console.log('Enviando dados:', { cart: cartData });
            });

            // Inicializar
            updateCartDisplay();
        });
    </script>
@stop