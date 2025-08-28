@extends('layouts.app')

@section('title', 'Novo Pedido')
@section('title-icon', 'fa-plus-circle')
@section('page-title', 'Criar Novo Pedido')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Pedidos</a></li>
    <li class="breadcrumb-item active">Novo Pedido</li>
@endsection

@section('content')
    <form id="orderForm" method="POST" action="{{ route('orders.store') }}" class="fade-in">
        @csrf

        <div class="row">
            <!-- Dados do Cliente -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            Dados do Cliente
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nome do Cliente *</label>
                            <input type="text" name="customer_name"
                                class="form-control @error('customer_name') is-invalid @enderror"
                                value="{{ old('customer_name') }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="customer_phone"
                                class="form-control @error('customer_phone') is-invalid @enderror"
                                value="{{ old('customer_phone') }}" placeholder="(+258) 84 123 4567">
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="customer_email"
                                class="form-control @error('customer_email') is-invalid @enderror"
                                value="{{ old('customer_email') }}" placeholder="cliente@email.com">
                            @error('customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Configurações do Pedido -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-cog me-2"></i>
                            Configurações
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Data de Entrega</label>
                            <input type="datetime-local" name="delivery_date"
                                class="form-control @error('delivery_date') is-invalid @enderror"
                                value="{{ old('delivery_date') }}">
                            @error('delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Prioridade *</label>
                            <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Baixa</option>
                                <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Média
                                </option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgente
                                </option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pagamento -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-money-bill me-2"></i>
                            Valores
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Valor Estimado Total *</label>
                            <div class="input-group">
                                <span class="input-group-text">MT</span>
                                <input type="number" step="0.01" name="estimated_amount" id="estimated_amount"
                                    class="form-control @error('estimated_amount') is-invalid @enderror"
                                    value="{{ old('estimated_amount') }}" required readonly>
                            </div>
                            @error('estimated_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sinal/Entrada</label>
                            <div class="input-group">
                                <span class="input-group-text">MT</span>
                                <input type="number" step="0.01" name="advance_payment" id="advance_payment"
                                    class="form-control @error('advance_payment') is-invalid @enderror"
                                    value="{{ old('advance_payment', 0) }}">
                            </div>
                            @error('advance_payment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="create_debt" id="create_debt" class="form-check-input"
                                value="1" {{ old('create_debt') ? 'checked' : '' }}>
                            <label class="form-check-label" for="create_debt">
                                Criar dívida para valor restante
                            </label>
                        </div>

                        <div id="debt_due_date_section" class="mb-3" style="display: none;">
                            <label class="form-label">Data Limite Pagamento</label>
                            <input type="date" name="debt_due_date" class="form-control"
                                value="{{ old('debt_due_date') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalhes do Pedido -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Descrição do Trabalho
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Descrição Geral *</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required
                                placeholder="Descreva o trabalho solicitado...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2"
                                placeholder="Observações adicionais...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Itens do Pedido -->
                <div class="card">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Itens do Pedido
                        </h5>
                        <button type="button" id="addItem" class="btn btn-light btn-sm">
                            <i class="fas fa-plus me-1"></i> Adicionar Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="itemsContainer">
                            <div class="item-row mb-3 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Nome do Item *</label>
                                        <input type="text" name="items[0][item_name]" class="form-control item-name"
                                            required placeholder="Ex: Impressão colorida A4">
                                        <input type="hidden" name="items[0][product_id]" class="product-id">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Quantidade *</label>
                                        <input type="number" name="items[0][quantity]"
                                            class="form-control item-quantity" min="1" value="1" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Preço Unitário *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">MT</span>
                                            <input type="number" step="0.01" name="items[0][unit_price]"
                                                class="form-control item-price" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Total</label>
                                        <div class="form-control-plaintext item-total fw-bold">MT 0,00</div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <label class="form-label">Descrição</label>
                                        <input type="text" name="items[0][description]" class="form-control"
                                            placeholder="Detalhes específicos do item">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm mt-2 remove-item"
                                    style="display: none;">
                                    <i class="fas fa-trash"></i> Remover
                                </button>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded">
                                    <div class="d-flex justify-content-between">
                                        <strong>Total Geral:</strong>
                                        <strong id="grandTotal">MT 0,00</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Voltar
                    </a>
                    <div>
                        <button type="button" class="btn btn-outline-primary me-2" onclick="saveDraft()">
                            <i class="fas fa-save me-2"></i> Salvar Rascunho
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i> Criar Pedido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let itemCounter = 1;

            // Elementos
            const addItemBtn = document.getElementById('addItem');
            const itemsContainer = document.getElementById('itemsContainer');
            const createDebtCheck = document.getElementById('create_debt');
            const debtDueDateSection = document.getElementById('debt_due_date_section');

            // Event Listeners
            addItemBtn.addEventListener('click', addNewItem);
            createDebtCheck.addEventListener('change', toggleDebtSection);

            // Configurar eventos iniciais
            setupItemEvents(document.querySelector('.item-row'));

            function addNewItem() {
                const newItem = createItemHTML(itemCounter);
                itemsContainer.insertAdjacentHTML('beforeend', newItem);

                const newItemElement = itemsContainer.lastElementChild;
                setupItemEvents(newItemElement);
                updateRemoveButtons();

                itemCounter++;
            }

            function createItemHTML(index) {
                return `
            <div class="item-row mb-3 p-3 border rounded">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Nome do Item *</label>
                        <input type="text" name="items[${index}][item_name]" class="form-control item-name" required 
                               placeholder="Ex: Impressão colorida A4">
                        <input type="hidden" name="items[${index}][product_id]" class="product-id">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quantidade *</label>
                        <input type="number" name="items[${index}][quantity]" class="form-control item-quantity" 
                               min="1" value="1" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Preço Unitário *</label>
                        <div class="input-group">
                            <span class="input-group-text">MT</span>
                            <input type="number" step="0.01" name="items[${index}][unit_price]" 
                                   class="form-control item-price" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Total</label>
                        <div class="form-control-plaintext item-total fw-bold">MT 0,00</div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <label class="form-label">Descrição</label>
                        <input type="text" name="items[${index}][description]" class="form-control" 
                               placeholder="Detalhes específicos do item">
                    </div>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm mt-2 remove-item">
                    <i class="fas fa-trash"></i> Remover
                </button>
            </div>
        `;
            }

            function setupItemEvents(itemElement) {
                const quantityInput = itemElement.querySelector('.item-quantity');
                const priceInput = itemElement.querySelector('.item-price');
                const removeBtn = itemElement.querySelector('.remove-item');

                quantityInput.addEventListener('input', calculateItemTotal);
                priceInput.addEventListener('input', calculateItemTotal);

                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        itemElement.remove();
                        updateRemoveButtons();
                        calculateGrandTotal();
                    });
                }
            }

            function calculateItemTotal(event) {
                const itemRow = event.target.closest('.item-row');
                const quantity = parseFloat(itemRow.querySelector('.item-quantity').value) || 0;
                const price = parseFloat(itemRow.querySelector('.item-price').value) || 0;
                const total = quantity * price;

                itemRow.querySelector('.item-total').textContent = `MT ${total.toFixed(2).replace('.', ',')}`;
                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let grandTotal = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
                    const price = parseFloat(row.querySelector('.item-price').value) || 0;
                    grandTotal += quantity * price;
                });

                document.getElementById('grandTotal').textContent = `MT ${grandTotal.toFixed(2).replace('.', ',')}`;
                document.getElementById('estimated_amount').value = grandTotal.toFixed(2);
            }

            function updateRemoveButtons() {
                const items = document.querySelectorAll('.item-row');
                items.forEach((item, index) => {
                    const removeBtn = item.querySelector('.remove-item');
                    if (items.length > 1) {
                        removeBtn.style.display = 'inline-block';
                    } else {
                        removeBtn.style.display = 'none';
                    }
                });
            }

            function toggleDebtSection() {
                if (createDebtCheck.checked) {
                    debtDueDateSection.style.display = 'block';
                } else {
                    debtDueDateSection.style.display = 'none';
                }
            }

            // Função para salvar rascunho (implementar conforme necessário)
            window.saveDraft = function() {
                showToast('Funcionalidade de rascunho em desenvolvimento', 'info');
            };
        });
    </script>
@endsection
@endsection
