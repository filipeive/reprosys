<?php
// resources/views/sales/show.blade.php
?>
@extends('adminlte::page')

@section('title', 'Detalhes da Venda')

@section('content_header')
    <h1>Detalhes da Venda</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div class="mb-3">
            <strong>ID da Venda:</strong>
            <p>{{ $sale->id }}</p>
        </div>
        <div class="mb-3">
            <strong>Data da Venda:</strong>
            <p>{{ $sale->sale_date }}</p>
        </div>
        <div class="mb-3">
            <strong>Cliente:</strong>
            <p>{{ $sale->customer_name ?? 'N/A' }}</p>
        </div>
        <div class="mb-3">
            <strong>Telefone do Cliente:</strong>
            <p>{{ $sale->customer_phone ?? 'N/A' }}</p>
        </div>
        <div class="mb-3">
            <strong>Método de Pagamento:</strong>
            <p>{{ ucfirst($sale->payment_method) }}</p>
        </div>
        <div class="mb-3">
            <strong>Total:</strong>
            <p>${{ number_format($sale->total_amount, 2) }}</p>
        </div>
        <div class="mb-3">
            <strong>Vendedor:</strong>
            <p>{{ $sale->user->name }}</p>
        </div>
        <div class="mb-3">
            <strong>Observações:</strong>
            <p>{{ $sale->notes ?? 'N/A' }}</p>
        </div>

        <h4>Itens da Venda</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>${{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Voltar</a>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop