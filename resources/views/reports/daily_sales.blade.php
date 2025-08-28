@extends('layouts.app')

@section('title', 'Vendas Diárias')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Relatório de Vendas Diárias</h1>
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
                    <h3 class="card-title"><i class="fas fa-filter"></i> Filtros</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.daily-sales') }}">
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
                                    <button type="button" class="btn btn-success btn-block" onclick="exportData()">
                                        <i class="fas fa-file-excel"></i> Exportar
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
                    <p>Dias com Vendas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>MT {{ number_format($sales->sum('total'), 2, ',', '.') }}</h3>
                    <p>Total Vendido</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>MT {{ $sales->count() > 0 ? number_format($sales->avg('total'), 2, ',', '.') : '0,00' }}</h3>
                    <p>Média Diária</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>MT {{ $sales->count() > 0 ? number_format($sales->max('total'), 2, ',', '.') : '0,00' }}</h3>
                    <p>Melhor Dia</p>
                </div>
                <div class="icon">
                    <i class="fas fa-trophy"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-table"></i> Vendas por Dia</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="daily-sales-table">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Dia da Semana</th>
                                    <th class="text-right">Total Vendido</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($sale->date)->locale('pt_BR')->dayName }}</td>
                                        <td class="text-right">MT {{ number_format($sale->total, 2, ',', '.') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('reports.index', ['date_from' => $sale->date, 'date_to' => $sale->date, 'report_type' => 'sales']) }}" 
                                               class="btn btn-sm btn-info" title="Ver detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Nenhuma venda encontrada no período</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-area"></i> Evolução das Vendas</h3>
                </div>
                <div class="card-body">
                    <canvas id="dailySalesChart" width="400" height="100"></canvas>
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
            // DataTable
            $('#daily-sales-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
                },
                "pageLength": 25,
                "order": [[ 0, "desc" ]]
            });

            // Gráfico
            const ctx = document.getElementById('dailySalesChart').getContext('2d');
            const labels = {!! json_encode($sales->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m'); })) !!};
            const data = {!! json_encode($sales->pluck('total')) !!};

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Vendas Diárias (MT)',
                        data: data,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        fill: true,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Vendas Diárias'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'MT ' + value.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        });

        function exportData() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'excel');
            window.open('{{ route("reports.daily-sales") }}?' + params.toString(), '_blank');
        }
    </script>
@stop