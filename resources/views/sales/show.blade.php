@extends('layouts.app')

@section('title', 'Detalhes da Venda #' . $sale->id . ' - Sistema Reprografia')
@section('page-title', 'Gestão de Vendas')
@section('title-icon', 'fa-shopping-cart')

@section('breadcrumbs')
     Editar Venda #{{ $sale->id }}
@endsection


@section('content')
    {{-- <!-- Notificações -->
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
    @endif --}}
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('sales.edit', $sale) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-2"></i> Editar
            </a>
            <a href="{{ route('sales.print', $sale) }}" class="btn btn-secondary" target="_blank">
                <i class="fas fa-print mr-2"></i> Imprimir
            </a>
            <a href="{{ route('sales.duplicate', $sale) }}" class="btn btn-info">
                <i class="fas fa-copy mr-2"></i> Duplicar
            </a>
            <button class="btn btn-danger" data-toggle="modal" data-target="#cancelModal">
                <i class="fas fa-trash mr-2"></i> Cancelar
            </button>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </a>
        </div>
    </div>
<br>
    <div class="row">
        <!-- Informações da Venda -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle mr-2"></i>Informações da Venda
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item mb-3">
                        <label class="font-weight-bold">ID da Venda:</label>
                        <div>#{{ $sale->id }}</div>
                    </div>

                    <div class="info-item mb-3">
                        <label class="font-weight-bold">Data e Hora:</label>
                        <div>{{ $sale->sale_date->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="info-item mb-3">
                        <label class="font-weight-bold">Vendedor:</label>
                        <div>{{ $sale->user->name ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item mb-3">
                        <label class="font-weight-bold">Método de Pagamento:</label>
                        <div>
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
                            @endswitch
                        </div>
                    </div>

                    <div class="info-item mb-3">
                        <label class="font-weight-bold">Total da Venda:</label>
                        <div class="h4 text-success mb-0">{{ number_format($sale->total_amount, 2, ',', '.') }} MT</div>
                    </div>

                    @if ($sale->notes)
                        <div class="info-item">
                            <label class="font-weight-bold">Observações:</label>
                            <div class="text-muted">{{ $sale->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informações do Cliente -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user mr-2"></i>Dados do Cliente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item mb-3">
                        <label class="font-weight-bold">Nome:</label>
                        <div>{{ $sale->customer_name ?: 'Cliente Avulso' }}</div>
                    </div>

                    <div class="info-item mb-3">
                        <label class="font-weight-bold">Telefone:</label>
                        <div>{{ $sale->customer_phone ?: 'Não informado' }}</div>
                    </div>

                    @if ($sale->customer_name)
                        <div class="mt-3">
                            <a href="{{ route('sales.index', ['search' => $sale->customer_name]) }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-search mr-1"></i>Ver outras vendas
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Resumo -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie mr-2"></i>Resumo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item mb-3">
                        <label class="font-weight-bold">Quantidade de Itens:</label>
                        <div>{{ $sale->items->count() }} item(s)</div>
                    </div>

                    <div class="info-item mb-3">
                        <label class="font-weight-bold">Quantidade Total:</label>
                        <div>{{ $sale->items->sum('quantity') }} unidade(s)</div>
                    </div>

                    <div class="info-item mb-3">
                        <label class="font-weight-bold">Ticket Médio:</label>
                        <div>{{ number_format($sale->items->avg('total_price'), 2, ',', '.') }} MT</div>
                    </div>

                    <div class="info-item">
                        <label class="font-weight-bold">Status:</label>
                        <div>
                            <span class="badge badge-success">
                                <i class="fas fa-check mr-1"></i>Concluída
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Itens da Venda -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-shopping-basket mr-2"></i>Itens da Venda
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Tipo</th>
                            <th class="text-center">Qtd.</th>
                            <th class="text-right">Preço Unit.</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i
                                            class="{{ $item->product->category->icon ?? 'fas fa-box' }} mr-2 text-muted"></i>
                                        <div>
                                            <strong>{{ $item->product->name }}</strong>
                                            @if ($item->product->code)
                                                <br><small class="text-muted">Código: {{ $item->product->code }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge badge-secondary">{{ $item->product->category->name ?? 'Sem categoria' }}</span>
                                </td>
                                <td>
                                    @if ($item->product->type === 'product')
                                        <span class="badge badge-primary">Produto</span>
                                    @else
                                        <span class="badge badge-info">Serviço</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light">{{ $item->quantity }}</span>
                                </td>
                                <td class="text-right">{{ number_format($item->unit_price, 2, ',', '.') }} MT</td>
                                <td class="text-right">
                                    <strong>{{ number_format($item->total_price, 2, ',', '.') }} MT</strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="thead-light">
                        <tr>
                            <th colspan="6" class="text-right">Total da Venda:</th>
                            <th class="text-right">
                                <span class="h5 text-success mb-0">{{ number_format($sale->total_amount, 2, ',', '.') }}
                                    MT</span>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Cancelamento -->
    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Cancelar Venda
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Atenção!</strong> Esta ação irá:
                    </div>
                    <ul class="mb-3">
                        <li>Cancelar definitivamente esta venda</li>
                        <li>Restaurar o stock dos produtos vendidos</li>
                        <li>Registrar os movimentos de estorno no histórico</li>
                    </ul>
                    <p>Tem certeza que deseja cancelar a venda #{{ $sale->id }}?</p>
                    <p><strong>Cliente:</strong> {{ $sale->customer_name ?: 'Cliente Avulso' }}</p>
                    <p><strong>Valor:</strong> {{ number_format($sale->total_amount, 2, ',', '.') }} MT</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Não Cancelar
                    </button>
                    <form action="{{ route('sales.destroy', $sale) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-2"></i>Sim, Cancelar Venda
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

        .modal-content {
            border-radius: 10px;
        }

        .modal-header {
            border-radius: 10px 10px 0 0;
        }

        .btn {
            border-radius: 5px;
        }

        .info-item {
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 0.5rem;
        }

        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-item label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
            display: block;
        }

        .badge {
            font-size: 0.75rem;
        }

        .table-responsive {
            border-radius: 5px;
        }

        .alert {
            border-radius: 8px;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto-fechar alertas
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);

            // Adicionar tooltips
            $('[title]').tooltip();

            // Confirmação adicional antes de cancelar
            $('#cancelModal form').on('submit', function(e) {
                const confirmed = confirm('Esta é uma ação irreversível. Tem absoluta certeza?');
                if (!confirmed) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
@stop
