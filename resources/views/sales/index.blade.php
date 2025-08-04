@extends('adminlte::page')

@section('title', 'Gestão de Vendas - Sistema Reprografia')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-shopping-cart text-primary"></i>
            Gestão de Vendas
        </h1>
        <div>
            <a href="{{ route('sales.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i> Nova Venda
            </a>
            <a href="{{ route('sales.manual-create') }}" class="btn btn-success ml-2">
                <i class="fas fa-edit mr-2"></i> Venda Manual
            </a>
        </div>
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
            <form method="GET" action="{{ route('sales.index') }}" id="filter-form">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Pesquisar Cliente</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Nome ou telefone do cliente...">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="clear-search">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
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
                            <label for="payment_method">Pagamento</label>
                            <select class="form-control" id="payment_method" name="payment_method">
                                <option value="">Todos</option>
                                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Dinheiro</option>
                                <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Cartão</option>
                                <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transferência</option>
                                <option value="credit" {{ request('payment_method') == 'credit' ? 'selected' : '' }}>Crédito</option>
                            </select>
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

    <!-- Resumo -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-shopping-cart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total de Vendas</span>
                    <span class="info-box-number">{{ $sales->total() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Valor Total</span>
                    <span class="info-box-number">{{ number_format($sales->sum('total_amount'), 2, ',', '.') }} MT</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-calendar-day"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Vendas Hoje</span>
                    <span class="info-box-number">{{ $sales->where('sale_date', now()->toDateString())->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Média por Venda</span>
                    <span class="info-box-number">{{ $sales->count() > 0 ? number_format($sales->avg('total_amount'), 2, ',', '.') : '0,00' }} MT</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Vendas -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list mr-2"></i> Vendas Registradas
            </h5>
            <div class="card-tools">
                <span class="badge badge-info">Total: {{ $sales->total() }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="sales-table">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Cliente</th>
                            <th>Telefone</th>
                            <th width="120">Data</th>
                            <th width="80">Itens</th>
                            <th width="100">Pagamento</th>
                            <th width="120">Total</th>
                            <th width="100">Vendedor</th>
                            <th width="180">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>{{ $sale->id }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $sale->customer_name ?: 'Cliente Avulso' }}</strong>
                                        @if($sale->notes)
                                            <br><small class="text-muted">{{ Str::limit($sale->notes, 30) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $sale->customer_phone ?: '-' }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $sale->sale_date->format('d/m/Y') }}</strong>
                                        <br><small class="text-muted">{{ $sale->sale_date->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light">{{ $sale->items->count() }}</span>
                                </td>
                                <td>
                                    @switch($sale->payment_method)
                                        @case('cash')
                                            <span class="badge badge-success"><i class="fas fa-money-bill mr-1"></i>Dinheiro</span>
                                            @break
                                        @case('card')
                                            <span class="badge badge-primary"><i class="fas fa-credit-card mr-1"></i>Cartão</span>
                                            @break
                                        @case('transfer')
                                            <span class="badge badge-info"><i class="fas fa-exchange-alt mr-1"></i>Transferência</span>
                                            @break
                                        @case('credit')
                                            <span class="badge badge-warning"><i class="fas fa-clock mr-1"></i>Crédito</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary">{{ $sale->payment_method }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <strong class="text-success">{{ number_format($sale->total_amount, 2, ',', '.') }} MT</strong>
                                </td>
                                <td>
                                    <small>{{ $sale->user->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('sales.show', $sale->id) }}" 
                                           class="btn btn-sm btn-info" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('sales.edit', $sale->id) }}" 
                                           class="btn btn-sm btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('sales.print', $sale->id) }}" 
                                           class="btn btn-sm btn-secondary" title="Imprimir" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger delete-btn" 
                                                data-id="{{ $sale->id }}" 
                                                data-customer="{{ $sale->customer_name ?: 'Cliente Avulso' }}" 
                                                title="Cancelar Venda">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="no-data">
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <br>Nenhuma venda encontrada
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
                        Mostrando {{ $sales->firstItem() ?? 0 }} a {{ $sales->lastItem() ?? 0 }} 
                        de {{ $sales->total() }} resultados
                    </small>
                </div>
                <nav>
                    {{ $sales->appends(request()->query())->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-trash mr-2"></i>Confirmar Cancelamento
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja cancelar esta venda?</p>
                    <p><strong>Cliente: <span id="delete-customer-name"></span></strong></p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <small>O stock dos produtos será restaurado automaticamente.</small>
                    </div>
                    <small class="text-muted">Esta ação não pode ser desfeita.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <form id="delete-form" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-2"></i>Cancelar Venda
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalhes Rápidos -->
    <div class="modal fade" id="quickViewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-eye mr-2"></i>Detalhes da Venda
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="quick-view-content">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Carregando...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <a href="#" id="view-full-sale" class="btn btn-primary">
                        <i class="fas fa-external-link-alt mr-2"></i>Ver Completo
                    </a>
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

        .quick-view-item {
            border-bottom: 1px solid #dee2e6;
            padding: 0.5rem 0;
        }

        .quick-view-item:last-child {
            border-bottom: none;
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

            // Auto-submit no change dos filtros de data e pagamento
            $('#date_from, #date_to, #payment_method').on('change', function() {
                $('#filter-form').submit();
            });

            // Excluir venda
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                const customer = $(this).data('customer');

                $('#delete-customer-name').text(customer);
                $('#delete-form').attr('action', `/sales/${id}`);
                $('#deleteModal').modal('show');
            });

            // Visualização rápida (opcional - pode implementar com AJAX)
            $(document).on('click', '.quick-view-btn', function() {
                const saleId = $(this).data('id');
                
                $('#quick-view-content').html(`
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Carregando...</p>
                    </div>
                `);
                
                $('#view-full-sale').attr('href', `/sales/${saleId}`);
                $('#quickViewModal').modal('show');
                
                // Aqui você pode fazer uma requisição AJAX para carregar os detalhes
                // $.get(`/sales/${saleId}/quick-view`, function(data) {
                //     $('#quick-view-content').html(data);
                // });
            });

            // Auto-fechar alertas
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);

            // Função para formatar valores monetários
            function formatMoney(value) {
                return new Intl.NumberFormat('pt-MZ', {
                    style: 'currency',
                    currency: 'MZN'
                }).format(value);
            }

            // Adicionar tooltips
            $('[title]').tooltip();

            // Confirmar exclusão via formulário normal
            $('.delete-btn').on('click', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                const customer = $(this).data('customer');
                
                if (confirm(`Tem certeza que deseja cancelar a venda para ${customer}?\n\nO stock será restaurado automaticamente.`)) {
                    form.submit();
                }
            });
        });
    </script>
@stop