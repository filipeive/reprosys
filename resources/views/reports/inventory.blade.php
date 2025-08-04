{{-- resources/views/reports/inventory.blade.php --}}
@extends('adminlte::page')

@section('title', 'Relatório de Inventário')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Relatório de Inventário</h1>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Cards de Resumo -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $products->count() }}</h3>
                    <p>Total de Produtos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $products->where('stock_quantity', '>', 0)->count() }}</h3>
                    <p>Em Stock</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $products->filter(function($p) { return $p->stock_quantity > 0 && $p->stock_quantity <= $p->min_stock_level; })->count() }}</h3>
                    <p>Stock Baixo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $products->where('stock_quantity', '<=', 0)->count() }}</h3>
                    <p>Esgotados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Filtros -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter"></i> Filtros</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.inventory') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status do Stock</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Todos</option>
                                        <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>Em Stock</option>
                                        <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Stock Baixo</option>
                                        <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Esgotado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category">Categoria</label>
                                    <select class="form-control" id="category" name="category">
                                        <option value="">Todas</option>
                                        @foreach($products->pluck('category.name')->unique()->filter() as $category)
                                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
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
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-success btn-block" onclick="exportInventory()">
                                        <i class="fas fa-file-excel"></i> Exportar
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-info btn-block" onclick="printInventory()">
                                        <i class="fas fa-print"></i> Imprimir
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
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-warehouse"></i> Inventário de Produtos</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="inventory-table">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Categoria</th>
                                    <th>Código</th>
                                    <th class="text-center">Stock Atual</th>
                                    <th class="text-center">Stock Mínimo</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-right">Preço Compra</th>
                                    <th class="text-right">Preço Venda</th>
                                    <th class="text-right">Valor Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category->name ?? 'Sem categoria' }}</td>
                                        <td>{{ $product->code ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $product->stock_quantity }}</td>
                                        <td class="text-center">{{ $product->min_stock_level }}</td>
                                        <td class="text-center">
                                            @if($product->stock_quantity <= 0)
                                                <span class="badge badge-danger">Esgotado</span>
                                            @elseif($product->stock_quantity <= $product->min_stock_level)
                                                <span class="badge badge-warning">Baixo</span>
                                            @else
                                                <span class="badge badge-success">OK</span>
                                            @endif
                                        </td>
                                        <td class="text-right">MT {{ number_format($product->purchase_price, 2, ',', '.') }}</td>
                                        <td class="text-right">MT {{ number_format($product->selling_price, 2, ',', '.') }}</td>
                                        <td class="text-right">MT {{ number_format($product->stock_quantity * $product->purchase_price, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="8" class="text-right">Total Valor do Stock:</th>
                                    <th class="text-right">
                                        MT {{ number_format($products->sum(function($p) { return $p->stock_quantity * $p->purchase_price; }), 2, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Produtos com Stock Baixo -->
    @if($products->filter(function($p) { return $p->stock_quantity > 0 && $p->stock_quantity <= $p->min_stock_level; })->count() > 0)
        <div class="row">
            <div class="col-md-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Produtos com Stock Baixo</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th class="text-center">Stock Atual</th>
                                        <th class="text-center">Stock Mínimo</th>
                                        <th class="text-center">Repor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products->filter(function($p) { return $p->stock_quantity > 0 && $p->stock_quantity <= $p->min_stock_level; }) as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td class="text-center">{{ $product->stock_quantity }}</td>
                                            <td class="text-center">{{ $product->min_stock_level }}</td>
                                            <td class="text-center">{{ $product->min_stock_level - $product->stock_quantity + 10 }}</td>
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
    <script>
        $(document).ready(function() {
            $('#inventory-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
                },
                "pageLength": 25,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "type": "num", "targets": [3, 4] },
                    { "orderable": false, "targets": 5 }
                ]
            });
        });

        function exportInventory() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'excel');
            window.open('{{ route("reports.inventory") }}?' + params.toString(), '_blank');
        }

        function printInventory() {
            window.print();
        }
    </script>
@stop