@extends('adminlte::page')

@section('title', 'Detalhes do Movimento')

@section('content_header')
    <h1>Detalhes do Movimento</h1>
    <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary">Voltar</a>
@endsection

@section('content')
    <div class="card mt-3">
        <div class="card-header">Informações</div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Produto</dt>
                <dd class="col-sm-9">{{ $stockMovement->product->name ?? '-' }}</dd>

                <dt class="col-sm-3">Tipo</dt>
                <dd class="col-sm-9">{{ ucfirst($stockMovement->movement_type) }}</dd>

                <dt class="col-sm-3">Quantidade</dt>
                <dd class="col-sm-9">{{ $stockMovement->quantity }}</dd>

                <dt class="col-sm-3">Data</dt>
                <dd class="col-sm-9">{{ $stockMovement->movement_date->format('d/m/Y') }}</dd>

                <dt class="col-sm-3">Motivo</dt>
                <dd class="col-sm-9">{{ $stockMovement->reason ?? '-' }}</dd>

                <dt class="col-sm-3">Usuário</dt>
                <dd class="col-sm-9">{{ $stockMovement->user->name ?? '-' }}</dd>
            </dl>
        </div>
    </div>
@endsection