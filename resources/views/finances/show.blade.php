@extends('layouts.app')

@section('title', 'Movimento Financeiro')
@section('page-title', 'Movimento Financeiro')
@section('title-icon', 'fa-wallet')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('finances.index') }}">Finanças</a></li>
    <li class="breadcrumb-item active">Movimento #{{ $transaction->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-receipt me-2 text-primary"></i>
                Detalhes do Movimento
            </h6>
            <a href="{{ route('finances.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Voltar
            </a>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">Descrição</small>
                    <div class="fw-semibold">{{ $transaction->description }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Data</small>
                    <div>{{ $transaction->transaction_date->format('d/m/Y') }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Valor</small>
                    <div class="fw-semibold {{ $transaction->direction === 'in' ? 'text-success' : 'text-danger' }}">
                        {{ $transaction->direction === 'in' ? '+' : '-' }} MT {{ number_format($transaction->amount, 2, ',', '.') }}
                    </div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Conta</small>
                    <div>{{ $transaction->account->name ?? '-' }}</div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Direção</small>
                    <div>{{ $transaction->direction === 'in' ? 'Entrada' : 'Saída' }}</div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Tipo</small>
                    <div>{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Responsável</small>
                    <div>{{ $transaction->user->name ?? 'Sistema' }}</div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Método</small>
                    <div>{{ $transaction->payment_method ?: '-' }}</div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Referência</small>
                    <div>{{ $transaction->reference_type ? class_basename($transaction->reference_type) . ' #' . $transaction->reference_id : '-' }}</div>
                </div>
                <div class="col-12">
                    <small class="text-muted d-block">Notas</small>
                    <div>{{ $transaction->notes ?: '-' }}</div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Criado em</small>
                    <div>{{ optional($transaction->created_at)->format('d/m/Y H:i') ?: '-' }}</div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Atualizado em</small>
                    <div>{{ optional($transaction->updated_at)->format('d/m/Y H:i') ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
