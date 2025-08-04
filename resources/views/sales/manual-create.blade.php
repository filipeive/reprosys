@extends('adminlte::page')

@section('title', 'Registrar Venda Manual')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-2">
                <i class="fas fa-history text-primary me-2"></i> 
                <span class="fw-bold">Registrar Venda Manual</span>
            </h1>
            <p class="text-muted mb-0 fs-6">
                <i class="fas fa-info-circle me-1"></i>
                Use esta tela para lançar vendas antigas do livro físico, informando a data/hora real da venda.
            </p>
        </div>
        <a href="{{ route('sales.index') }}" class="btn btn-outline-primary btn-lg">
            <i class="fas fa-arrow-left me-2"></i> Voltar
        </a>
    </div>

    <!-- Progress Steps -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="progress-steps">
                <div class="step active">
                    <div class="step-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <span>Data & Vendedor</span>
                </div>
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <span>Pagamento</span>
                </div>
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <span>Produtos</span>
                </div>
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <span>Finalizar</span>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <form action="{{ route('sales.store') }}" method="POST" id="manual-sale-form">
        @csrf
        
        <!-- Card 1: Informações da Venda -->
        <div class="card mb-4 shadow-lg border-0 modern-card">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Informações da Venda
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="datetime-local" class="form-control form-control-lg" name="sale_date" id="sale_date" required>
                            <label for="sale_date">
                                <i class="fas fa-clock me-2 text-primary"></i>Data e Hora da Venda *
                            </label>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-lightbulb me-1"></i>
                            Informe a data/hora real quando a venda foi realizada
                        </small>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control form-control-lg bg-light" value="{{ Auth::user()->name }}" disabled>
                            <label>
                                <i class="fas fa-user me-2 text-success"></i>Vendedor Responsável
                            </label>
                        </div>
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-shield-alt me-1"></i>
                            Vendedor autenticado no sistema
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Dados do Cliente -->
        <div class="card mb-4 shadow-lg border-0 modern-card">
            <div class="card-header bg-gradient-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users me-2"></i>
                    Dados do Cliente
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control form-control-lg" name="customer_name" id="customer_name" value="n/a">
                            <label for="customer_name">
                                <i class="fas fa-user-tag me-2 text-info"></i>Nome do Cliente
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control form-control-lg" name="customer_phone" id="customer_phone" placeholder="(00) 00000-0000">
                            <label for="customer_phone">
                                <i class="fas fa-phone me-2 text-info"></i>Telefone de Contato
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Método de Pagamento -->
        <div class="card mb-4 shadow-lg border-0 modern-card">
            <div class="card-header bg-gradient-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-credit-card me-2"></i>
                    Método de Pagamento
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="payment_method" class="form-label fw-bold">
                            <i class="fas fa-money-bill-wave me-2 text-warning"></i>
                            Forma de Pagamento *
                        </label>
                        <select class="form-select form-select-lg" name="payment_method" id="payment_method" required>
                            <option value="">Selecione uma opção...</option>
                            <option value="cash" data-icon="fas fa-money-bill-alt">💵 Dinheiro</option>
                            <option value="card" data-icon="fas fa-credit-card">💳 Cartão</option>
                            <option value="transfer" data-icon="fas fa-exchange-alt">🏦 Transferência</option>
                            <option value="credit" data-icon="fas fa-handshake">🤝 Crédito</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="notes" class="form-label fw-bold">
                            <i class="fas fa-sticky-note me-2 text-warning"></i>
                            Observações
                        </label>
                        <textarea class="form-control form-control-lg" name="notes" id="notes" rows="3" 
                                  placeholder="Ex: Venda referente ao livro físico, desconto aplicado, etc..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Produtos/Serviços -->
        <div class="card mb-4 shadow-lg border-0 modern-card">
            <div class="card-header bg-gradient-success text-white d-flex justify-content-between align-items-center">
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
                <div class="table-responsive">
                    <table class="table table-hover mb-0 modern-table" id="products-table">
                        <thead class="table-success">
                            <tr>
                                <th class="text-center" width="80">
                                    <i class="fas fa-check"></i>
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
                                <tr class="product-row" data-product-id="{{ $product->id }}">
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
                                                <i class="fas fa-barcode me-1"></i>
                                                Cód: {{ $product->id }}
                                            </small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">MZN</span>
                                            <input type="number" step="0.01" min="0" 
                                                   value="{{ $product->selling_price }}" 
                                                   class="form-control text-end unit-price">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" min="0" 
                                               class="form-control form-control-sm text-center quantity" 
                                               placeholder="0">
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-success subtotal" data-subtotal="0">
                                            MZN 0,00
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger clear-row" 
                                                title="Limpar linha">
                                            <i class="fas fa-eraser"></i>
                                        </button>
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
                        </tfoot>
                    </table>
                </div>
                <div class="p-3 bg-light border-top">
                    <small class="text-muted">
                        <i class="fas fa-lightbulb me-1 text-warning"></i>
                        <strong>Dica:</strong> Selecione os produtos desejados e informe a quantidade. O preço pode ser editado se necessário.
                    </small>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="card shadow-lg border-0 modern-card">
            <div class="card-body p-4">
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
                        <button type="submit" class="btn btn-success btn-lg px-4">
                            <i class="fas fa-save me-2"></i>
                            Registrar Venda Manual
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <style>
        /* Cards Modernos */
        .modern-card {
            border-radius: 1.25rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .modern-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
        }

        /* Gradientes para Headers */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }
        
        .bg-gradient-info {
            background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
        }
        
        .bg-gradient-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        }
        
        .bg-gradient-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        }

        /* Progress Steps */
        .progress-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
            position: relative;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 10%;
            right: 10%;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .step.active .step-icon {
            background: #007bff;
            color: white;
            transform: scale(1.1);
        }

        .step span {
            font-size: 0.875rem;
            font-weight: 600;
            color: #6c757d;
        }

        .step.active span {
            color: #007bff;
        }

        /* Formulários Modernos */
        .form-floating > .form-control:focus,
        .form-floating > .form-control:not(:placeholder-shown) {
            padding-top: 1.625rem;
            padding-bottom: 0.625rem;
        }

        .form-floating > label {
            opacity: 0.65;
            transform: scale(0.85) translateY(-0.5rem);
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Tabela Moderna */
        .modern-table {
            border: none;
        }

        .modern-table thead th {
            border: none;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            padding: 1rem 0.75rem;
        }

        .modern-table tbody tr {
            border: none;
            transition: all 0.2s ease;
        }

        .modern-table tbody tr:hover {
            background: #f8f9fa;
            transform: scale(1.01);
        }

        .modern-table tbody td {
            border: none;
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        /* Product Info */
        .product-info {
            padding: 0.5rem 0;
        }

        /* Input Groups */
        .input-group-text {
            background: #f8f9fa;
            border-color: #dee2e6;
            font-weight: 600;
        }

        /* Botões */
        .btn {
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-lg {
            padding: 0.75rem 1.5rem;
        }

        /* Clear Row Button */
        .clear-row {
            transition: all 0.2s ease;
        }

        .clear-row:hover {
            background: #f8d7da;
            border-color: #f5c6cb;
            transform: scale(1.1);
        }

        /* Total Display */
        .total-display .badge {
            font-size: 1rem !important;
            border-radius: 2rem;
        }

        /* Form Check */
        .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }

        /* Subtotal */
        .subtotal {
            font-size: 1.1rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .progress-steps {
                flex-wrap: wrap;
                gap: 1rem;
            }
            
            .step {
                flex: 1;
                min-width: 120px;
            }
            
            .modern-card:hover {
                transform: none;
            }
        }

        /* Animações */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modern-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .modern-card:nth-child(2) { animation-delay: 0.1s; }
        .modern-card:nth-child(3) { animation-delay: 0.2s; }
        .modern-card:nth-child(4) { animation-delay: 0.3s; }
        .modern-card:nth-child(5) { animation-delay: 0.4s; }
    </style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Função para calcular subtotal
        function calculateSubtotal(row) {
            const unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
            const quantity = parseInt(row.find('.quantity').val()) || 0;
            const subtotal = unitPrice * quantity;
            
            row.find('.subtotal').text('MZN ' + subtotal.toFixed(2).replace('.', ','));
            row.find('.subtotal').attr('data-subtotal', subtotal);
            
            calculateTotal();
        }

        // Função para calcular total geral
        function calculateTotal() {
            let total = 0;
            $('.subtotal').each(function() {
                total += parseFloat($(this).attr('data-subtotal')) || 0;
            });
            
            const formattedTotal = total.toFixed(2).replace('.', ',');
            $('#total-amount').text(formattedTotal);
            $('#footer-total').text(formattedTotal);
        }

        // Event listeners para cálculos
        $(document).on('input', '.unit-price, .quantity', function() {
            const row = $(this).closest('tr');
            calculateSubtotal(row);
        });

        // Ao selecionar um produto, focar na quantidade
        $(document).on('change', '.select-product', function() {
            if ($(this).is(':checked')) {
                $(this).closest('tr').find('.quantity').focus();
            } else {
                const row = $(this).closest('tr');
                row.find('.quantity').val('');
                calculateSubtotal(row);
            }
        });

        // Limpar linha
        $(document).on('click', '.clear-row', function() {
            const row = $(this).closest('tr');
            row.find('input[type="number"]').val('');
            row.find('.select-product').prop('checked', false);
            calculateSubtotal(row);
            
            // Animação de feedback
            $(this).addClass('btn-danger').removeClass('btn-outline-danger');
            setTimeout(() => {
                $(this).removeClass('btn-danger').addClass('btn-outline-danger');
            }, 200);
        });

        // Validação do formulário
        $('#manual-sale-form').submit(function(event) {
            let items = [];
            let hasError = false;
            
            $('#products-table tbody tr').each(function() {
                const row = $(this);
                const productId = row.data('product-id');
                const isSelected = row.find('.select-product').prop('checked');
                const unitPrice = parseFloat(row.find('.unit-price').val());
                const quantity = parseInt(row.find('.quantity').val());

                if (isSelected && quantity > 0) {
                    items.push({
                        product_id: productId,
                        unit_price: unitPrice,
                        quantity: quantity
                    });
                }
            });

            // Validar se há pelo menos um item
            if (items.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção!',
                    text: 'Selecione pelo menos um produto com quantidade maior que zero.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#007bff'
                });
                event.preventDefault();
                return false;
            }

            // Validar campos obrigatórios
            if (!$('#sale_date').val()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campo obrigatório',
                    text: 'Por favor, informe a data e hora da venda.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
                $('#sale_date').focus();
                event.preventDefault();
                return false;
            }

            if (!$('#payment_method').val()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campo obrigatório',
                    text: 'Por favor, selecione o método de pagamento.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
                $('#payment_method').focus();
                event.preventDefault();
                return false;
            }

            // Converter items para JSON e adicionar ao formulário (compatibilidade com controller)
            console.log('Items para enviar:', items); // Debug
            
            if ($('#items-json').length === 0) {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'items-json',
                    name: 'items',
                    value: JSON.stringify(items)
                }).appendTo('#manual-sale-form');
            } else {
                $('#items-json').val(JSON.stringify(items));
            }

            console.log('JSON enviado:', JSON.stringify(items)); // Debug

            // Mostrar loading
            Swal.fire({
                title: 'Processando...',
                text: 'Registrando a venda manual...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });

        // Máscara para telefone
        $('#customer_phone').mask('(00) 00000-0000');

        // Auto-calcular quando página carrega
        calculateTotal();

        // Animação dos steps (exemplo)
        setTimeout(() => {
            $('.progress-steps .step:nth-child(2)').addClass('active');
        }, 2000);
    });
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- jQuery Mask Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
@stop