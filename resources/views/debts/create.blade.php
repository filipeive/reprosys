@extends('layouts.app')

@section('title', 'Nova Dívida')
@section('page-title', 'Nova Dívida')

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">
                <i class="fas fa-plus-circle me-2 text-primary"></i>
                Nova Dívida de {{ $type === 'product' ? 'Produtos' : 'Dinheiro' }}
            </h3>
            <p class="text-muted mb-0">Preencha os dados abaixo</p>
        </div>
        <a href="{{ route('debts.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Voltar
        </a>
    </div>

    <!-- Formulário -->
    <form action="{{ route('debts.store') }}" method="POST" id="debt-form">
        @csrf
        <input type="hidden" name="debt_type" value="{{ $type }}">

        <div class="row">
            <div class="col-lg-8">

                <!-- Informações do Devedor -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            {{ $type === 'product' ? 'Informações do Cliente' : 'Informações do Funcionário' }}
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($type === 'product')
                            <!-- Dívida de Produtos -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nome do Cliente *</label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                        name="customer_name" value="{{ old('customer_name') }}" required>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Telefone</label>
                                    <input type="text" class="form-control @error('customer_phone') is-invalid @enderror"
                                        name="customer_phone" value="{{ old('customer_phone') }}">
                                    @error('customer_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Documento</label>
                                    <input type="text" class="form-control" name="customer_document"
                                        value="{{ old('customer_document') }}">
                                </div>
                            </div>
                        @else
                            <!-- Dívida de Dinheiro -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Selecionar Funcionário *</label>
                                    <select class="form-select @error('employee_id') is-invalid @enderror"
                                        name="employee_id" id="employee-select" required>
                                        <option value="">Escolha um funcionário...</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" data-name="{{ $employee->name }}"
                                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }} - {{ $employee->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nome Completo *</label>
                                    <input type="text" class="form-control @error('employee_name') is-invalid @enderror"
                                        name="employee_name" id="employee-name" value="{{ old('employee_name') }}"
                                        required>
                                    @error('employee_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Telefone</label>
                                    <input type="text" class="form-control" name="employee_phone"
                                        value="{{ old('employee_phone') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Documento</label>
                                    <input type="text" class="form-control" name="employee_document"
                                        value="{{ old('employee_document') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Valor da Dívida *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">MT</span>
                                        <input type="number" step="0.01" min="0.01"
                                            class="form-control @error('amount') is-invalid @enderror" name="amount"
                                            value="{{ old('amount') }}" required>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($type === 'product')
                    <!-- Produtos -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Produtos da Dívida
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <select class="form-select" id="product-select">
                                        <option value="">Selecione um produto...</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                                data-price="{{ $product->selling_price }}"
                                                data-stock="{{ $product->stock_quantity ?? 999 }}">
                                                {{ $product->name }} - MT
                                                {{ number_format($product->selling_price, 2, ',', '.') }}
                                                @if ($product->stock_quantity)
                                                    (Estoque: {{ $product->stock_quantity }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-primary w-100" onclick="addProduct()">
                                        <i class="fas fa-plus me-2"></i>Adicionar
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm" id="products-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Produto</th>
                                            <th width="120">Quantidade</th>
                                            <th width="120" class="text-end">Preço Unit.</th>
                                            <th width="120" class="text-end">Total</th>
                                            <th width="80" class="text-center">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products-tbody">
                                        <!-- Produtos adicionados aqui -->
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <td colspan="3" class="text-end fw-bold">Total Geral:</td>
                                            <td class="text-end fw-bold" id="products-total">MT 0,00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <input type="hidden" name="products" id="products-json">
                            @error('products')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @endif

                <!-- Detalhes da Dívida -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Detalhes da Dívida
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Data da Dívida *</label>
                                <input type="date" class="form-control @error('debt_date') is-invalid @enderror"
                                    name="debt_date" value="{{ old('debt_date', date('Y-m-d')) }}" required>
                                @error('debt_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Data de Vencimento</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                    name="due_date" value="{{ old('due_date') }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Descrição *</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Observações</label>
                                <textarea class="form-control" name="notes" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">

                <!-- Resumo -->
                <div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-calculator me-2"></i>
                            Resumo
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small text-muted">Tipo de Dívida</label>
                            <div class="fw-bold">
                                {{ $type === 'product' ? '📦 Produtos/Serviços' : '💵 Dinheiro' }}
                            </div>
                        </div>

                        @if ($type === 'product')
                            <div class="mb-3">
                                <label class="form-label small text-muted">Total de Itens</label>
                                <div class="fw-bold" id="summary-items">0 produtos</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted">Valor Total</label>
                                <div class="h4 mb-0 text-success" id="summary-total">MT 0,00</div>
                            </div>
                        @else
                            <div class="mb-3">
                                <label class="form-label small text-muted">Valor a Receber</label>
                                <div class="h4 mb-0 text-success">-</div>
                            </div>
                        @endif

                        <hr>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pagamento Inicial (Opcional)</label>
                            <div class="input-group">
                                <span class="input-group-text">MT</span>
                                <input type="number" step="0.01" min="0" class="form-control"
                                    name="initial_payment" value="{{ old('initial_payment') }}">
                            </div>
                            <small class="text-muted">Deixe vazio se não houver entrada</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success w-100 btn-lg">
                            <i class="fas fa-save me-2"></i>
                            Criar Dívida
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        let productsCart = [];

        // Auto-preencher nome do funcionário
        document.getElementById('employee-select')?.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            document.getElementById('employee-name').value = selected.dataset.name || '';
        });

        // Adicionar produto ao carrinho
        function addProduct() {
            const select = document.getElementById('product-select');
            const option = select.options[select.selectedIndex];

            if (!option.value) {
                alert('Selecione um produto');
                return;
            }

            const product = {
                product_id: parseInt(option.value),
                name: option.dataset.name,
                unit_price: parseFloat(option.dataset.price),
                stock: parseInt(option.dataset.stock),
                quantity: 1
            };

            // Verificar se já existe
            const existing = productsCart.find(p => p.product_id === product.product_id);
            if (existing) {
                if (existing.quantity < product.stock) {
                    existing.quantity++;
                } else {
                    alert('Estoque máximo atingido');
                    return;
                }
            } else {
                productsCart.push(product);
            }

            updateCart();
            select.value = '';
        }

        // Remover produto
        function removeProduct(index) {
            productsCart.splice(index, 1);
            updateCart();
        }

        // Atualizar quantidade
        function updateQuantity(index, value) {
            const qty = parseInt(value);
            if (qty > 0 && qty <= productsCart[index].stock) {
                productsCart[index].quantity = qty;
                updateCart();
            }
        }

        // Atualizar carrinho
        function updateCart() {
            const tbody = document.getElementById('products-tbody');
            let total = 0;

            tbody.innerHTML = '';
            productsCart.forEach((item, index) => {
                const subtotal = item.quantity * item.unit_price;
                total += subtotal;

                tbody.innerHTML += `
            <tr>
                <td>${item.name}</td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           value="${item.quantity}" min="1" max="${item.stock}"
                           onchange="updateQuantity(${index}, this.value)">
                </td>
                <td class="text-end">MT ${item.unit_price.toFixed(2)}</td>
                <td class="text-end fw-bold">MT ${subtotal.toFixed(2)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeProduct(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
            });

            document.getElementById('products-total').textContent = `MT ${total.toFixed(2)}`;
            document.getElementById('summary-items').textContent = `${productsCart.length} produtos`;
            document.getElementById('summary-total').textContent = `MT ${total.toFixed(2)}`;
            document.getElementById('products-json').value = JSON.stringify(productsCart);
        }

        // Validar antes de enviar
        document.getElementById('debt-form').addEventListener('submit', function(e) {
            @if ($type === 'product')
                if (productsCart.length === 0) {
                    e.preventDefault();
                    alert('Adicione pelo menos um produto');
                    return false;
                }
            @endif
        });
    </script>
@endpush
