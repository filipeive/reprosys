@extends('layouts.app')

@section('title', "Dívida #{$debt->id}")
@section('page-title', "Dívida #{$debt->id}")
@section('title-icon', 'fa-credit-card')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('debts.index') }}">Dívidas</a></li>
    <li class="breadcrumb-item active">Dívida #{{ $debt->id }}</li>
@endsection

@section('content')
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
                <input type="hidden" name="debt_id" id="payment-debt-id" value="{{ $debt->id }}">

                <div class="alert alert-info">
                    <div><strong>Cliente:</strong> <span id="payment-customer-name">{{ $debt->customer_name }}</span></div>
                    <div><strong>Valor Restante:</strong> <span id="payment-remaining-amount">MT
                            {{ number_format($debt->remaining_amount, 2, ',', '.') }}</span></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Valor do Pagamento *</label>
                    <div class="input-group">
                        <span class="input-group-text">MT</span>
                        <input type="number" step="0.01" name="amount" id="payment-amount" class="form-control"
                            max="{{ $debt->remaining_amount }}" required>
                    </div>
                    <small class="text-muted">Máximo: <strong id="max-amount-text">MT
                            {{ number_format($debt->remaining_amount, 2, ',', '.') }}</strong></small>
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
                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Observações</label>
                    <textarea name="notes" class="form-control" rows="3" maxlength="500"
                        placeholder="Observações sobre o pagamento (opcional)"></textarea>
                </div>
            </form>
        </div>
        <div class="offcanvas-footer p-3 border-top">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" form="payment-form" class="btn btn-success flex-fill">
                    <i class="fas fa-check me-2"></i> Registrar Pagamento
                </button>
            </div>
        </div>
    </div>
    <!-- Offcanvas para Criar/Editar Dívida -->
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
                                <label class="form-label fw-semibold">Nome do Cliente *</label>
                                <input type="text" class="form-control" name="customer_name" id="customer-name"
                                    required>
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
                                <label class="form-label fw-semibold">Documento</label>
                                <input type="text" class="form-control" name="customer_document"
                                    id="customer-document">
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
                                <label class="form-label fw-semibold">Data de Vencimento</label>
                                <input type="date" class="form-control" name="due_date" id="due-date">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seleção de Produtos -->
                <div class="p-4 border-bottom">
                    <h6 class="mb-3"><i class="fas fa-shopping-cart me-2"></i> Produtos da Dívida</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <select class="form-select" id="product-select">
                                <option value="">Selecione um produto ou serviço...</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                        data-type="{{ $product->type }}" data-unit="{{ $product->unit ?? 'unid' }}"
                                        data-price="{{ $product->selling_price }}"
                                        data-stock="{{ $product->stock_quantity ?? 0 }}">
                                        {{ $product->name }} - MT
                                        {{ number_format($product->selling_price, 2, ',', '.') }}
                                        @if ($product->type === 'product' && $product->stock_quantity)
                                            (Estoque: {{ $product->stock_quantity }} {{ $product->unit ?? 'unid' }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary w-100" onclick="addProductToCart()">
                                <i class="fas fa-plus me-2"></i>Adicionar
                            </button>
                        </div>
                    </div>

                    <!-- Carrinho de Produtos -->
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" id="products-cart-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto/Serviço</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Preço Unit.</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="products-cart-items">
                                <!-- Produtos serão adicionados aqui via JS -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total Geral:</td>
                                    <td class="text-end fw-bold" id="products-cart-total">MT 0,00</td>
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
                                <label class="form-label fw-semibold">Descrição da Dívida *</label>
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Pagamento Inicial (Opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">MT</span>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        name="initial_payment" id="initial-payment">
                                </div>
                                <small class="text-muted">Deixe em branco se não houver pagamento inicial</small>
                            </div>
                        </div>
                    </div>

                    <!-- Campo hidden para os produtos (JSON) -->
                    <input type="hidden" name="products" id="products-json">
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

    <div class="row">
        <!-- Informações da Dívida -->
        <div class="col-lg-8">
            <!-- Card Principal da Dívida -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>
                            Dívida #{{ $debt->id }}
                        </h5>
                        @php
                            $statusBadge = match ($debt->status) {
                                'active' => 'bg-warning',
                                'paid' => 'bg-success',
                                'cancelled' => 'bg-secondary',
                                'overdue' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                            $statusText = match ($debt->status) {
                                'active' => 'Ativa',
                                'paid' => 'Paga',
                                'cancelled' => 'Cancelada',
                                'overdue' => 'Vencida',
                                default => ucfirst($debt->status),
                            };
                        @endphp
                        <span class="badge {{ $statusBadge }} fs-6">{{ $statusText }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Cliente:</strong> {{ $debt->customer_name }}</p>
                            <p class="mb-2"><strong>Telefone:</strong> {{ $debt->customer_phone ?? 'N/A' }}</p>
                            @if ($debt->customer_document)
                                <p class="mb-2"><strong>Documento:</strong> {{ $debt->customer_document }}</p>
                            @endif
                            <p class="mb-2"><strong>Data da Dívida:</strong> {{ $debt->debt_date->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Vencimento:</strong>
                                @if ($debt->due_date)
                                    @php
                                        $isOverdue =
                                            $debt->status === 'overdue' ||
                                            ($debt->status === 'active' && $debt->due_date->isPast());
                                    @endphp
                                    <span class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                        {{ $debt->due_date->format('d/m/Y') }}
                                    </span>
                                    @if ($isOverdue && $debt->status === 'active')
                                        <br><small class="text-danger">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Atraso de {{ $debt->due_date->diffInDays(now()) }} dias
                                        </small>
                                    @endif
                                @else
                                    <span class="text-muted">Sem data definida</span>
                                @endif
                            </p>
                            <p class="mb-2"><strong>Criado por:</strong> {{ $debt->user->name }}</p>
                            <p class="mb-2"><strong>Criado em:</strong> {{ $debt->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <p class="mb-2"><strong>Descrição:</strong></p>
                            <p class="text-muted">{{ $debt->description }}</p>

                            @if ($debt->notes)
                                <p class="mb-2 mt-3"><strong>Observações:</strong></p>
                                <p class="text-muted">{{ $debt->notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Itens da Dívida -->
            @if ($debt->items->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Itens da Dívida ({{ $debt->items->count() }}
                            {{ $debt->items->count() === 1 ? 'item' : 'itens' }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produto/Serviço</th>
                                        <th>Categoria</th>
                                        <th class="text-center">Quantidade</th>
                                        <th class="text-end">Preço Unitário</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($debt->items as $item)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $item->product->name }}</div>
                                                <small
                                                    class="text-muted">{{ $item->product->type === 'product' ? 'Produto' : 'Serviço' }}</small>
                                            </td>
                                            <td>
                                                {{ $item->product->category->name ?? 'Sem categoria' }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $item->quantity }}
                                                    {{ $item->product->unit ?? 'unid' }}</span>
                                            </td>
                                            <td class="text-end">MT {{ number_format($item->unit_price, 2, ',', '.') }}
                                            </td>
                                            <td class="text-end fw-semibold">MT
                                                {{ number_format($item->total_price, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-end">Total da Dívida:</th>
                                        <th class="text-end text-primary">MT
                                            {{ number_format($debt->original_amount, 2, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Histórico de Pagamentos -->
            @if ($debt->payments->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-money-bill me-2"></i>
                            Histórico de Pagamentos ({{ $debt->payments->count() }}
                            {{ $debt->payments->count() === 1 ? 'pagamento' : 'pagamentos' }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Data</th>
                                        <th class="text-end">Valor</th>
                                        <th>Forma de Pagamento</th>
                                        <th>Usuário</th>
                                        <th>Observações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($debt->payments->sortByDesc('payment_date') as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                            <td class="text-end text-success fw-bold">MT
                                                {{ number_format($payment->amount, 2, ',', '.') }}</td>
                                            <td>
                                                @php
                                                    $methodBadge = match ($payment->payment_method) {
                                                        'cash' => 'bg-success',
                                                        'card' => 'bg-primary',
                                                        'transfer' => 'bg-info',
                                                        'mpesa' => 'bg-warning text-dark',
                                                        'emola' => 'bg-danger',
                                                        default => 'bg-secondary',
                                                    };
                                                    $methodText = match ($payment->payment_method) {
                                                        'cash' => 'Dinheiro',
                                                        'card' => 'Cartão',
                                                        'transfer' => 'Transferência',
                                                        'mpesa' => 'M-Pesa',
                                                        'emola' => 'E-mola',
                                                        default => ucfirst($payment->payment_method),
                                                    };
                                                @endphp
                                                <span class="badge {{ $methodBadge }}">{{ $methodText }}</span>
                                            </td>
                                            <td>{{ $payment->user->name }}</td>
                                            <td>
                                                @if ($payment->notes)
                                                    <small
                                                        class="text-muted">{{ Str::limit($payment->notes, 50) }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>Total Pago:</th>
                                        <th class="text-end text-success">MT
                                            {{ number_format($debt->original_amount - $debt->remaining_amount, 2, ',', '.') }}
                                        </th>
                                        <th colspan="3"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar de Ações e Resumo -->
        <div class="col-lg-4">
            <!-- Resumo Financeiro -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        Resumo Financeiro
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <div class="p-3 bg-primary bg-opacity-10 rounded">
                                <small class="text-muted d-block">Valor Original</small>
                                <h4 class="mb-0 text-primary fw-bold">MT
                                    {{ number_format($debt->original_amount, 2, ',', '.') }}</h4>
                            </div>
                        </div>

                        @php $paidAmount = $debt->original_amount - $debt->remaining_amount; @endphp
                        @if ($paidAmount > 0)
                            <div class="col-12 mb-3">
                                <div class="p-3 bg-success bg-opacity-10 rounded">
                                    <small class="text-muted d-block">Total Pago</small>
                                    <h5 class="mb-0 text-success fw-bold">MT {{ number_format($paidAmount, 2, ',', '.') }}
                                    </h5>
                                </div>
                            </div>
                        @endif

                        @if ($debt->remaining_amount > 0)
                            <div class="col-12 mb-3">
                                <div class="p-3 bg-warning bg-opacity-10 rounded">
                                    <small class="text-muted d-block">Valor Restante</small>
                                    <h5 class="mb-0 text-warning fw-bold">MT
                                        {{ number_format($debt->remaining_amount, 2, ',', '.') }}</h5>

                                    @if ($debt->due_date && $debt->status === 'active')
                                        @php $daysRemaining = now()->diffInDays($debt->due_date, false); @endphp
                                        @if ($daysRemaining < 0)
                                            <small class="text-danger d-block mt-1">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                {{ abs($daysRemaining) }} dias em atraso
                                            </small>
                                        @elseif($daysRemaining <= 7)
                                            <small class="text-warning d-block mt-1">
                                                <i class="fas fa-clock"></i>
                                                Vence em {{ $daysRemaining }} {{ $daysRemaining === 1 ? 'dia' : 'dias' }}
                                            </small>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($debt->status === 'paid')
                            <div class="col-12">
                                <div class="p-3 bg-success bg-opacity-20 rounded border border-success">
                                    <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                    <h6 class="text-success mb-0">Dívida Quitada!</h6>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ações Disponíveis -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Ações Disponíveis
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if ($debt->status === 'active' && $debt->remaining_amount > 0)
                            <button type="button" class="btn btn-success" onclick="openPaymentOffcanvas()">
                                <i class="fas fa-money-bill me-2"></i>
                                Registrar Pagamento
                            </button>

                            <button type="button" class="btn btn-outline-success" onclick="markAsPaid()">
                                <i class="fas fa-check-circle me-2"></i>
                                Marcar como Paga
                            </button>

                            <hr class="my-2">

                            <button type="button" class="btn btn-outline-danger" onclick="confirmCancelDebt()">
                                <i class="fas fa-ban me-2"></i>
                                Cancelar Dívida
                            </button>
                        @endif

                        @if ($debt->status !== 'paid' && $debt->status !== 'cancelled')
                            <button type="button" class="btn btn-outline-warning"
                                onclick="openEditDebtOffcanvas({{ $debt->id }})" title="Editar Dívida">
                                <i class="fas fa-edit"></i>
                                Editar Dívida
                            </button>
                        @endif

                        <a href="{{ route('debts.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Voltar à Lista
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações Adicionais
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <small class="text-muted">Criado em:</small>
                            <div class="fw-semibold">{{ $debt->created_at->format('d/m/Y H:i') }}</div>
                        </div>

                        @if ($debt->updated_at != $debt->created_at)
                            <div class="col-12 mt-2">
                                <small class="text-muted">Última atualização:</small>
                                <div class="fw-semibold">{{ $debt->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        @endif

                        <div class="col-12 mt-2">
                            <small class="text-muted">Criado por:</small>
                            <div class="fw-semibold">{{ $debt->user->name }}</div>
                        </div>

                        @if ($debt->payments->count() > 0)
                            <div class="col-12 mt-2">
                                <small class="text-muted">Último pagamento:</small>
                                <div class="fw-semibold">
                                    {{ $debt->payments->sortByDesc('payment_date')->first()->payment_date->format('d/m/Y') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let productsCart = [];

        // Função para adicionar produto ao carrinho
        function addProductToCart() {
            const select = document.getElementById('product-select');
            const productId = select.value;

            if (!productId) {
                showToast('Selecione um produto ou serviço', 'warning');
                return;
            }

            const option = select.options[select.selectedIndex];
            const product = {
                product_id: parseInt(productId),
                name: option.dataset.name,
                type: option.dataset.type,
                unit: option.dataset.unit || 'unid',
                unit_price: parseFloat(option.dataset.price),
                stock: parseInt(option.dataset.stock) || 0,
                quantity: 1
            };

            // Verificar se já existe no carrinho
            const existing = productsCart.find(item => item.product_id === product.product_id);
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
                productsCart.push(product);
            }

            updateProductsCart();
            select.value = '';
        }

        // Função para remover produto do carrinho
        function removeCartProduct(index) {
            productsCart.splice(index, 1);
            updateProductsCart();
        }

        // Atualizar carrinho de produtos
        function updateProductsCart() {
            const tbody = document.getElementById('products-cart-items');
            const totalEl = document.getElementById('products-cart-total');
            let total = 0;

            tbody.innerHTML = '';
            productsCart.forEach((item, index) => {
                const row = document.createElement('tr');
                const itemTotal = item.unit_price * item.quantity;
                total += itemTotal;

                row.innerHTML = `
                    <td>
                        <div class="fw-semibold">${item.name}</div>
                        <small class="text-muted">${item.type === 'product' ? 'Produto' : 'Serviço'}</small>
                    </td>
                    <td class="text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="decreaseProductQuantity(${index})">-</button>
                            <input type="number" class="form-control form-control-sm text-center mx-1" value="${item.quantity}" min="1" max="${item.type === 'product' ? item.stock : '999'}" style="width: 60px;" onchange="updateProductQuantity(${index}, this.value)">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="increaseProductQuantity(${index})">+</button>
                        </div>
                    </td>
                    <td class="text-end">MT ${item.unit_price.toFixed(2).replace('.', ',')}</td>
                    <td class="text-end fw-semibold">MT ${itemTotal.toFixed(2).replace('.', ',')}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeCartProduct(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            totalEl.textContent = `MT ${total.toFixed(2).replace('.', ',')}`;

            // Atualizar campo hidden com os produtos em formato JSON
            document.getElementById('products-json').value = JSON.stringify(productsCart);
        }

        // Funções para quantidade de produtos
        function increaseProductQuantity(index) {
            const item = productsCart[index];
            if (item.type === 'product' && item.quantity >= item.stock) {
                showToast(`Estoque máximo atingido: ${item.stock}`, 'warning');
                return;
            }
            item.quantity += 1;
            updateProductsCart();
        }

        function decreaseProductQuantity(index) {
            if (productsCart[index].quantity > 1) {
                productsCart[index].quantity -= 1;
                updateProductsCart();
            }
        }

        function updateProductQuantity(index, value) {
            const qty = parseInt(value);
            if (isNaN(qty) || qty < 1) return;

            const item = productsCart[index];
            if (item.type === 'product' && qty > item.stock) {
                showToast(`Estoque insuficiente. Máximo: ${item.stock}`, 'error');
                document.querySelector(`input[onchange="updateProductQuantity(${index}, this.value)"]`).value = item
                    .quantity;
                return;
            }
            item.quantity = qty;
            updateProductsCart();
        }
        // Função para abrir offcanvas de nova dívida
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

            // Carregar dados da dívida
            fetch(`/debts/${debtId}/edit-data`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const debt = data.data;

                        // Preencher campos básicos
                        document.getElementById('customer-name').value = debt.customer_name;
                        document.getElementById('customer-phone').value = debt.customer_phone || '';
                        document.getElementById('customer-document').value = debt.customer_document || '';
                        document.getElementById('debt-date').value = debt.debt_date;
                        document.getElementById('due-date').value = debt.due_date || '';
                        document.getElementById('description').value = debt.description;
                        document.getElementById('notes').value = debt.notes || '';

                        // Carregar produtos no carrinho (apenas para visualização - produtos não editáveis após criação)
                        if (debt.items && debt.items.length > 0) {
                            productsCart = debt.items.map(item => ({
                                product_id: item.product_id,
                                name: item.product.name,
                                type: item.product.type,
                                unit: item.product.unit || 'unid',
                                unit_price: parseFloat(item.unit_price),
                                stock: item.product.stock_quantity || 0,
                                quantity: item.quantity
                            }));
                            updateProductsCart();

                            // Desabilitar edição de produtos para dívidas existentes
                            document.getElementById('product-select').disabled = true;
                            document.querySelector('button[onclick="addProductToCart()"]').disabled = true;
                            document.querySelectorAll('#products-cart-items button').forEach(btn => btn.disabled =
                                true);
                            document.querySelectorAll('#products-cart-items input').forEach(input => input.disabled =
                                true);
                        }

                        const offcanvas = new bootstrap.Offcanvas(document.getElementById('debtFormOffcanvas'));
                        offcanvas.show();
                    } else {
                        showToast('Erro ao carregar dados da dívida', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showToast('Erro de conexão', 'error');
                });
        }

        // Resetar formulário
        function resetDebtForm() {
            document.getElementById('debt-form').reset();
            document.getElementById('debt-id').value = '';
            productsCart = [];
            updateProductsCart();
            clearValidation();

            // Reabilitar campos de produtos
            document.getElementById('product-select').disabled = false;
            document.querySelector('button[onclick="addProductToCart()"]').disabled = false;
        }

        // Validar formulário de dívida
        function validateDebtForm() {
            clearValidation();
            let isValid = true;

            const customerName = document.getElementById('customer-name').value.trim();
            const debtDate = document.getElementById('debt-date').value;
            const description = document.getElementById('description').value.trim();

            if (!customerName) {
                showFieldError('customer-name', 'Nome do cliente é obrigatório');
                isValid = false;
            }

            if (!debtDate) {
                showFieldError('debt-date', 'Data da dívida é obrigatória');
                isValid = false;
            }

            if (!description) {
                showFieldError('description', 'Descrição é obrigatória');
                isValid = false;
            }

            if (productsCart.length === 0) {
                showToast('Adicione pelo menos um produto ao carrinho', 'warning');
                isValid = false;
            }

            const dueDate = document.getElementById('due-date').value;
            if (dueDate && debtDate && new Date(dueDate) < new Date(debtDate)) {
                showFieldError('due-date', 'Data de vencimento deve ser posterior à data da dívida');
                isValid = false;
            }

            return isValid;
        }

        // Submit do formulário de dívida
        document.getElementById('debt-form').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateDebtForm()) return;

            const submitBtn = document.getElementById('save-debt-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';

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
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Offcanvas.getInstance(document.getElementById('debtFormOffcanvas')).hide();
                        showToast(data.message || 'Dívida salva com sucesso!', 'success');

                        if (data.redirect) {
                            setTimeout(() => window.location.href = data.redirect, 1000);
                        } else {
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const fieldId = field.replace('_', '-');
                                showFieldError(fieldId, data.errors[field][0]);
                            });
                        }
                        showToast(data.message || 'Erro ao salvar dívida.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showToast('Erro de conexão.', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        });
        // Função para abrir offcanvas de pagamento
        function openPaymentOffcanvas() {
            document.getElementById('payment-form').action = `/debts/{{ $debt->id }}/add-payment`;
            document.getElementById('payment-form').reset();
            document.querySelector('input[name="payment_date"]').value = new Date().toISOString().split('T')[0];
            clearValidation();

            const offcanvas = new bootstrap.Offcanvas(document.getElementById('paymentOffcanvas'));
            offcanvas.show();
        }

        // Função para marcar como paga
        function markAsPaid() {
            if (!confirm('Confirma que deseja marcar esta dívida como totalmente paga?')) {
                return;
            }

            fetch(`/debts/{{ $debt->id }}/mark-as-paid`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Dívida marcada como paga!', 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showToast(data.message || 'Erro ao marcar dívida como paga.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showToast('Erro de conexão.', 'error');
                });
        }

        // Função para confirmar cancelamento da dívida
        function confirmCancelDebt() {
            if (!confirm(
                    'Confirma o cancelamento desta dívida? Esta ação irá devolver os produtos ao estoque (se aplicável) e não pode ser desfeita.'
                )) {
                return;
            }

            fetch(`/debts/{{ $debt->id }}/cancel`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Dívida cancelada com sucesso!', 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showToast(data.message || 'Erro ao cancelar dívida.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showToast('Erro de conexão.', 'error');
                });
        }

        // Validar formulário de pagamento
        function validatePaymentForm() {
            clearValidation();
            let isValid = true;

            const amount = parseFloat(document.getElementById('payment-amount').value);
            const maxAmount = parseFloat(document.getElementById('payment-amount').max);
            const paymentMethod = document.querySelector('select[name="payment_method"]').value;
            const paymentDate = document.querySelector('input[name="payment_date"]').value;

            if (!amount || amount <= 0) {
                showFieldError('payment-amount', 'Valor do pagamento é obrigatório');
                isValid = false;
            } else if (amount > maxAmount) {
                showFieldError('payment-amount', `Valor máximo: MT ${maxAmount.toFixed(2)}`);
                isValid = false;
            }

            if (!paymentMethod) {
                showFieldError('select[name="payment_method"]', 'Forma de pagamento é obrigatória');
                isValid = false;
            }

            if (!paymentDate) {
                showFieldError('input[name="payment_date"]', 'Data do pagamento é obrigatória');
                isValid = false;
            } else if (new Date(paymentDate) > new Date()) {
                showFieldError('input[name="payment_date"]', 'Data não pode ser futura');
                isValid = false;
            }

            return isValid;
        }

        // Submit do formulário de pagamento
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
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Offcanvas.getInstance(document.getElementById('paymentOffcanvas')).hide();
                        showToast(data.message || 'Pagamento registrado com sucesso!', 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const selector = field === 'payment_method' ?
                                    'select[name="payment_method"]' :
                                    `input[name="${field}"], #${field.replace('_', '-')}`;
                                showFieldError(selector, data.errors[field][0]);
                            });
                        }
                        showToast(data.message || 'Erro ao registrar pagamento.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showToast('Erro de conexão.', 'error');
                });
        });

        // Funções utilitárias
        function showFieldError(fieldSelector, message) {
            const field = document.querySelector(fieldSelector) || document.getElementById(fieldSelector);
            if (field) {
                field.classList.add('is-invalid');
                const feedback = field.parentNode.querySelector('.invalid-feedback') ||
                    field.nextElementSibling?.classList.contains('invalid-feedback') ?
                    field.nextElementSibling : null;
                if (feedback) {
                    feedback.textContent = message;
                }
            }
        }

        function clearValidation() {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }

        function showToast(message, type = 'info') {
            const bgClass = type === 'success' ? 'bg-success' :
                type === 'error' ? 'bg-danger' :
                type === 'warning' ? 'bg-warning' : 'bg-primary';

            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white ${bgClass} border-0`;
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
    </script>
@endpush

@push('styles')
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }

        .card-header {
            font-weight: 600;
        }

        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
            border-top: none;
        }

        .badge {
            font-size: 0.75em;
            font-weight: 500;
        }

        .bg-opacity-10 {
            background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
        }

        .bg-success.bg-opacity-10 {
            background-color: rgba(var(--bs-success-rgb), 0.1) !important;
        }

        .bg-warning.bg-opacity-10 {
            background-color: rgba(var(--bs-warning-rgb), 0.1) !important;
        }

        .bg-success.bg-opacity-20 {
            background-color: rgba(var(--bs-success-rgb), 0.2) !important;
        }

        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .offcanvas {
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
        }

        .offcanvas-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
    </style>
@endpush
