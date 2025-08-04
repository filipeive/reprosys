{{-- filepath: resources/views/expenses/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Gestão de Despesas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-money-bill-wave text-danger"></i>
            Gestão de Despesas
        </h1>
        <a href="{{ route('expenses.create') }}" class="btn btn-success">
            <i class="fas fa-plus mr-2"></i> Adicionar Despesa
        </a>
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

    <!-- Resumo -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total de Despesas</span>
                    <span class="info-box-number">{{ number_format($totalExpenses, 2, ',', '.') }} MT</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Média de Despesas</span>
                    <span class="info-box-number">{{ number_format($averageExpense, 2, ',', '.') }} MT</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-arrow-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Maior Despesa</span>
                    <span class="info-box-number">{{ number_format($highestExpense, 2, ',', '.') }} MT</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-arrow-down"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Menor Despesa</span>
                    <span class="info-box-number">{{ number_format($lowestExpense, 2, ',', '.') }} MT</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter mr-2"></i> Filtros
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('expenses.index') }}" id="filter-form">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Pesquisar Descrição</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Descrição da despesa...">
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
                            <label for="date_from">Data Inicial</label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_to">Data Final</label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
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

    <!-- Lista de Despesas -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list mr-2"></i> Despesas Registradas
            </h5>
            <div class="card-tools">
                <span class="badge badge-info">Total: {{ $expenses->total() }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="expenses-table">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Data</th>
                            <th>Categoria</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Usuário</th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr>
                                <td>{{ $expense->id }}</td>
                                <td>
                                    <strong>{{ $expense->expense_date->format('d/m/Y') }}</strong>
                                </td>
                                <td>{{ $expense->category->name ?? '-' }}</td>
                                <td>{{ $expense->description }}</td>
                                <td>
                                    <strong class="text-danger">{{ number_format($expense->amount, 2, ',', '.') }} MT</strong>
                                </td>
                                <td>
                                    <small>{{ $expense->user->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-info" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir"
                                                onclick="return confirm('Tem certeza que deseja excluir esta despesa?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="no-data">
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <br>Nenhuma despesa encontrada
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">
                        Mostrando {{ $expenses->firstItem() ?? 0 }} a {{ $expenses->lastItem() ?? 0 }} 
                        de {{ $expenses->total() }} resultados
                    </small>
                </div>
                <nav>
                    {{ $expenses->appends(request()->query())->links('pagination::bootstrap-5') }}
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
        .info-box {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .info-box-icon {
            border-radius: 10px 0 0 10px;
        }
        .btn-group .btn {
            border-radius: 0;
        }
        .btn-group .btn:first-child {
            border-radius: 5px 0 0 5px;
        }
        .btn-group .btn:last-child {
            border-radius: 0 5px 5px 0;
        }
        .badge {
            font-size: 0.75rem;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Limpar pesquisa
            $('#clear-search').click(function() {
                $('#search').val('');
                $('#filter-form').submit();
            });

            // Auto-submit no change dos filtros de data
            $('#date_from, #date_to').on('change', function() {
                $('#filter-form').submit();
            });

            // Auto-fechar alertas
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);

            // Adicionar tooltips
            $('[title]').tooltip();
        });
    </script>
@stop