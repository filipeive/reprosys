@extends('adminlte::page')

@section('title', 'Detalhes do Produto')

@section('content_header')
    <h1>Detalhes do Produto</h1>
@stop

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Nome:</strong>
                <p class="text-break">{{ $product->name }}</p>
            </div>
            <div class="col-md-6">
                <strong>Categoria:</strong>
                <p>{{ $product->category->name }}</p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <strong>Tipo:</strong>
                <p>{{ ucfirst($product->type) }}</p>
            </div>
            <div class="col-md-4">
                <strong>Preço de Venda:</strong>
                <p class="text-success font-weight-bold">MZN {{ number_format($product->selling_price, 2) }}</p>
            </div>
            <div class="col-md-4">
                <strong>Preço de Compra:</strong>
                <p class="text-muted">MZN {{ number_format($product->purchase_price, 2) }}</p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <strong>Estoque:</strong>
                <p>{{ $product->stock_quantity }} {{ $product->unit }}</p>
            </div>
            <div class="col-md-4">
                <strong>Nível Mínimo de Estoque:</strong>
                <p>{{ $product->min_stock_level }}</p>
            </div>
            <div class="col-md-4">
                <strong>Status:</strong>
                @if($product->is_active)
                    <span class="badge badge-success">Ativo</span>
                @else
                    <span class="badge badge-danger">Inativo</span>
                @endif
            </div>
        </div>

        <div class="mb-3">
            <strong>Descrição:</strong>
            <p class="text-justify">{{ $product->description ?: '-' }}</p>
        </div>

        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

@if($product->stockMovements->count() > 0)
<div class="card mt-4 shadow-sm">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title">Movimentos de Estoque</h3>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover mb-0">
            <thead class="thead-light">
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Quantidade</th>
                    <th>Motivo</th>
                    <th>Usuário</th>
                </tr>
            </thead>
            <tbody>
                @foreach($product->stockMovements as $movement)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($movement->movement_date)->format('d/m/Y') }}</td>
                        <td>
                            @switch($movement->movement_type)
                                @case('in')
                                    <span class="badge badge-success">Entrada</span>
                                    @break
                                @case('out')
                                    <span class="badge badge-danger">Saída</span>
                                    @break
                                @case('adjustment')
                                    <span class="badge badge-warning">Ajuste</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary">{{ ucfirst($movement->movement_type) }}</span>
                            @endswitch
                        </td>
                        <td>{{ $movement->quantity }}</td>
                        <td>{{ $movement->reason ?: '-' }}</td>
                        <td>{{ $movement->user->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .card.shadow-sm {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .text-break {
            word-break: break-word;
        }
        .text-justify {
            text-align: justify;
        }
        .table-responsive {
            overflow-x: auto;
        }
    </style>
@stop
