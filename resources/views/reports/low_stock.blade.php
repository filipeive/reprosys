{{-- resources/views/reports/low_stock.blade.php --}}
@extends('adminlte::page')

@section('title', 'Produtos com Stock Baixo')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Produtos com Stock Baixo</h1>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Alertas -->
        @if($products->count() > 0)
            <div class="col-md-12 mb-3">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Atenção!</strong> Foram encontrados {{ $products->count() }} produtos com stock baixo ou esgotado.
                </div>
            </div>
        @else
            <div class="col-md-12 mb-3">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <strong>Parabéns!</strong> Todos os produtos estão com stock adequado.
                </div>
            </div>
        @endif
    </div>

    <div class="row">
        <!-- Resumo -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $products->where('stock_quantity', 0)->count() }}</h3>
                    <p>Produtos Esgotados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $products->where('stock_quantity', '>', 0)->count() }}</h3>
                    <p>Stock Baixo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $products->count() }}</h3>
                    <p>Total Produtos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>MT {{ number_format($products->sum(function($p) { return $p->stock_quantity * $p->purchase_price; }), 2, ',', '.') }}</h3>
                    <p>Valor em Stock</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>

    @if($products->count() > 0)
        <div class="row">
            <!-- Tabela de Produtos -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-boxes"></i> Produtos com Stock Baixo</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-success btn-sm" onclick="exportReport()">
                                <i class="fas fa-file-pdf"></i> Exportar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="products-table">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Categoria</th>
                                        <th class="text-center">Stock Atual</th>
                                        <th class="text-center">Stock Mínimo</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-right">Preço Compra</th>
                                        <th class="text-right">Preço Venda</th>
                                        <th class="text-right">Valor Stock</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr class="{{ $product->stock_quantity <= 0 ? 'table-danger' : 'table-warning' }}">
                                            <td>
                                                <strong>{{ $product->name }}</strong>
                                                @if($product->description)
                                                    <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-{{ $product->stock_quantity <= 0 ? 'danger' : 'warning' }}">
                                                    {{ $product->stock_quantity }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $product->min_stock_level }}</td>
                                            <td class="text-center">
                                                @if($product->stock_quantity <= 0)
                                                    <span class="badge badge-danger">Esgotado</span>
                                                @else
                                                    <span class="badge badge-warning">Baixo</span>
                                                @endif
                                            </td>
                                            <td class="text-right">MT {{ number_format($product->purchase_price, 2, ',', '.') }}</td>
                                            <td class="text-right">MT {{ number_format($product->selling_price, 2, ',', '.') }}</td>
                                            <td class="text-right">MT {{ number_format($product->stock_quantity * $product->purchase_price, 2, ',', '.') }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info" title="Ver Produto">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning" title="Editar Stock">
                                                    <i class="fas fa-edit"></i>
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

        <!-- Gráfico de Status -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> Status do Stock</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-bar"></i> Produtos Mais Críticos</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="criticalChart" width="400" height="200"></canvas>
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
                "order": [[ 2, "asc" ]],
                "columnDefs": [
                    { "orderable": false, "targets": -1 }
                ]
            });

            @if($products->count() > 0)
                // Gráfico de Status
                const statusCtx = document.getElementById('statusChart').getContext('2d');
                const esgotados = {{ $products->where('stock_quantity', 0)->count() }};
                const baixo = {{ $products->where('stock_quantity', '>', 0)->count() }};
                
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Esgotados', 'Stock Baixo'],
                        datasets: [{
                            data: [esgotados, baixo],
                            backgroundColor: ['#dc3545', '#ffc107']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Distribuição por Status'
                            }
                        }
                    }
                });

                // Gráfico de Produtos Mais Críticos
                const criticalCtx = document.getElementById('criticalChart').getContext('2d');
                const criticalProducts = {!! json_encode($products->take(10)->pluck('name')) !!};
                const criticalStock = {!! json_encode($products->take(10)->pluck('stock_quantity')) !!};
                
                new Chart(criticalCtx, {
                    type: 'bar',
                    data: {
                        labels: criticalProducts,
                        datasets: [{
                            label: 'Stock Atual',
                            data: criticalStock,
                            backgroundColor: 'rgba(220, 53, 69, 0.5)',
                            borderColor: 'rgba(220, 53, 69, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Top 10 Produtos Críticos'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            @endif
        });

        function exportReport() {
            const params = new URLSearchParams();
            params.set('export', 'pdf');
            params.set('type', 'low-stock');
            
            window.open('{{ route("reports.export") }}?' + params.toString(), '_blank');
        }
    </script>
@stop