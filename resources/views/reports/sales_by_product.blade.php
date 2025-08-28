@extends('layouts.app')

@section('title', 'Vendas por Produto')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Vendas por Produto</h1>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Filtros -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter"></i> Período</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.sales-by-product') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date_from">Data Inicial</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" 
                                           value="{{ $dateFrom }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date_to">Data Final</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" 
                                           value="{{ $dateTo }}">
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
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-success btn-block" onclick="exportReport()">
                                        <i class="fas fa-file-pdf"></i> Exportar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Resumo -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $sales->count() }}</h3>
                    <p>Produtos Vendidos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($sales->sum('quantity_sold')) }}</h3>
                    <p>Qtd Total Vendida</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>MT {{ number_format($sales->sum('total_revenue'), 2, ',', '.') }}</h3>
                    <p>Receita Total</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>MT {{ number_format($sales->avg('total_revenue'), 2, ',', '.') }}</h3>
                    <p>Receita Média</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Vendas por Produto -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Top 10 Produtos</h3>
                </div>
                <div class="card-body">
                    <canvas id="productChart" width="400" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tabela de Produtos -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-table"></i> Detalhes por Produto</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="products-table">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Qtd Vendida</th>
                                    <th class="text-right">Receita Total</th>
                                    <th class="text-right">Receita Média</th>
                                    <th class="text-center">% do Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->name }}</td>
                                        <td class="text-center">{{ number_format($sale->quantity_sold) }}</td>
                                        <td class="text-right">MT {{ number_format($sale->total_revenue, 2, ',', '.') }}</td>
                                        <td class="text-right">MT {{ number_format($sale->total_revenue / $sale->quantity_sold, 2, ',', '.') }}</td>
                                        <td class="text-center">
                                            {{ number_format(($sale->total_revenue / $sales->sum('total_revenue')) * 100, 1) }}%
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
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.css">
    <style>
        .small-box {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        $(document).ready(function() {
            // Configurar DataTable
            $('#products-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
                },
                "pageLength": 25,
                "order": [[ 2, "desc" ]],
                "columnDefs": [
                    { "type": "num", "targets": [1, 2, 3, 4] }
                ]
            });

            // Gráfico de Produtos
            const ctx = document.getElementById('productChart').getContext('2d');
            const topProducts = {!! json_encode($sales->take(10)->pluck('name')) !!};
            const topProductsData = {!! json_encode($sales->take(10)->pluck('total_revenue')) !!};
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: topProducts,
                    datasets: [{
                        label: 'Receita (MT)',
                        data: topProductsData,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Top 10 Produtos por Receita'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });

        function exportReport() {
            const params = new URLSearchParams();
            params.set('date_from', document.getElementById('date_from').value);
            params.set('date_to', document.getElementById('date_to').value);
            params.set('export', 'pdf');
            params.set('type', 'sales-by-product');
            
            window.open('{{ route("reports.export") }}?' + params.toString(), '_blank');
        }
    </script>
@stop