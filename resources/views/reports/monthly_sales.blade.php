@extends('layouts.app')

@section('title', 'Vendas Mensais')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Relatório de Vendas Mensais</h1>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Resumo -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $sales->count() }}</h3>
                    <p>Meses Analisados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>MT {{ number_format($sales->sum('total'), 2, ',', '.') }}</h3>
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
                    <h3>MT {{ number_format($sales->avg('total'), 2, ',', '.') }}</h3>
                    <p>Média Mensal</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>MT {{ number_format($sales->max('total'), 2, ',', '.') }}</h3>
                    <p>Melhor Mês</p>
                </div>
                <div class="icon">
                    <i class="fas fa-trophy"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Vendas Mensais -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line"></i> Evolução das Vendas Mensais</h3>
                </div>
                <div class="card-body">
                    <canvas id="monthlySalesChart" width="400" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tabela de Vendas Mensais -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-table"></i> Detalhes Mensais</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="monthly-table">
                            <thead>
                                <tr>
                                    <th>Mês/Ano</th>
                                    <th class="text-right">Receita</th>
                                    <th class="text-center">Variação</th>
                                    <th class="text-center">% do Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $index => $sale)
                                    @php
                                        $previousSale = $sales->get($index + 1);
                                        $variation = $previousSale ? (($sale->total - $previousSale->total) / $previousSale->total * 100) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ \Carbon\Carbon::parse($sale->month . '-01')->format('M/Y') }}</strong>
                                        </td>
                                        <td class="text-right">MT {{ number_format($sale->total, 2, ',', '.') }}</td>
                                        <td class="text-center">
                                            @if($variation > 0)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-arrow-up"></i> {{ number_format($variation, 1) }}%
                                                </span>
                                            @elseif($variation < 0)
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-arrow-down"></i> {{ number_format(abs($variation), 1) }}%
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ number_format(($sale->total / $sales->sum('total')) * 100, 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Análise Estatística -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calculator"></i> Análise Estatística</h3>
                </div>
                <div class="card-body">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-chart-bar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Média Mensal</span>
                            <span class="info-box-number">MT {{ number_format($sales->avg('total'), 2, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-trophy"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Melhor Mês</span>
                            <span class="info-box-number">MT {{ number_format($sales->max('total'), 2, ',', '.') }}</span>
                            <span class="progress-description">
                                {{ \Carbon\Carbon::parse($sales->where('total', $sales->max('total'))->first()->month . '-01')->format('M/Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-chart-line"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pior Mês</span>
                            <span class="info-box-number">MT {{ number_format($sales->min('total'), 2, ',', '.') }}</span>
                            <span class="progress-description">
                                {{ \Carbon\Carbon::parse($sales->where('total', $sales->min('total'))->first()->month . '-01')->format('M/Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlySalesChart').getContext('2d');
    const labels = {!! json_encode($sales->pluck('month')->map(fn($m) => \Carbon\Carbon::parse($m . '-01')->format('M/Y'))) !!};
    const data = {!! json_encode($sales->pluck('total')) !!};

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Receita Mensal',
                data: data,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0,123,255,0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: false }
            }
        }
    });
});
</script>
@stop