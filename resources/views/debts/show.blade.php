@extends('layouts.app')

@section('title', "Dívida #{$debt->id}")
@section('page-title', "Dívida #{$debt->id}")
@section('title-icon', 'fa-credit-card')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('debts.index') }}">Dívidas</a></li>
    <li class="breadcrumb-item active">Detalhes</li>
@endsection

@section('content')
    <div class="row">
        <!-- Informações da Dívida -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i> Detalhes da Dívida</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Cliente:</strong> {{ $debt->customer_name }}</p>
                            <p><strong>Telefone:</strong> {{ $debt->customer_phone ?? 'N/A' }}</p>
                            <p><strong>Data:</strong> {{ $debt->debt_date->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> <span class="badge {{ $debt->status_badge }}">{{ $debt->status_text }}</span></p>
                            <p><strong>Vencimento:</strong> {{ $debt->due_date?->format('d/m/Y') ?? 'N/A' }}</p>
                            @if($debt->is_overdue)
                                <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Atraso de {{ $debt->days_overdue }} dias</p>
                            @endif
                        </div>
                    </div>
                    <p><strong>Descrição:</strong> {{ $debt->description }}</p>
                    @if($debt->notes)
                        <p><strong>Observações:</strong> {{ $debt->notes }}</p>
                    @endif
                </div>
            </div>

            <!-- Itens da Venda -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i> Itens da Venda #{{ $debt->sale->id }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto/Serviço</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Preço Unitário</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($debt->sale->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">MT {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                        <td class="text-end">MT {{ number_format($item->total_price, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr class="fw-bold">
                                    <td colspan="3" class="text-end">Total:</td>
                                    <td class="text-end">MT {{ number_format($debt->original_amount, 2, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagamentos -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill me-2"></i> Pagamentos</h5>
                </div>
                <div class="card-body">
                    <p><strong>Total Pago:</strong> MT {{ number_format($debt->paid_amount, 2, ',', '.') }}</p>
                    <p><strong>Restante:</strong> MT {{ number_format($debt->remaining_amount, 2, ',', '.') }}</p>
                    
                    @if($debt->canAddPayment())
                        <button type="button" class="btn btn-success w-100" 
                                onclick="openPaymentOffcanvas({{ $debt->id }}, '{{ $debt->customer_name }}', {{ $debt->remaining_amount }})">
                            <i class="fas fa-plus me-2"></i> Registrar Pagamento
                        </button>
                    @endif
                </div>
            </div>

            <!-- Lista de Pagamentos -->
            @if($debt->payments->isNotEmpty())
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Histórico de Pagamentos</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($debt->payments as $payment)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>MT {{ number_format($payment->amount, 2, ',', '.') }}</strong>
                                            <small class="d-block text-muted">{{ $payment->payment_date->format('d/m/Y') }}</small>
                                        </div>
                                        <span class="badge {{ $payment->payment_method_badge }}">
                                            {{ $payment->payment_method_text }}
                                        </span>
                                    </div>
                                    @if($payment->notes)
                                        <small class="text-muted mt-1 d-block">{{ $payment->notes }}</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection