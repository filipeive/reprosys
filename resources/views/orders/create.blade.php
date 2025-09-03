@extends('layouts.app')

@section('title', isset($order) ? 'Editar Pedido #' . $order->id : 'Novo Pedido')
@section('page-title', isset($order) ? 'Editar Pedido #' . $order->id : 'Novo Pedido')
@section('title-icon', 'fa-clipboard-list')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Pedidos</a></li>
    @if(isset($order))
        <li class="breadcrumb-item"><a href="{{ route('orders.show', $order) }}">Pedido #{{ $order->id }}</a></li>
        <li class="breadcrumb-item active">Editar</li>
    @else
        <li class="breadcrumb-item active">Novo Pedido</li>
    @endif
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <form id="order-form" method="POST" action="{{ isset($order) ? route('orders.update', $order) : route('orders.store') }}">
                @csrf
                @if(isset($order))
                    @method('PUT')
                @endif

                <!-- Informações do Cliente -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>Informações do Cliente
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nome do Cliente *</label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                       name="customer_name" value="{{ old('customer_name', $order->customer_name ?? '') }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Telefone</label>
                                <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                                       name="customer_phone" value="{{ old('customer_phone', $order->customer_phone ?? '') }}">
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                       name="customer_email" value="{{ old('customer_email', $order->customer_email ?? '') }}">
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prioridade *</label>
                                <select class="form-select @error('priority') is-invalid @enderror" name="priority" required>
                                    <option value="">Selecione a prioridade</option>
                                    <option value="low" {{ old('priority', $order->priority ?? '') === 'low' ? 'selected' : '' }}>Baixa</option>
                                    <option value="medium" {{ old('priority', $order->priority ?? '') === 'medium' ? 'selected' : '' }}>Média</option>
                                    <option value="high" {{ old('priority', $order->priority ?? '') === 'high' ? 'selected' : '' }}>Alta</option>
                                    <option value="urgent" {{ old('priority', $order->priority ?? '') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                @if(!isset($order) || $order->canBeCompleted())
                <!-- Seleção de Itens -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-box me-2"></i>Itens do Pedido
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Seletor de Produtos -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Adicionar Produto/Serviço</label>
                                <select class="form-select" id="product-select">
                                    <option value="">Selecione um produto ou serviço...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                                data-name="{{ $product->name }}"
                                                data-price="{{ $product->selling_price }}"
                                                data-stock="{{ $product->stock_quantity }}">
                                            {{ $product->name }} - MT {{ number_format($product->selling_price, 2, ',', '.') }}
                                            @if($product->stock_quantity > 0)
                                                (Estoque: {{ $product->stock_quantity }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-success w-100" onclick="addItemToCart()">
                                    <i class="fas fa-plus me-2"></i>Adicionar Item
                                </button>
                            </div>
                        </div>

                        <!-- Ou adicionar item personalizado -->
                        <div class="border-top pt-4 mb-4">
                            <h6 class="text-muted mb-3">Ou adicionar item personalizado:</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="custom-item-name" placeholder="Nome do item">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="custom-item-quantity" placeholder="Quantidade" min="1" value="1">
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text">MT</span>
                                        <input type="number" class="form-control" id="custom-item-price" placeholder="Preço" step="0.01" min="0">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-success w-100" onclick="addCustomItem()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Carrinho de Itens -->
                        <div class="table-responsive">
                            <table class="table table-bordered" id="cart-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center" style="width: 120px;">Quantidade</th>
                                        <th class="text-end" style="width: 130px;">Preço Unit.</th>
                                        <th class="text-end" style="width: 130px;">Total</th>
                                        <th class="text-center" style="width: 80px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    <!-- Itens serão adicionados aqui via JavaScript -->
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total Estimado:</td>
                                        <td class="text-end fw-bold fs-5" id="cart-total">MT 0,00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Detalhes do Pedido -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Detalhes do Pedido
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Descrição do Pedido *</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          name="description" rows="3" required>{{ old('description', $order->description ?? '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Valor Estimado *</label>
                                <div class="input-group">
                                    <span class="input-group-text">MT</span>
                                    <input type="number" step="0.01" class="form-control @error('estimated_amount') is-invalid @enderror" 
                                           name="estimated_amount" id="estimated-amount" 
                                           value="{{ old('estimated_amount', $order->estimated_amount ?? '') }}" required>
                                </div>
                                @error('estimated_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Sinal Recebido</label>
                                <div class="input-group">
                                    <span class="input-group-text">MT</span>
                                    <input type="number" step="0.01" class="form-control @error('advance_payment') is-invalid @enderror" 
                                           name="advance_payment" value="{{ old('advance_payment', $order->advance_payment ?? '') }}">
                                </div>
                                @error('advance_payment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Data de Entrega</label>
                                <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" 
                                       name="delivery_date" value="{{ old('delivery_date', $order && $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '') }}" 
                                       min="{{ now()->addDay()->format('Y-m-d') }}">
                                @error('delivery_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @if(isset($order))
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status">
                                    <option value="pending" {{ old('status', $order->status) === 'pending' ? 'selected' : '' }}>Pendente</option>
                                    <option value="in_progress" {{ old('status', $order->status) === 'in_progress' ? 'selected' : '' }}>Em Andamento</option>
                                    <option value="completed" {{ old('status', $order->status) === 'completed' ? 'selected' : '' }}>Concluído</option>
                                    <option value="delivered" {{ old('status', $order->status) === 'delivered' ? 'selected' : '' }}>Entregue</option>
                                    <option value="cancelled" {{ old('status', $order->status) === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Observações</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          name="notes" rows="3">{{ old('notes', $order->notes ?? '') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @if(isset($order))
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Notas Internas</label>
                                <textarea class="form-control @error('internal_notes') is-invalid @enderror" 
                                          name="internal_notes" rows="3">{{ old('internal_notes', $order->internal_notes ?? '') }}</textarea>
                                @error('internal_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                        </div>

                        @if(!isset($order))
                        <!-- Opções para novo pedido -->
                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="create_debt" id="create-debt">
                                    <label class="form-check-label fw-semibold" for="create-debt">
                                        Criar dívida para o valor restante
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6" id="debt-due-date-container" style="display: none;">
                                <label class="form-label fw-semibold">Vencimento da Dívida</label>
                                <input type="date" class="form-control @error('debt_due_date') is-invalid @enderror" 
                                       name="debt_due_date" min="{{ now()->format('Y-m-d') }}">
                                @error('debt_due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Voltar
                            </a>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary" onclick="saveDraft()">
                                    <i class="fas fa-save me-2"></i>Salvar Rascunho
                                </button>
                                <button type="submit" class="btn btn-success" id="save-order-btn">
                                    <i class="fas fa-check me-2"></i>
                                    {{ isset($order) ? 'Atualizar Pedido' : 'Criar Pedido' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let cartItems = {{ isset($order) && $order->items ? $order->items->map(function($item) { 
            return [
                'id' => $item->product_id,
                'name' => $item->item_name,
                'price' => $item->unit_price,
                'quantity' => $item->quantity
            ];
        })->toJson() : '[]' }};

        document.addEventListener('DOMContentLoaded', function() {
            // Carregar itens existentes se for edição
            if (cartItems.length > 0) {
                updateCart();
            }

            // Gerenciar checkbox de dívida
            const createDebtCheck = document.getElementById('create-debt');
            const debtContainer = document.getElementById('debt-due-date-container');
            
            if (createDebtCheck) {
                createDebtCheck.addEventListener('change', function() {
                    if (this.checked) {
                        debtContainer.style.display = 'block';
                        // Definir data padrão (30 dias)
                        const futureDate = new Date();
                        futureDate.setDate(futureDate.getDate() + 30);
                        document.querySelector('input[name="debt_due_date"]').value = futureDate.toISOString().split('T')[0];
                    } else {
                        debtContainer.style.display = 'none';
                    }
                });
            }
        });

        // Função para adicionar item do produto selecionado
        function addItemToCart() {
            const select = document.getElementById('product-select');
            const productId = select.value;
            
            if (!productId) {
                alert('Selecione um produto ou serviço');
                return;
            }

            const option = select.options[select.selectedIndex];
            const product = {
                id: productId,
                name: option.dataset.name,
                price: parseFloat(option.dataset.price),
                quantity: 1
            };

            // Verificar se já existe no carrinho
            const existing = cartItems.find(item => item.id === productId);
            if (existing) {
                existing.quantity += 1;
            } else {
                cartItems.push(product);
            }

            updateCart();
            select.value = '';
        }

        // Função para adicionar item personalizado
        function addCustomItem() {
            const name = document.getElementById('custom-item-name').value.trim();
            const quantity = parseInt(document.getElementById('custom-item-quantity').value) || 1;
            const price = parseFloat(document.getElementById('custom-item-price').value) || 0;

            if (!name) {
                alert('Digite o nome do item');
                return;
            }

            if (price <= 0) {
                alert('Digite um preço válido');
                return;
            }

            const customItem = {
                id: 'custom_' + Date.now(), // ID único para itens personalizados
                name: name,
                price: price,
                quantity: quantity
            };

            cartItems.push(customItem);
            updateCart();

            // Limpar campos
            document.getElementById('custom-item-name').value = '';
            document.getElementById('custom-item-quantity').value = '1';
            document.getElementById('custom-item-price').value = '';
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
                    <td>
                        <strong>${item.name}</strong>
                        ${item.id && !item.id.toString().startsWith('custom_') ? '<small class="text-muted d-block">Produto do catálogo</small>' : '<small class="text-success d-block">Item personalizado</small>'}
                    </td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm" 
                               value="${item.quantity}" min="1" style="width: 80px; margin: 0 auto;" 
                               onchange="updateItemQuantity(${index}, this.value)">
                    </td>
                    <td class="text-end">MT ${item.price.toFixed(2).replace('.', ',')}</td>
                    <td class="text-end fw-semibold">MT ${itemTotal.toFixed(2).replace('.', ',')}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${index})" title="Remover">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            // Atualizar total
            totalEl.textContent = `MT ${total.toFixed(2).replace('.', ',')}`;
            
            // Sincronizar com campo de valor estimado
            const estimatedField = document.getElementById('estimated-amount');
            if (estimatedField && total > 0) {
                estimatedField.value = total.toFixed(2);
            }
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
            if (confirm('Remover este item do pedido?')) {
                cartItems.splice(index, 1);
                updateCart();
            }
        }

        // Submit do formulário
        document.getElementById('order-form').addEventListener('submit', function(e) {
            @if(!isset($order) || $order->canBeCompleted())
            // Validar se há itens no carrinho apenas para novos pedidos
            if (cartItems.length === 0) {
                e.preventDefault();
                alert('Adicione pelo menos um item ao pedido');
                return;
            }

            // Adicionar itens como campo oculto
            const itemsInput = document.createElement('input');
            itemsInput.type = 'hidden';
            itemsInput.name = 'items';
            itemsInput.value = JSON.stringify(cartItems);
            this.appendChild(itemsInput);
            @endif

            // Desabilitar botão para evitar duplo submit
            const submitBtn = document.getElementById('save-order-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';
        });

        // Função para salvar rascunho
        function saveDraft() {
            // Implementar salvamento em localStorage ou sessão
            const formData = new FormData(document.getElementById('order-form'));
            formData.set('items', JSON.stringify(cartItems));
            formData.set('is_draft', '1');
            
            // Aqui você pode implementar o salvamento do rascunho
            alert('Funcionalidade de rascunho será implementada');
        }
    </script>
@endpush

@push('styles')
    <style>
        .card {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: none;
        }
        
        .card-header {
            border-bottom: 2px solid rgba(0,0,0,0.1);
        }
        
        .table-bordered td, .table-bordered th {
            border-color: #dee2e6;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .btn {
            padding: 0.5rem 1rem;
        }
        
        .table-responsive {
            border-radius: 0.375rem;
            overflow: hidden;
        }
    </style>
@endpush