@extends('adminlte::page')

@section('title', 'Relatórios')

@section('content_header')
    <h1 class="mb-2">Relatórios</h1>
    <div class="mb-3">
        <a href="{{ route('reports.daily-sales') }}" class="btn btn-primary mr-2" target="_blank">
            <i class="fas fa-calendar-day"></i> Vendas Diárias
        </a>
        <a href="{{ route('reports.monthly-sales') }}" class="btn btn-info mr-2" target="_blank">
            <i class="fas fa-calendar-alt"></i> Vendas Mensais
        </a>
        <a href="{{ route('reports.sales-by-product') }}" class="btn btn-success mr-2" target="_blank">
            <i class="fas fa-box"></i> Vendas por Produto
        </a>
        <a href="{{ route('reports.profit-loss') }}" class="btn btn-warning mr-2" target="_blank">
            <i class="fas fa-chart-line"></i> Lucros e Perdas
        </a>
        <a href="{{ route('reports.low-stock') }}" class="btn btn-danger mr-2" target="_blank">
            <i class="fas fa-exclamation-triangle"></i> Baixo Estoque
        </a>
        <a href="{{ route('reports.inventory') }}" class="btn btn-secondary mr-2" target="_blank">
            <i class="fas fa-boxes"></i> Inventário
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Filtros -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter"></i> Filtros</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_from">Data Inicial</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from"
                                        value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_to">Data Final</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to"
                                        value="{{ request('date_to', now()->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="report_type">Tipo</label>
                                    <select class="form-control" id="report_type" name="report_type">
                                        <option value="all" {{ request('report_type') == 'all' ? 'selected' : '' }}>Todos
                                        </option>
                                        <option value="sales" {{ request('report_type') == 'sales' ? 'selected' : '' }}>
                                            Vendas</option>
                                        <option value="expenses"
                                            {{ request('report_type') == 'expenses' ? 'selected' : '' }}>Despesas</option>
                                        <option value="products"
                                            {{ request('report_type') == 'products' ? 'selected' : '' }}>Produtos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> Filtrar
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <a href="{{ route('reports.export', array_merge(request()->all(), ['format' => 'pdf'])) }}"
                                        class="btn btn-danger mr-2" target="_blank">
                                        <i class="fas fa-file-pdf"></i> Baixar PDF
                                    </a>
                                    <a href="{{ route('reports.export', array_merge(request()->all(), ['format' => 'excel'])) }}"
                                        class="btn btn-success mr-2" target="_blank">
                                        <i class="fas fa-file-excel"></i> Baixar Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Cards de Resumo -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalSales }}</h3>
                    <p>Total de Vendas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>MT {{ number_format($totalRevenue, 2, ',', '.') }}</h3>
                    <p>Receita Total</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>MT {{ number_format($totalExpenses, 2, ',', '.') }}</h3>
                    <p>Despesas Totais</p>
                </div>
                <div class="icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-{{ $totalRevenue - $totalExpenses >= 0 ? 'success' : 'danger' }}">
                <div class="inner">
                    <h3>MT {{ number_format($totalRevenue - $totalExpenses, 2, ',', '.') }}</h3>
                    <p>Lucro/Prejuízo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Vendas -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-area"></i> Vendas por Período</h3>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Métodos de Pagamento -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie"></i> Métodos de Pagamento</h3>
                </div>
                <div class="card-body">
                    <canvas id="paymentMethodChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Produtos Mais Vendidos -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-trophy"></i> Produtos Mais Vendidos</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Qtd Vendida</th>
                                    <th class="text-right">Receita</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topProducts as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-center">{{ $product->total_quantity }}</td>
                                        <td class="text-right">MT
                                            {{ number_format($product->total_revenue, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendas Recentes -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock"></i> Vendas Recentes</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Cliente</th>
                                    <th class="text-right">Total</th>
                                    <th>Pagamento</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentSales as $sale)
                                    <tr>
                                        <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                                        <td>{{ $sale->customer_name ?? 'N/A' }}</td>
                                        <td class="text-right">MT {{ number_format($sale->total_amount, 2, ',', '.') }}
                                        </td>
                                        <td>
                                            @switch($sale->payment_method)
                                                @case('cash')
                                                    <span class="badge badge-success">Dinheiro</span>
                                                @break

                                                @case('card')
                                                    <span class="badge badge-primary">Cartão</span>
                                                @break

                                                @case('transfer')
                                                    <span class="badge badge-info">Transferência</span>
                                                @break

                                                @case('credit')
                                                    <span class="badge badge-warning">Crédito</span>
                                                @break
                                            @endswitch
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Vendas Detalhadas -->
    @if (request('report_type') === 'sales' || request('report_type') === 'all')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-table"></i> Vendas Detalhadas</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="sales-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Data</th>
                                        <th>Cliente</th>
                                        <th>Telefone</th>
                                        <th>Itens</th>
                                        <th class="text-right">Total</th>
                                        <th>Pagamento</th>
                                        <th>Vendedor</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $sale)
                                        <tr>
                                            <td>{{ $sale->id }}</td>
                                            <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                                            <td>{{ $sale->customer_name ?? 'N/A' }}</td>
                                            <td>{{ $sale->customer_phone ?? 'N/A' }}</td>
                                            <td>{{ $sale->items->count() }}</td>
                                            <td class="text-right">MT
                                                {{ number_format($sale->total_amount, 2, ',', '.') }}</td>
                                            <td>
                                                @switch($sale->payment_method)
                                                    @case('cash')
                                                        <span class="badge badge-success">Dinheiro</span>
                                                    @break

                                                    @case('card')
                                                        <span class="badge badge-primary">Cartão</span>
                                                    @break

                                                    @case('transfer')
                                                        <span class="badge badge-info">Transferência</span>
                                                    @break

                                                    @case('credit')
                                                        <span class="badge badge-warning">Crédito</span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>{{ $sale->user->name }}</td>
                                            <td>
                                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('sales.print', $sale) }}"
                                                    class="btn btn-sm btn-secondary" target="_blank">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabela de Despesas -->
    @if (request('report_type') === 'expenses' || request('report_type') === 'all')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-receipt"></i> Despesas</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="expenses-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Data</th>
                                        <th>Categoria</th>
                                        <th>Descrição</th>
                                        <th class="text-right">Valor</th>
                                        <th>Recibo</th>
                                        <th>Usuário</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenses as $expense)
                                        <tr>
                                            <td>{{ $expense->id }}</td>
                                            <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                                            <td>{{ $expense->category->name ?? '-' }}</td>
                                            <td>{{ Str::limit($expense->description, 50) }}</td>
                                            <td class="text-right">MT {{ number_format($expense->amount, 2, ',', '.') }}
                                            </td>
                                            <td>{{ $expense->receipt_number ?? 'N/A' }}</td>
                                            <td>{{ $expense->user->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Relatório de Stock -->
    @if (request('report_type') === 'products' || request('report_type') === 'all')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-boxes"></i> Relatório de Stock</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="products-table">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Categoria</th>
                                        <th>Tipo</th>
                                        <th class="text-center">Stock Atual</th>
                                        <th class="text-center">Stock Mínimo</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-right">Preço Compra</th>
                                        <th class="text-right">Preço Venda</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                                            <td>
                                                @if ($product->type === 'product')
                                                    <span class="badge badge-primary">Produto</span>
                                                @else
                                                    <span class="badge badge-info">Serviço</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($product->type === 'product')
                                                    {{ $product->stock_quantity }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($product->type === 'product')
                                                    {{ $product->min_stock_level }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($product->type === 'product')
                                                    @if ($product->stock_quantity <= 0)
                                                        <span class="badge badge-danger">Esgotado</span>
                                                    @elseif($product->stock_quantity <= $product->min_stock_level)
                                                        <span class="badge badge-warning">Baixo</span>
                                                    @else
                                                        <span class="badge badge-success">OK</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-right">MT
                                                {{ number_format($product->purchase_price, 2, ',', '.') }}</td>
                                            <td class="text-right">MT
                                                {{ number_format($product->selling_price, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.css">
    <style>
        .small-box {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        $(document).ready(function() {
            // Configurar DataTables
            $('#sales-table, #expenses-table, #products-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
                },
                "pageLength": 25,
                "order": [
                    [0, "desc"]
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": -1
                }]
            });

            // Gráfico de Vendas
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($salesChartLabels) !!},
                    datasets: [{
                        label: 'Vendas (MT)',
                        data: {!! json_encode($salesChartData) !!},
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Evolução das Vendas'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Gráfico de Métodos de Pagamento
            const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
            new Chart(paymentCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($paymentMethodLabels) !!},
                    datasets: [{
                        data: {!! json_encode($paymentMethodData) !!},
                        backgroundColor: [
                            '#28a745',
                            '#007bff',
                            '#17a2b8',
                            '#ffc107'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Métodos de Pagamento'
                        }
                    }
                }
            });

            // Exportar PDF
            $('#export-pdf').click(function() {
                const params = new URLSearchParams(window.location.search);
                params.set('export', 'pdf');
                window.open('{{ route('reports.export') }}?' + params.toString(), '_blank');
            });
        });
    </script>
@stop
