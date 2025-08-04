@extends('adminlte::page')

@section('title', 'Movimentos de Estoque')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-warehouse text-primary"></i>
            Movimentos de Estoque
        </h1>
        <a href="{{ route('stock-movements.create') }}" class="btn btn-success">
            <i class="fas fa-plus mr-2"></i> Registrar Movimento
        </a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <i class="fas fa-check mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter mr-2"></i> Filtros
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('stock-movements.index') }}" id="filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="product">Produto</label>
                            <input type="text" class="form-control" id="product" name="product"
                                value="{{ request('product') }}" placeholder="Nome do produto...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_from">Data Inicial</label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_to">Data Final</label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="movement_type">Tipo</label>
                            <select class="form-control" id="movement_type" name="movement_type">
                                <option value="">Todos</option>
                                <option value="in" {{ request('movement_type') == 'in' ? 'selected' : '' }}>Entrada</option>
                                <option value="out" {{ request('movement_type') == 'out' ? 'selected' : '' }}>Saída</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search mr-1"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Movimentos -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list mr-2"></i> Movimentos Registrados
            </h5>
            <div class="card-tools">
                <span class="badge badge-info">Total: {{ $movements->total() }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0" id="movements-table">
                    <thead class="thead-light">
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Produto</th>
                            <th>Tipo</th>
                            <th>Quantidade</th>
                            <th>Usuário</th>
                            <th>Motivo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            <tr>
                                <td>{{ $movement->movement_date->format('d/m/Y') }}</td>
                                <td>{{ $movement->created_at->format('H:i') }}</td>
                                <td>{{ $movement->product->name ?? '-' }}</td>
                                <td>
                                    @if($movement->movement_type === 'in')
                                        <span class="badge badge-success">Entrada</span>
                                    @else
                                        <span class="badge badge-danger">Saída</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $movement->quantity }}</strong>
                                </td>
                                <td>{{ $movement->user->name ?? '-' }}</td>
                                <td>{{ $movement->reason ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('stock-movements.show', $movement) }}" class="btn btn-info btn-sm" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('delete-stock-movement', $movement)
                                        <form action="{{ route('stock-movements.destroy', $movement) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Tem certeza que deseja excluir este movimento?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="no-data text-center">
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <br>Nenhum movimento encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Mostrando {{ $movements->firstItem() ?? 0 }} a {{ $movements->lastItem() ?? 0 }} 
                        de {{ $movements->total() }} resultados
                    </small>
                </div>
                <nav>
                    {{ $movements->appends(request()->query())->links('pagination::bootstrap-5') }}
                </nav>
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
        .badge {
            font-size: 0.85rem;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto-submit nos filtros
            $('#date_from, #date_to, #movement_type').on('change', function() {
                $('#filter-form').submit();
            });

            // Limpar pesquisa de produto
            $('#product').on('input', function(e) {
                if ($(this).val() === '') {
                    $('#filter-form').submit();
                }
            });

            // Auto-fechar alertas
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 4000);

            // Tooltips
            $('[title]').tooltip();
        });
    </script>
@stop