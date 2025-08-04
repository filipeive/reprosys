@extends('adminlte::page')

@section('title', 'Gestão de Produtos e Serviços')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-boxes text-primary"></i>
            Gestão de Produtos e Serviços
        </h1>
        @can('admin')
        <button class="btn btn-primary" data-toggle="modal" data-target="#productModal">
            <i class="fas fa-plus mr-2"></i> Novo Produto/Serviço
        </button>
        @endcan
    </div>
@stop

@section('content')
    <!-- Notificações -->
    <div id="alerts-container"></div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter mr-2"></i> Filtros
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="search">Pesquisar Produto/Serviço</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search"
                                placeholder="Digite o nome ou descrição...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="clear-search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="type-filter">Tipo</label>
                        <select class="form-control" id="type-filter">
                            <option value="">Todos</option>
                            <option value="product">Produto</option>
                            <option value="service">Serviço</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="status-filter">Status</label>
                        <select class="form-control" id="status-filter">
                            <option value="">Todos</option>
                            <option value="active">Ativo</option>
                            <option value="inactive">Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="stock-filter">Estoque</label>
                        <select class="form-control" id="stock-filter">
                            <option value="">Todos</option>
                            <option value="low">Baixo</option>
                            <option value="ok">Normal</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Produtos -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list mr-2"></i> Produtos/Serviços Cadastrados
            </h5>
            <div class="card-tools">
                <span class="badge badge-info" id="total-products">Total: 0</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="products-table">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th width="100">Tipo</th>
                            <th width="120">Preço Venda</th>
                            <th width="100">Estoque</th>
                            <th width="80">Status</th>
                            <th width="180">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="products-tbody">
                        <tr>
                            <td colspan="8" class="loading">
                                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                                <br>Carregando produtos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Paginação -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <select class="form-control form-control-sm" id="per-page" style="width: auto;">
                        <option value="10">10 por página</option>
                        <option value="25" selected>25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </select>
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0" id="pagination">
                        <!-- Paginação será gerada via JavaScript -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal para Criar/Editar Produto -->
    <div class="modal fade" id="productModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="product-form">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-box mr-2"></i>
                            <span id="modal-title">Novo Produto/Serviço</span>
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="product-id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product-name">Nome *</label>
                                    <input type="text" class="form-control" id="product-name" required
                                        maxlength="150">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product-category">Categoria *</label>
                                    <select class="form-control" id="product-category" required>
                                        <option value="">Selecione</option>
                                        <!-- Carregar categorias via JS -->
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="product-description">Descrição</label>
                            <textarea class="form-control" id="product-description" rows="2" maxlength="500"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="product-type">Tipo *</label>
                                    <select class="form-control" id="product-type" required>
                                        <option value="">Selecione</option>
                                        <option value="product">Produto</option>
                                        <option value="service">Serviço</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="product-selling-price">Preço de Venda *</label>
                                    <input type="number" class="form-control" id="product-selling-price" min="0"
                                        step="0.01" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="product-purchase-price">Preço de Compra</label>
                                    <input type="number" class="form-control" id="product-purchase-price"
                                        min="0" step="0.01">
                                </div>
                            </div>
                        </div>
                        <div class="row" id="product-fields">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="product-stock">Estoque Inicial</label>
                                    <input type="number" class="form-control" id="product-stock" min="0">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="product-min-stock">Estoque Mínimo</label>
                                    <input type="number" class="form-control" id="product-min-stock" min="0">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="product-unit">Unidade</label>
                                    <input type="text" class="form-control" id="product-unit" maxlength="20"
                                        placeholder="ex: un, kg, m">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="product-active" checked>
                                <label class="custom-control-label" for="product-active">Produto Ativo</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Ajuste de Estoque -->
    <div class="modal fade" id="stockModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form id="stock-form">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="fas fa-cubes mr-2"></i>Ajustar Estoque
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="stock-product-id">
                        <div class="alert alert-info">
                            <strong>Produto:</strong> <span id="stock-product-name"></span><br>
                            <strong>Estoque Atual:</strong> <span id="current-stock"></span>
                        </div>
                        <div class="form-group">
                            <label for="adjustment-type">Tipo de Ajuste *</label>
                            <select class="form-control" id="adjustment-type" required>
                                <option value="">Selecione</option>
                                <option value="increase">Entrada</option>
                                <option value="decrease">Saída</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="adjustment-quantity">Quantidade *</label>
                            <input type="number" class="form-control" id="adjustment-quantity" min="1" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="adjustment-reason">Motivo *</label>
                            <input type="text" class="form-control" id="adjustment-reason" maxlength="200" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-2"></i>Ajustar Estoque
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-trash mr-2"></i>Confirmar Exclusão
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir este produto/serviço?</p>
                    <p><strong id="delete-product-name"></strong></p>
                    <small class="text-muted">Esta ação não pode ser desfeita.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">
                        <i class="fas fa-trash mr-2"></i>Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            border-radius: 10px 10px 0 0;
        }

        .table th {
            border-top: none;
            background: #f8f9fa;
        }

        .badge-status {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .badge-type {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .modal-content {
            border-radius: 10px;
        }

        .modal-header {
            border-radius: 10px 10px 0 0;
        }

        .form-control,
        .btn {
            border-radius: 5px;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }

        .pagination-sm .page-link {
            padding: 0.25rem 0.5rem;
        }

        .loading,
        .no-data {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }

        .invalid-feedback {
            display: block;
        }

        .stock-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
        }
    </style>
@stop

@section('js')
@section('js')
    <script>
        $(document).ready(function() {
            let currentPage = 1;
            let perPage = 25;
            let editingId = null;

            // CSRF Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Carregar dados iniciais
            loadCategories();
            loadProducts();

            // Event listeners
            $('#search').on('input', debounce(loadProducts, 300));
            $('#type-filter').on('change', loadProducts);
            $('#status-filter').on('change', loadProducts);
            $('#stock-filter').on('change', loadProducts);
            $('#per-page').on('change', function() {
                perPage = parseInt($(this).val());
                currentPage = 1;
                loadProducts();
            });

            $('#clear-search').click(function() {
                $('#search').val('');
                loadProducts();
            });

            // Controle de campos baseado no tipo
            $('#product-type').on('change', function() {
                const type = $(this).val();
                const productFields = $('#product-fields');

                if (type === 'service') {
                    productFields.hide();
                    $('#product-stock').prop('required', false);
                    $('#product-min-stock').prop('required', false);
                } else if (type === 'product') {
                    productFields.show();
                    $('#product-stock').prop('required', true);
                    $('#product-min-stock').prop('required', true);
                }
            });

            function debounce(func, delay) {
                let timeoutId;
                return function(...args) {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => func.apply(this, args), delay);
                };
            }

            function loadCategories() {
                $.get('/products/categories', function(data) {
                    const select = $('#product-category');
                    select.empty().append('<option value="">Selecione</option>');
                    data.forEach(category => {
                        select.append(`<option value="${category.id}">${category.name}</option>`);
                    });
                }).fail(function() {
                    showAlert('error', 'Erro ao carregar categorias');
                });
            }

            function loadProducts() {
                const filters = {
                    search: $('#search').val(),
                    type: $('#type-filter').val(),
                    status: $('#status-filter').val(),
                    stock: $('#stock-filter').val(),
                    page: currentPage,
                    per_page: perPage
                };

                $.get('{{ route('products.getProducts') }}', filters, function(data) {
                    renderTable(data.data);
                    renderPagination(data);
                    $('#total-products').text(`Total: ${data.total}`);
                }).fail(function() {
                    showAlert('error', 'Erro ao carregar produtos');
                });
            }

            function renderTable(products) {
                const tbody = $('#products-tbody');
                tbody.empty();

                if (products.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="8" class="no-data">
                                <i class="fas fa-search fa-2x mb-2"></i>
                                <br>Nenhum produto/serviço encontrado
                            </td>
                        </tr>
                    `);
                    return;
                }

                products.forEach(product => {
                    const typeBadge = product.type === 'product' ?
                        '<span class="badge badge-primary badge-type">Produto</span>' :
                        '<span class="badge badge-info badge-type">Serviço</span>';

                    const statusBadge = product.is_active ?
                        '<span class="badge badge-success badge-status">Ativo</span>' :
                        '<span class="badge badge-secondary badge-status">Inativo</span>';

                    let stockInfo = '';
                    if (product.type === 'product') {
                        const stockQuantity = product.stock_quantity || 0;
                        const minLevel = product.min_stock_level || 0;
                        const unit = product.unit || '';

                        stockInfo = `${stockQuantity} ${unit}`;
                        if (stockQuantity <= minLevel) {
                            stockInfo += ' <span class="badge badge-warning ml-1">Baixo</span>';
                        }
                    } else {
                        stockInfo = '<span class="text-muted">N/A</span>';
                    }

                    const stockButton = product.type === 'product' ?
                        `<button class="btn btn-sm btn-warning stock-btn ml-1" data-id="${product.id}" data-name="${product.name}" data-stock="${product.stock_quantity || 0}" title="Ajustar Estoque">
                            <i class="fas fa-cubes"></i>
                           </button>` :
                        '';

                    const row = `
                        <tr>
                            <td>${product.id}</td>
                            <td>
                                <strong>${product.name}</strong>
                                ${product.description ? `<br><small class="text-muted">${product.description}</small>` : ''}
                            </td>
                            <td>${product.category ? product.category.name : '-'}</td>
                            <td>${typeBadge}</td>
                            <td>MZN ${parseFloat(product.selling_price).toFixed(2)}</td>
                            <td>${stockInfo}</td>
                            <td>${statusBadge}</td>
                            <td>
                                <a href="/products/${product.id}" class="btn btn-sm btn-info show-btn" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('admin')   
                                <button class="btn btn-sm btn-primary edit-btn" data-id="${product.id}" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ${stockButton}
                                <button class="btn btn-sm btn-danger delete-btn ml-1" data-id="${product.id}" data-name="${product.name}" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endcan
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }

            function renderPagination(data) {
                const pagination = $('#pagination');
                pagination.empty();

                if (data.last_page <= 1) return;

                if (data.current_page > 1) {
                    pagination.append(`
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="${data.current_page - 1}">Anterior</a>
                        </li>
                    `);
                }

                for (let i = 1; i <= data.last_page; i++) {
                    const active = i === data.current_page ? 'active' : '';
                    pagination.append(`
                        <li class="page-item ${active}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                }

                if (data.current_page < data.last_page) {
                    pagination.append(`
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="${data.current_page + 1}">Próxima</a>
                        </li>
                    `);
                }
            }

            $(document).on('click', '.page-link', function(e) {
                e.preventDefault();
                const page = parseInt($(this).data('page'));
                if (page !== currentPage) {
                    currentPage = page;
                    loadProducts();
                }
            });

            // Modal de novo produto
            $('#productModal').on('show.bs.modal', function() {
                if (!editingId) {
                    $('#modal-title').text('Novo Produto/Serviço');
                    $('#product-form')[0].reset();
                    $('#product-active').prop('checked', true);
                    $('#product-fields').show();
                    clearValidation();
                }
            });

            // Editar produto
            $(document).on('click', '.edit-btn', function() {
                const id = parseInt($(this).data('id'));
                editingId = id;

                $.get(`/products/${id}/edit`, function(data) {
                    const product = data.product;
                    $('#modal-title').text('Editar Produto/Serviço');
                    $('#product-id').val(product.id);
                    $('#product-name').val(product.name);
                    $('#product-description').val(product.description);
                    $('#product-type').val(product.type);
                    $('#product-selling-price').val(product.selling_price);
                    $('#product-purchase-price').val(product.purchase_price);
                    $('#product-stock').val(product.stock_quantity);
                    $('#product-min-stock').val(product.min_stock_level);
                    $('#product-unit').val(product.unit);
                    $('#product-category').val(product.category_id);
                    $('#product-active').prop('checked', product.is_active);

                    // Controlar visibilidade dos campos
                    if (product.type === 'service') {
                        $('#product-fields').hide();
                    } else {
                        $('#product-fields').show();
                    }

                    clearValidation();
                    $('#productModal').modal('show');
                }).fail(function() {
                    showAlert('error', 'Erro ao carregar produto');
                });
            });

            // Salvar produto (create ou update)
            $('#product-form').submit(function(e) {
                e.preventDefault();
                console.log('Submit do form disparou');
                const editing = editingId !== null;

                const formData = {
                    name: $('#product-name').val().trim(),
                    description: $('#product-description').val().trim(),
                    type: $('#product-type').val(),
                    selling_price: parseFloat($('#product-selling-price').val()),
                    purchase_price: parseFloat($('#product-purchase-price').val()) || null,
                    category_id: $('#product-category').val(),
                    is_active: $('#product-active').prop('checked') ? 1 : 0
                };

                // Campos específicos para produtos
                if (formData.type === 'product') {
                    formData.stock_quantity = parseInt($('#product-stock').val()) || 0;
                    formData.min_stock_level = parseInt($('#product-min-stock').val()) || 0;
                    formData.unit = $('#product-unit').val().trim();
                }

                // Corrigir método HTTP via _method para update
                if (editing) {
                    formData._method = 'PUT'; // Laravel interpreta como PUT
                }

                const url = editing ? `/products/${editingId}` : '/products';

                $.ajax({
                    url: url,
                    method: 'POST', // Sempre POST — usamos _method para simular PUT
                    data: formData,
                    success: function(response) {
                        $('#productModal').modal('hide');
                        editingId = null;
                        loadProducts();
                        showAlert('success', response.message);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            showValidationErrors(errors);
                        } else {
                            showAlert('error', 'Erro ao salvar produto');
                        }
                    }
                });
            });


            // Ajustar estoque
            $(document).on('click', '.stock-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const currentStock = $(this).data('stock');

                $('#stock-product-id').val(id);
                $('#stock-product-name').text(name);
                $('#current-stock').text(currentStock);
                $('#stock-form')[0].reset();
                clearValidation();
                $('#stockModal').modal('show');
            });

            $('#stock-form').submit(function(e) {
                e.preventDefault();

                const productId = $('#stock-product-id').val();
                const formData = {
                    adjustment_type: $('#adjustment-type').val(),
                    quantity: parseInt($('#adjustment-quantity').val()),
                    reason: $('#adjustment-reason').val().trim()
                };

                $.ajax({
                    url: `/products/${productId}/adjust-stock`,
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#stockModal').modal('hide');
                        loadProducts();
                        showAlert('success', 'Estoque ajustado com sucesso');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            showValidationErrors(errors, 'stock');
                        } else {
                            showAlert('error', 'Erro ao ajustar estoque');
                        }
                    }
                });
            });

            // Excluir produto
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                $('#delete-product-name').text(name);
                $('#deleteModal').modal('show');

                $('#confirm-delete').off('click').on('click', function() {
                    $.ajax({
                        url: `/products/${id}`,
                        method: 'DELETE',
                        success: function(response) {
                            $('#deleteModal').modal('hide');
                            loadProducts();
                            showAlert('success', response.message);
                        },
                        error: function(xhr) {
                            $('#deleteModal').modal('hide');
                            const message = xhr.responseJSON?.message ||
                                'Erro ao excluir produto';
                            showAlert('error', message);
                        }
                    });
                });
            });

            function showValidationErrors(errors, context = 'product') {
                clearValidation();

                const fieldMappings = {
                    product: {
                        'name': '#product-name',
                        'category_id': '#product-category',
                        'type': '#product-type',
                        'selling_price': '#product-selling-price',
                        'purchase_price': '#product-purchase-price',
                        'stock_quantity': '#product-stock',
                        'min_stock_level': '#product-min-stock'
                    },
                    stock: {
                        'adjustment_type': '#adjustment-type',
                        'quantity': '#adjustment-quantity',
                        'reason': '#adjustment-reason'
                    }
                };

                const mappings = fieldMappings[context];

                $.each(errors, function(field, messages) {
                    const fieldId = mappings[field];
                    if (fieldId) {
                        $(fieldId).addClass('is-invalid');
                        $(fieldId).next('.invalid-feedback').text(messages[0]);
                    }
                });
            }

            function clearValidation() {
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            function showAlert(type, message) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const alert = `
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `;
                $('#alerts-container').html(alert);

                // Remove automaticamente após 5 segundos
                setTimeout(() => {
                    $('.alert').alert('close');
                }, 5000);
            }
        });
    </script>
@stop
