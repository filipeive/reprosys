@extends('adminlte::page')

@section('title', 'Detalhes da Despesa #' . $expense->id)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-receipt text-danger"></i>
            Despesa #{{ $expense->id }}
        </h1>
        <div>
            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-2"></i> Editar
            </a>
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Notificações -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <i class="fas fa-check mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- Informações da Despesa -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle mr-2"></i>Informações da Despesa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item mb-3">
                        <label class="font-weight-bold">ID da Despesa:</label>
                        <div>#{{ $expense->id }}</div>
                    </div>
                    <div class="info-item mb-3">
                        <label class="font-weight-bold">Data:</label>
                        <div>{{ $expense->expense_date->format('d/m/Y') }}</div>
                    </div>
                    <div class="info-item mb-3">
                        <label class="font-weight-bold">Categoria:</label>
                        <div>
                            <span class="badge badge-secondary">
                                {{ $expense->category->name ?? '-' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-item mb-3">
                        <label class="font-weight-bold">Valor:</label>
                        <div class="h4 text-danger mb-0">{{ number_format($expense->amount, 2, ',', '.') }} MT</div>
                    </div>
                    <div class="info-item mb-3">
                        <label class="font-weight-bold">Usuário:</label>
                        <div>{{ $expense->user->name ?? '-' }}</div>
                    </div>
                    <div class="info-item mb-3">
                        <label class="font-weight-bold">Número do Recibo:</label>
                        <div>{{ $expense->receipt_number ?? '-' }}</div>
                    </div>
                    @if($expense->notes)
                        <div class="info-item">
                            <label class="font-weight-bold">Observações:</label>
                            <div class="text-muted">{{ $expense->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Descrição -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-align-left mr-2"></i>Descrição da Despesa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item mb-3">
                        <label class="font-weight-bold">Descrição:</label>
                        <div>{{ $expense->description }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            border-radius: 10px 10px 0 0;
        }
        .info-item {
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 0.5rem;
        }
        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .info-item label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
            display: block;
        }
        .badge {
            font-size: 0.85rem;
        }
        .alert {
            border-radius: 8px;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
            $('[title]').tooltip();
        });
    </script>
@stop