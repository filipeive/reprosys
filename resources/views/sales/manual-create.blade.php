@extends('layouts.app')

@section('title', 'Registrar Venda Manual')
@section('title-icon', 'fa-history')
@section('page-title', 'Registrar Venda Manual')

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('sales.index') }}">Vendas</a>
    </li>
    <li class="breadcrumb-item active">Venda Manual</li>
@endsection

@section('content')
    <!-- Header Info -->
    <div class="alert alert-info border-0 rounded-lg mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle fa-lg me-3"></i>
            <div>
                <strong>Venda Manual</strong>
                <p class="mb-0 small">
                    Use esta tela para lançar vendas antigas do livro físico, informando a data/hora real da venda.
                    Os preços podem ser editados conforme necessário.
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('sales.store') }}" method="POST" id="manual-sale-form">
        @csrf
        
        <!-- Card 1: Informações da Venda -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Informações da Venda
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="sale_date" class="form-label">
                            <i class="fas fa-clock me-2 text-primary"></i>Data e Hora da Venda *
                        </label>
                        <input type="datetime-local" class="form-control form-control-lg" 
                               name="sale_date" id="sale_date" required
                               value="{{ old('sale_date', now()->format('Y-m-d\TH:i')) }}">
                        <div class="form-text">
                            <i class="fas fa-lightbulb me-1"></i>
                            Informe a data/hora real quando a venda foi realizada
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="fas fa-user me-2 text-success"></i>Vendedor Responsável
                        </label>
                        <input type="text" class="form-control form-control-lg bg-light" 
                               value="{{ Auth::user()->name }}" readonly>
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                        <div class="form-text">
                            <i class="fas fa-shield-alt me-1"></i>
                            Vendedor autenticado no sistema
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Dados do Cliente -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users me-2"></i>
                    Dados do Cliente
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="customer_name" class="form-label">Nome do Cliente</label>
                        <input type="text" class="form-control" name="customer_name" 
                               id="customer_name" value="{{ old('customer_name', 'Cliente Avulso') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="customer_phone" class="form-label">Telefone de Contato</label>
                        <input type="text" class="form-control" name="customer_phone" 
                               id="customer_phone" placeholder="(00) 00000-0000"
                               value="{{ old('customer_phone') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Método de Pagamento -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-credit-card me-2"></i>
                    Método de Pagamento
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="payment_method" class="form-label fw-bold">
                            Forma de Pagamento *
                        </label>
                        <select class="form-select form-select-lg" name="payment_method" id="payment_method" required>
                            <option value="">Selecione uma opção...</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>💵 Dinheiro</option>
                            <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>💳 Cartão</option>
                            <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>🏦 Transferência</option>
                            <option value="credit" {{ old('payment_method') == 'credit' ? 'selected' : '' }}>🤝 Crédito</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="notes" class="form-label fw-bold">Observações</label>
                        <textarea class="form-control" name="notes" id="notes" rows="3" 
                                  placeholder="Ex: Venda referente ao livro físico, desconto aplicado, etc...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Produtos/Serviços com Preços Editáveis -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Produtos/Serviços
                </h5>
                <div class="total-display">
                    <span class="badge bg-light text-dark fs-6 px-3 py-2">
                        Total: MZN <span id="total-amount">0,00</span>
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Filtro de produtos -->
                <div class="bg-light p-3 border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="product-filter" 
                                       placeholder="Filtrar produtos por nome...">
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-all-rows">
                                <i class="fas fa-eraser me-1"></i> Limpar Tudo
                            </button>
                            <button type="button" class="btn btn-success btn-sm" id="add-selected">
                                <i class="fas fa-plus me-1"></i> Adicionar Selecionados
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="products-table">
                        <thead class="table-success">
                            <tr>
                                <th class="text-center" width="80">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="select-all">
                                        <label class="form-check-label" for="select-all"></label>
                                    </div>
                                </th>
                                <th>
                                    <i class="fas fa-box me-2"></i>Produto
                                </th>
                                <th class="text-center" width="130">
                                    <i class="fas fa-tag me-2"></i>Preço Unit.
                                </th>
                                <th class="text-center" width="120">
                                    <i class="fas fa-sort-numeric-up me-2"></i>Qtd.
                                </th>
                                <th class="text-center" width="130">
                                    <i class="fas fa-calculator me-2"></i>Subtotal
                                </th>
                                <th class="text-center" width="80">
                                    <i class="fas fa-tools"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr class="product-row" data-product-id="{{ $product->id }}" data-product-name="{{ strtolower($product->name) }}">
                                    <td class="text-center">
                                        <div class="form-check">
                                            <input type="checkbox" value="1" 
                                                   class="form-check-input select-product" id="product_{{ $product->id }}">
                                            <label class="form-check-label" for="product_{{ $product->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="product-info">
                                            <span class="fw-bold text-dark">{{ $product->name }}</span>
                                            <br>
                                            <small class="text-muted">
                                                @if($product->type === 'product')
                                                    <i class="fas fa-cubes me-1"></i>Stock: {{ $product->stock_quantity }}
                                                @else
                                                    <i class="fas fa-concierge-bell me-1"></i>Serviço
                                                @endif
                                                | Cód: {{ $product->id }}
                                            </small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">MZN</span>
                                            <input type="number" step="0.01" min="0" 
                                                   value="{{ $product->selling_price }}" 
                                                   class="form-control text-end unit-price"
                                                   data-original-price="{{ $product->selling_price }}">
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            Original: {{ number_format($product->selling_price, 2, ',', '.') }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" min="0" value="0"
                                               class="form-control form-control-sm text-center quantity" 
                                               placeholder="0"
                                               @if($product->type === 'product')
                                                   max="{{ $product->stock_quantity }}"
                                               @endif>
                                    </td>
                                    <td class="text-center">
                                        <div class="subtotal-display">
                                            <span class="fw-bold text-success subtotal" data-subtotal="0">
                                                MZN 0,00
                                            </span>
                                            <div class="discount-info small text-muted mt-1" style="display: none;">
                                                <span class="discount-amount"></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-success quick-add" 
                                                    title="Adicionar 1 unidade" data-product-id="{{ $product->id }}">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger clear-row" 
                                                    title="Limpar linha">
                                                <i class="fas fa-eraser"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold fs-5">
                                    <i class="fas fa-calculator me-2 text-success"></i>
                                    TOTAL GERAL:
                                </td>
                                <td class="text-center fw-bold fs-5 text-success">
                                    MZN <span id="footer-total">0,00</span>
                                </td>
                                <td></td>
                            </tr>
                            <tr id="discount-summary" style="display: none;">
                                <td colspan="4" class="text-end text-muted">
                                    <i class="fas fa-percentage me-2"></i>
                                    Total de Descontos:
                                </td>
                                <td class="text-center text-danger">
                                    MZN <span id="total-discount">0,00</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="p-3 bg-light border-top">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <small class="text-muted">
                                <i class="fas fa-lightbulb me-1 text-warning"></i>
                                <strong>Dica:</strong> Os preços podem ser editados para aplicar descontos ou valores especiais.
                                Selecione os produtos e informe as quantidades desejadas.
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="apply-discount">
                                <i class="fas fa-percentage me-1"></i> Aplicar Desconto Geral
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">
                            <i class="fas fa-shield-check me-1"></i>
                            Todos os dados serão salvos com segurança
                        </small>
                    </div>
                    <div class="btn-group" role="group">
                        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-success btn-lg px-4" id="submit-btn">
                            <i class="fas fa-save me-2"></i>
                            Registrar Venda Manual
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal de Desconto Geral -->
    <div class="modal fade" id="discountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-percentage me-2"></i>Aplicar Desconto Geral
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="discount-type" class="form-label">Tipo de Desconto</label>
                        <select class="form-select" id="discount-type">
                            <option value="percentage">Percentual (%)</option>
                            <option value="fixed">Valor Fixo (MZN)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="discount-value" class="form-label">Valor do Desconto</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="discount-value" placeholder="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Aplicar aos produtos:</label>
                        <div class="form-check">
                            <input type="radio" name="discount-apply" value="selected" class="form-check-input" id="discount-selected" checked>
                            <label class="form-check-label" for="discount-selected">Apenas produtos selecionados</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="discount-apply" value="all" class="form-check-input" id="discount-all">
                            <label class="form-check-label" for="discount-all">Todos os produtos com quantidade</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="apply-discount-btn">Aplicar Desconto</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .product-row {
        transition: all 0.2s ease;
    }

    .product-row:hover {
        background: #f8f9fa;
    }

    .product-row.selected {
        background: #e3f2fd;
        border-left: 3px solid #2196f3;
    }

    .product-row.hidden {
        display: none;
    }

    .unit-price {
        font-weight: bold;
    }

    .unit-price.modified {
        background: #fff3cd;
        border-color: #ffc107;
    }

    .subtotal-display {
        min-height: 50px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .discount-info {
        font-style: italic;
    }

    .quick-add:hover {
        background: #28a745;
        color: white;
    }

    .clear-row:hover {
        background: #dc3545;
        color: white;
    }

    .btn-group .btn {
        border-radius: 0;
    }

    .btn-group .btn:first-child {
        border-radius: 0.375rem 0 0 0.375rem;
    }

    .btn-group .btn:last-child {
        border-radius: 0 0.375rem 0.375rem 0;
    }

    .total-display .badge {
        font-size: 1rem;
        border-radius: 2rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let totalDiscount = 0;

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

    // Filtro de produtos
    document.getElementById('product-filter').addEventListener('input', function() {
        const search = this.value.toLowerCase();
        document.querySelectorAll('.product-row').forEach(function(row) {
            const productName = row.getAttribute('data-product-name');
            if (productName.includes(search) || search === '') {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    });

    // Selecionar todos os produtos
    document.getElementById('select-all').addEventListener('change', function() {
        const isChecked = this.checked;
        document.querySelectorAll('.select-product:not(.product-row.hidden .select-product)').forEach(function(checkbox) {
            checkbox.checked = isChecked;
            toggleRowSelection(checkbox.closest('.product-row'), isChecked);
        });
    });

    // Função para alternar seleção de linha
    function toggleRowSelection(row, isSelected) {
        if (isSelected) {
            row.classList.add('selected');
            const quantityInput = row.querySelector('.quantity');
            if (quantityInput.value == '0' || quantityInput.value === '') {
                quantityInput.value = '1';
                calculateSubtotal(row);
            }
        } else {
            row.classList.remove('selected');
        }
    }

    // Event listeners para seleção individual
    document.querySelectorAll('.select-product').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const row = this.closest('.product-row');
            toggleRowSelection(row, this.checked);
        });
    });

    // Função para calcular subtotal
    function calculateSubtotal(row) {
        const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
        const originalPrice = parseFloat(row.querySelector('.unit-price').getAttribute('data-original-price')) || 0;
        const quantity = parseInt(row.querySelector('.quantity').value) || 0;
        const subtotal = unitPrice * quantity;
        
        const subtotalElement = row.querySelector('.subtotal');
        const discountInfo = row.querySelector('.discount-info');
        
        subtotalElement.textContent = 'MZN ' + subtotal.toFixed(2).replace('.', ',');
        subtotalElement.setAttribute('data-subtotal', subtotal);
        
        // Mostrar informação de desconto se houver diferença no preço
        const discount = (originalPrice - unitPrice) * quantity;
        if (discount > 0 && quantity > 0) {
            discountInfo.style.display = 'block';
            discountInfo.querySelector('.discount-amount').textContent = 
                'Desconto: MZN ' + discount.toFixed(2).replace('.', ',');
        } else {
            discountInfo.style.display = 'none';
        }
        
        // Marcar campo de preço como modificado
        const priceInput = row.querySelector('.unit-price');
        if (unitPrice !== originalPrice) {
            priceInput.classList.add('modified');
        } else {
            priceInput.classList.remove('modified');
        }
        
        calculateTotal();
    }

    // Função para calcular total geral
    function calculateTotal() {
        let total = 0;
        let totalDiscountAmount = 0;
        
        document.querySelectorAll('.subtotal').forEach(function(element) {
            total += parseFloat(element.getAttribute('data-subtotal')) || 0;
        });

        // Calcular desconto total
        document.querySelectorAll('.product-row').forEach(function(row) {
            const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
            const originalPrice = parseFloat(row.querySelector('.unit-price').getAttribute('data-original-price')) || 0;
            const quantity = parseInt(row.querySelector('.quantity').value) || 0;
            const discount = (originalPrice - unitPrice) * quantity;
            if (discount > 0) {
                totalDiscountAmount += discount;
            }
        });
        
        const formattedTotal = total.toFixed(2).replace('.', ',');
        document.getElementById('total-amount').textContent = formattedTotal;
        document.getElementById('footer-total').textContent = formattedTotal;
        
        // Mostrar resumo de desconto se houver
        const discountSummary = document.getElementById('discount-summary');
        if (totalDiscountAmount > 0) {
            discountSummary.style.display = 'table-row';
            document.getElementById('total-discount').textContent = totalDiscountAmount.toFixed(2).replace('.', ',');
        } else {
            discountSummary.style.display = 'none';
        }
    }

    // Event listeners para cálculos automáticos
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('unit-price') || e.target.classList.contains('quantity')) {
            const row = e.target.closest('.product-row');
            calculateSubtotal(row);
        }
    });

    // Botões de ação rápida
    document.querySelectorAll('.quick-add').forEach(function(button) {
        button.addEventListener('click', function() {
            const row = this.closest('.product-row');
            const checkbox = row.querySelector('.select-product');
            const quantityInput = row.querySelector('.quantity');
            
            checkbox.checked = true;
            toggleRowSelection(row, true);
            quantityInput.value = parseInt(quantityInput.value) + 1;
            calculateSubtotal(row);
        });
    });

    // Limpar linha
    document.querySelectorAll('.clear-row').forEach(function(button) {
        button.addEventListener('click', function() {
            const row = this.closest('.product-row');
            const checkbox = row.querySelector('.select-product');
            const quantityInput = row.querySelector('.quantity');
            const priceInput = row.querySelector('.unit-price');
            
            checkbox.checked = false;
            quantityInput.value = '0';
            priceInput.value = priceInput.getAttribute('data-original-price');
            priceInput.classList.remove('modified');
            
            toggleRowSelection(row, false);
            calculateSubtotal(row);
        });
    });

    // Limpar tudo
    document.getElementById('clear-all-rows').addEventListener('click', function() {
        if (confirm('Tem certeza que deseja limpar todos os produtos?')) {
            document.querySelectorAll('.clear-row').forEach(btn => btn.click());
        }
    });

    // Adicionar produtos selecionados
    document.getElementById('add-selected').addEventListener('click', function() {
        let addedCount = 0;
        document.querySelectorAll('.select-product:checked').forEach(function(checkbox) {
            const row = checkbox.closest('.product-row');
            const quantityInput = row.querySelector('.quantity');
            if (quantityInput.value === '0' || quantityInput.value === '') {
                quantityInput.value = '1';
                calculateSubtotal(row);
                addedCount++;
            }
        });
        
        if (addedCount > 0) {
            if (typeof window.showToast === 'function') {
                window.showToast(`${addedCount} produtos adicionados com sucesso!`, 'success');
            }
        }
    });

    // Modal de desconto geral
    document.getElementById('apply-discount').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('discountModal'));
        modal.show();
    });

    // Aplicar desconto geral
    document.getElementById('apply-discount-btn').addEventListener('click', function() {
        const discountType = document.getElementById('discount-type').value;
        const discountValue = parseFloat(document.getElementById('discount-value').value) || 0;
        const applyTo = document.querySelector('input[name="discount-apply"]:checked').value;
        
        if (discountValue <= 0) {
            alert('Por favor, informe um valor de desconto válido.');
            return;
        }

        let rowsToApply;
        if (applyTo === 'selected') {
            rowsToApply = document.querySelectorAll('.product-row.selected');
        } else {
            rowsToApply = document.querySelectorAll('.product-row');
        }

        rowsToApply.forEach(function(row) {
            const quantityInput = row.querySelector('.quantity');
            if (parseInt(quantityInput.value) > 0) {
                const priceInput = row.querySelector('.unit-price');
                const currentPrice = parseFloat(priceInput.value);
                let newPrice;

                if (discountType === 'percentage') {
                    newPrice = currentPrice * (1 - discountValue / 100);
                } else {
                    newPrice = currentPrice - discountValue;
                }

                newPrice = Math.max(0, newPrice); // Não permitir preços negativos
                priceInput.value = newPrice.toFixed(2);
                calculateSubtotal(row);
            }
        });

        bootstrap.Modal.getInstance(document.getElementById('discountModal')).hide();
        
        if (typeof window.showToast === 'function') {
            window.showToast('Desconto aplicado com sucesso!', 'success');
        }
    });

    // Validação e submissão do formulário
    document.getElementById('manual-sale-form').addEventListener('submit', function(e) {
        let items = [];
        let hasError = false;
        
        document.querySelectorAll('.product-row').forEach(function(row) {
            const productId = row.getAttribute('data-product-id');
            const isSelected = row.querySelector('.select-product').checked;
            const unitPrice = parseFloat(row.querySelector('.unit-price').value);
            const quantity = parseInt(row.querySelector('.quantity').value);

            if ((isSelected || quantity > 0) && quantity > 0 && unitPrice >= 0) {
                items.push({
                    product_id: parseInt(productId),
                    unit_price: unitPrice,
                    quantity: quantity
                });
            }
        });

        // Validações
        if (items.length === 0) {
            alert('Selecione pelo menos um produto com quantidade maior que zero.');
            e.preventDefault();
            return false;
        }

        if (!document.getElementById('sale_date').value) {
            alert('Por favor, informe a data e hora da venda.');
            document.getElementById('sale_date').focus();
            e.preventDefault();
            return false;
        }

        if (!document.getElementById('payment_method').value) {
            alert('Por favor, selecione o método de pagamento.');
            document.getElementById('payment_method').focus();
            e.preventDefault();
            return false;
        }

        // Adicionar dados dos itens ao formulário
        const existingInput = document.getElementById('items-json');
        if (existingInput) {
            existingInput.remove();
        }

        const input = document.createElement('input');
        input.type = 'hidden';
        input.id = 'items-json';
        input.name = 'items';
        input.value = JSON.stringify(items);
        this.appendChild(input);

        // Mostrar loading
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processando...';
    });

    // Inicializar
    calculateTotal();
});
</script>
@endpush