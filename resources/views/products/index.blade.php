@extends('layouts.app')

@section('title', 'Produtos')
@section('page-title', 'Produtos e Serviços')

@php
    $titleIcon = 'fas fa-box me-2';
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item active">Produtos</li>
@endsection

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-box me-2"></i>
                Produtos e Serviços
            </h2>
            <p class="text-muted mb-0">Gerencie seus produtos e serviços da reprografia</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.create') }}?type=product" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Novo Produto
            </a>
            <a href="{{ route('products.create') }}?type=service" class="btn btn-info">
                <i class="fas fa-concierge-bell me-2"></i> Novo Serviço
            </a>
            <a href="{{ route('categories.index') }}" class="btn btn-success">
                <i class="fas fa-tags me-2"></i> Categorias
            </a>
            <a href="{{ route('products.report') }}" class="btn btn-secondary">
                <i class="fas fa-file-export me-2"></i> Exportar
            </a>
        </div>
    </div>
    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card h-100 border-start border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total de Produtos</h6>
                            <h3 class="mb-0 text-primary">{{ $allProducts->where('type', 'product')->count() }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card h-100 border-start border-4 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total de Serviços</h6>
                            <h3 class="mb-0 text-success">{{ $allProducts->where('type', 'service')->count() }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-concierge-bell fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card h-100 border-start border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Estoque Baixo</h6>
                            <h3 class="mb-0 text-warning">{{ $lowStockCount ?? 0 }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card h-100 border-start border-4 border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Categorias</h6>
                            <h3 class="mb-0 text-info">{{ $categories->count() ?? 0 }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filtros e Pesquisa
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Pesquisar Produto</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                            placeholder="Nome do produto ou serviço...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tipo</label>
                        <select class="form-select" name="type">
                            <option value="">Todos</option>
                            <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>Produto</option>
                            <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>Serviço</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Categoria</label>
                        <select class="form-select" name="category_id">
                            <option value="">Todas</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i> Limpar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Produtos -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Lista de Produtos e Serviços
                </h5>
                <span class="badge bg-primary">Total: {{ $products->total() }}</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Produto/Serviço</th>
                            <th>Categoria</th>
                            <th>Tipo</th>
                            <th>Preço</th>
                            <th>Estoque</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td><span class="fw-bold text-primary">#{{ $product->id }}</span></td>
                                <td>
                                    <div>
                                        <span class="fw-semibold">{{ $product->name }}</span>
                                        @if ($product->description)
                                            <br><small
                                                class="text-muted">{{ Str::limit($product->description, 40) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $product->category->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if ($product->type === 'product')
                                        <span class="badge bg-primary">
                                            <i class="fas fa-box me-1"></i>Produto
                                        </span>
                                    @else
                                        <span class="badge bg-info">
                                            <i class="fas fa-concierge-bell me-1"></i>Serviço
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-success">
                                        {{ number_format($product->selling_price, 2, ',', '.') }} MT
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($product->type === 'product')
                                        <div class="stock-info">
                                            <span class="fw-semibold">
                                                {{ $product->stock_quantity }} {{ $product->unit }}
                                            </span>
                                            @if ($product->isLowStock())
                                                <br><span class="badge bg-warning">Baixo</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($product->is_active)
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-secondary">Inativo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('products.edit', $product->id) }}"
                                            class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        @if ($product->type === 'product')
                                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                                                data-bs-target="#stockModal{{ $product->id }}" title="Ajustar Estoque">
                                                <i class="fas fa-cubes"></i>
                                            </button>
                                        @endif

                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-info"
                                            title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $product->id }}" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        <!-- Toggle Status -->
                                        <form method="POST" action="{{ route('products.update', $product->id) }}"
                                            class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="toggle_status" value="1">
                                            <input type="hidden" name="is_active"
                                                value="{{ $product->is_active ? '0' : '1' }}">
                                            <button type="submit"
                                                class="btn btn-outline-{{ $product->is_active ? 'warning' : 'success' }}"
                                                title="{{ $product->is_active ? 'Desativar' : 'Ativar' }}">
                                                <i
                                                    class="fas fa-{{ $product->is_active ? 'toggle-off' : 'toggle-on' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal de Ajuste de Estoque -->
                            @if ($product->type === 'product')
                                <div class="modal fade" id="stockModal{{ $product->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST"
                                                action="{{ route('products.adjust-stock', $product->id) }}">
                                                @csrf
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-cubes me-2"></i>Ajustar Estoque
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-light mb-3">
                                                        <h6 class="mb-1">{{ $product->name }}</h6>
                                                        <small class="text-muted">
                                                            Estoque atual: {{ $product->stock_quantity }}
                                                            {{ $product->unit }}
                                                        </small>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Tipo de Ajuste *</label>
                                                        <select class="form-select" name="adjustment_type" required>
                                                            <option value="">Selecione</option>
                                                            <option value="increase">Entrada (+)</option>
                                                            <option value="decrease">Saída (-)</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Quantidade *</label>
                                                        <input type="number" class="form-control" name="quantity"
                                                            min="1" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Motivo *</label>
                                                        <textarea class="form-control" name="reason" rows="3" maxlength="200" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        Cancelar
                                                    </button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fas fa-save me-2"></i>Confirmar Ajuste
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Modal de Exclusão -->
                            <div class="modal fade" id="deleteModal{{ $product->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('products.destroy', $product->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-trash me-2"></i>Confirmar Exclusão
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center">
                                                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                                                    <h5>Deseja excluir este produto?</h5>
                                                    <p class="text-muted">
                                                        <strong>{{ $product->name }}</strong><br>
                                                        Esta ação não pode ser desfeita.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Cancelar
                                                </button>
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-trash me-2"></i>Excluir Produto
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3 opacity-50"></i>
                                        <h5>Nenhum produto encontrado</h5>
                                        <p>Não há produtos que correspondam aos filtros aplicados.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if ($products->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Mostrando {{ $products->firstItem() }} a {{ $products->lastItem() }}
                            de {{ $products->total() }} produtos
                        </div>
                        {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .product-row {
            transition: all 0.2s ease;
        }

        .product-row:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .status-toggle {
            cursor: pointer;
        }

        .stock-info {
            min-width: 80px;
        }

        .btn-group .btn {
            border-radius: 0;
        }

        .btn-group .btn:first-child {
            border-top-left-radius: 0.375rem;
            border-bottom-left-radius: 0.375rem;
        }

        .btn-group .btn:last-child {
            border-top-right-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Exibir mensagens de sessão como Toasts
            @if (session('success'))
                FDSMULTSERVICES.Toast.show("{{ session('success') }}", 'success');
            @endif

            @if (session('error'))
                FDSMULTSERVICES.Toast.show("{{ session('error') }}", 'error');
            @endif

            @if (session('warning'))
                FDSMULTSERVICES.Toast.show("{{ session('warning') }}", 'warning');
            @endif

            @if (session('info'))
                FDSMULTSERVICES.Toast.show("{{ session('info') }}", 'info');
            @endif
        });
        // Confirmação adicional para ações críticas
        const deleteButtons = document.querySelectorAll('button[data-bs-target*="deleteModal"]');
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const productName = this.closest('tr').querySelector('.fw-semibold').textContent;
                console.log('Preparando exclusão de:', productName);
            });
        });
    </script>
@endpush
