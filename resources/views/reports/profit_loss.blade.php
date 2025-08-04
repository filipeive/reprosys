@extends('adminlte::page')

@section('title', 'Relatório de Lucros e Perdas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Relatório de Lucros e Perdas</h1>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header"><i class="fas fa-filter"></i> Período</div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.profit-loss') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
                            </div>
                            <div class="col-md-4">
                                <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-success btn-block" onclick="window.print()">
                                    <i class="fas fa-file-pdf"></i> Exportar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Demonstração de Resultados -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><i class="fas fa-chart-line"></i> Demonstração de Resultados</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tbody>
                            <tr class="table-success">
                                <td><strong>RECEITAS</strong></td>
                                <td class="text-right"><strong>MT {{ number_format($revenue, 2, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <td class="pl-4">Vendas de Produtos</td>
                                <td class="text-right">MT {{ number_format($revenue, 2, ',', '.') }}</td>
                            </tr>
                            <tr class="table-warning">
                                <td><strong>CUSTO DOS PRODUTOS VENDIDOS</strong></td>
                                <td class="text-right"><strong>MT {{ number_format($costOfGoodsSold, 2, ',', '.') }}</strong></td>
                            </tr>
                            <tr class="table-info">
                                <td><strong>LUCRO BRUTO</strong></td>
                                <td class="text-right"><strong>MT {{ number_format($revenue - $costOfGoodsSold, 2, ',', '.') }}</strong></td>
                            </tr>
                            <tr class="table-danger">
                                <td><strong>DESPESAS OPERACIONAIS</strong></td>
                                <td class="text-right"><strong>MT {{ number_format($totalExpenses, 2, ',', '.') }}</strong></td>
                            </tr>
                            <tr class="{{ $profit >= 0 ? 'table-success' : 'table-danger' }}">
                                <td><strong>{{ $profit >= 0 ? 'LUCRO' : 'PREJUÍZO' }} LÍQUIDO</strong></td>
                                <td class="text-right"><strong>MT {{ number_format($profit, 2, ',', '.') }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Métricas -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><i class="fas fa-calculator"></i> Métricas</div>
                <div class="card-body">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-percentage"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Margem Bruta</span>
                            <span class="info-box-number">{{ $revenue > 0 ? number_format((($revenue - $costOfGoodsSold) / $revenue) * 100, 1) : 0 }}%</span>
                        </div>
                    </div>
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-chart-pie"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Margem Líquida</span>
                            <span class="info-box-number">{{ $revenue > 0 ? number_format(($profit / $revenue) * 100, 1) : 0 }}%</span>
                        </div>
                    </div>
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-coins"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">ROI</span>
                            <span class="info-box-number">{{ $costOfGoodsSold > 0 ? number_format(($profit / $costOfGoodsSold) * 100, 1) : 0 }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop