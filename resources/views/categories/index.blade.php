@extends('adminlte::page')

@section('title', 'Gestão de Categorias - Sistema Reprografia')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-tags text-primary"></i>
            Gestão de Categorias
        </h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#categoryModal">
            <i class="fas fa-plus mr-2"></i> Nova Categoria
        </button>
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

    <!-- Filtros e Pesquisa -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter mr-2"></i> Filtros
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="search">Pesquisar Categoria</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search"
                                placeholder="Digite o nome da categoria...">
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
                        <label for="status-filter">Status</label>
                        <select class="form-control" id="status-filter">
                            <option value="">Todos</option>
                            <option value="active">Ativo</option>
                            <option value="inactive">Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="type-filter">Tipo</label>
                        <select class="form-control" id="type-filter">
                            <option value="">Todos</option>
                            <option value="product">Produtos</option>
                            <option value="service">Serviços</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Categorias -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list mr-2"></i> Categorias Cadastradas
            </h5>
            <div class="card-tools">
                <span class="badge badge-info" id="total-categories">Total: {{ $categories->count() }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="categories-table">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th width="100">Tipo</th>
                            <th width="80">Status</th>
                            <th width="100">Produtos</th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="categories-tbody">
                        @forelse($categories as $category)
                            <tr data-id="{{ $category->id }}" 
                                data-name="{{ $category->name }}" 
                                data-description="{{ $category->description }}"
                                data-type="{{ $category->type }}"
                                data-color="{{ $category->color }}"
                                data-icon="{{ $category->icon }}"
                                data-status="{{ $category->status }}">
                                <td>{{ $category->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="category-color mr-2"
                                            style="background-color: {{ $category->color }}"></span>
                                        <i class="{{ $category->icon }} category-icon mr-2"></i>
                                        <strong>{{ $category->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $category->description ?? '-' }}</td>
                                <td>
                                    @if ($category->type === 'product')
                                        <span class="badge badge-primary badge-type">Produto</span>
                                    @else
                                        <span class="badge badge-info badge-type">Serviço</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($category->status === 'active')
                                        <span class="badge badge-success badge-status">Ativo</span>
                                    @else
                                        <span class="badge badge-secondary badge-status">Inativo</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light">{{ $category->products_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-btn" 
                                            data-id="{{ $category->id }}" 
                                            title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn ml-1" 
                                            data-id="{{ $category->id }}" 
                                            data-name="{{ $category->name }}" 
                                            title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="no-data-row">
                                <td colspan="7" class="no-data">
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <br>Nenhuma categoria encontrada
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para Criar/Editar Categoria -->
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="category-form" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-tag mr-2"></i>
                            <span id="modal-title">Nova Categoria</span>
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="category-id">
                        <input type="hidden" name="_method" id="form-method" value="POST">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category-name">Nome da Categoria *</label>
                                    <input type="text" class="form-control" id="category-name" name="name"
                                        placeholder="Digite o nome da categoria" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category-type">Tipo *</label>
                                    <select class="form-control" id="category-type" name="type" required>
                                        <option value="">Selecione o tipo</option>
                                        <option value="product">Produtos</option>
                                        <option value="service">Serviços</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="category-description">Descrição</label>
                            <textarea class="form-control" id="category-description" name="description" rows="3"
                                placeholder="Descreva a categoria..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category-color">Cor da Categoria</label>
                                    <input type="color" class="form-control" id="category-color" name="color"
                                        value="#007bff">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category-icon">Ícone</label>
                                    <select class="form-control" id="category-icon" name="icon">
                                        <option value="fas fa-box">📦 Caixa</option>
                                        <option value="fas fa-print">🖨️ Impressão</option>
                                        <option value="fas fa-cut">✂️ Corte</option>
                                        <option value="fas fa-palette">🎨 Design</option>
                                        <option value="fas fa-tools">🔧 Ferramentas</option>
                                        <option value="fas fa-file-alt">📄 Documentos</option>
                                        <option value="fas fa-image">🖼️ Imagens</option>
                                        <option value="fas fa-tshirt">👕 Vestuário</option>
                                        <option value="fas fa-gift">🎁 Presentes</option>
                                        <option value="fas fa-star">⭐ Especial</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="status" value="inactive">
                                <input type="checkbox" class="custom-control-input" id="category-active" name="status"
                                    value="active" checked>
                                <label class="custom-control-label" for="category-active">Categoria Ativa</label>
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
                    <p>Tem certeza que deseja excluir esta categoria?</p>
                    <p><strong id="delete-category-name"></strong></p>
                    <small class="text-muted">Esta ação não pode ser desfeita.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <form id="delete-form" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-2"></i>Excluir
                        </button>
                    </form>
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

        .category-icon {
            width: 20px;
            text-align: center;
        }

        .category-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
            border: 1px solid #dee2e6;
        }

        .modal-content {
            border-radius: 10px;
        }

        .modal-header {
            border-radius: 10px 10px 0 0;
        }

        .form-control {
            border-radius: 5px;
        }

        .btn {
            border-radius: 5px;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }

        .no-data {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }

        .invalid-feedback {
            display: block;
        }

        .hidden {
            display: none !important;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let editingId = null;
            const originalRows = $('#categories-tbody tr').clone();

            // Filtros
            $('#search').on('input', filterCategories);
            $('#status-filter').on('change', filterCategories);
            $('#type-filter').on('change', filterCategories);

            $('#clear-search').click(function() {
                $('#search').val('');
                filterCategories();
            });

            function filterCategories() {
                const search = $('#search').val().toLowerCase();
                const statusFilter = $('#status-filter').val();
                const typeFilter = $('#type-filter').val();
                let visibleCount = 0;

                $('#categories-tbody tr').each(function() {
                    const $row = $(this);
                    
                    // Pular linha de "nenhum dado"
                    if ($row.find('.no-data').length > 0) {
                        return;
                    }

                    const name = $row.data('name') ? $row.data('name').toString().toLowerCase() : '';
                    const description = $row.data('description') ? $row.data('description').toString().toLowerCase() : '';
                    const status = $row.data('status');
                    const type = $row.data('type');

                    const matchesSearch = name.includes(search) || description.includes(search);
                    const matchesStatus = !statusFilter || status === statusFilter;
                    const matchesType = !typeFilter || type === typeFilter;

                    if (matchesSearch && matchesStatus && matchesType) {
                        $row.show();
                        visibleCount++;
                    } else {
                        $row.hide();
                    }
                });

                // Mostrar/ocultar linha de "nenhum dado"
                if (visibleCount === 0) {
                    if ($('#no-results-row').length === 0) {
                        $('#categories-tbody').append(`
                            <tr id="no-results-row">
                                <td colspan="7" class="no-data">
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <br>Nenhuma categoria encontrada com os filtros aplicados
                                </td>
                            </tr>
                        `);
                    }
                } else {
                    $('#no-results-row').remove();
                }

                $('#total-categories').text(`Total: ${visibleCount}`);
            }

            // Abrir modal para nova categoria
            $('button[data-target="#categoryModal"]').click(function() {
                resetModal();
            });

            $('#categoryModal').on('hidden.bs.modal', function() {
                resetModal();
            });

            function resetModal() {
                editingId = null;
                $('#modal-title').text('Nova Categoria');
                $('#category-form')[0].reset();
                $('#category-form').attr('action', '{{ route("categories.store") }}');
                $('#form-method').val('POST');
                $('#category-id').val('');
                $('#category-color').val('#007bff');
                $('#category-active').prop('checked', true);
                clearValidation();
            }

            // Editar categoria
            $(document).on('click', '.edit-btn', function() {
                const $row = $(this).closest('tr');
                const id = $row.data('id');
                
                editingId = id;
                $('#modal-title').text('Editar Categoria');
                $('#category-form').attr('action', `/categories/${id}`);
                $('#form-method').val('PUT');
                $('#category-id').val(id);
                $('#category-name').val($row.data('name'));
                $('#category-description').val($row.data('description'));
                $('#category-type').val($row.data('type'));
                $('#category-color').val($row.data('color'));
                $('#category-icon').val($row.data('icon'));
                $('#category-active').prop('checked', $row.data('status') === 'active');
                
                clearValidation();
                $('#categoryModal').modal('show');
            });

            // Excluir categoria
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                $('#delete-category-name').text(name);
                $('#delete-form').attr('action', `/categories/${id}`);
                $('#deleteModal').modal('show');
            });

            // Validação do formulário
            $('#category-form').submit(function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    return false;
                }
            });

            function validateForm() {
                clearValidation();
                let isValid = true;

                const name = $('#category-name').val().trim();
                const type = $('#category-type').val();

                if (!name) {
                    showFieldError('#category-name', 'Nome é obrigatório');
                    isValid = false;
                }

                if (!type) {
                    showFieldError('#category-type', 'Tipo é obrigatório');
                    isValid = false;
                }

                // Verificar nome duplicado (apenas no frontend para UX)
                if (name) {
                    let isDuplicate = false;
                    $('#categories-tbody tr').each(function() {
                        const $row = $(this);
                        const rowId = $row.data('id');
                        const rowName = $row.data('name');
                        
                        if (rowName && rowName.toLowerCase() === name.toLowerCase() && rowId != editingId) {
                            isDuplicate = true;
                            return false;
                        }
                    });

                    if (isDuplicate) {
                        showFieldError('#category-name', 'Já existe uma categoria com este nome');
                        isValid = false;
                    }
                }

                return isValid;
            }

            function showFieldError(fieldId, message) {
                const field = $(fieldId);
                field.addClass('is-invalid');
                field.siblings('.invalid-feedback').text(message);
            }

            function clearValidation() {
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            // Auto-fechar alertas
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 3000);
        });
    </script>
@stop