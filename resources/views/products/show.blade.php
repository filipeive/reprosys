@extends('layouts.app')

@section('title', 'Detalhes do Produto')
@section('title-icon', 'fa-box')
@section('page-title', 'Detalhes do Produto')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produtos</a></li>
    <li class="breadcrumb-item active">Detalhes</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm fade-in">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-box me-2 text-primary"></i>
                    Informações do Produto
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Nome do Produto</label>
                            <p class="fw-semibold mb-0">{{ $product->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Categoria</label>
                            <p class="mb-0">{{ $product->category->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Tipo</label>
                            <p class="mb-0">{{ ucfirst($product->type) }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Preço de Venda</label>
                            <p class="text-success fw-bold mb-0">MZN {{ number_format($product->selling_price, 2) }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Preço de Compra</label>
                            <p class="text-muted mb-0">MZN {{ number_format($product->purchase_price, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Estoque Atual</label>
                            <p class="mb-0">
                                <span class="badge bg-primary rounded-pill px-3 py-2">
                                    {{ $product->stock_quantity }} {{ $product->unit }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Estoque Mínimo</label>
                            <p class="mb-0">{{ $product->min_stock_level }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Status</label>
                            <p class="mb-0">
                                @if($product->is_active)
                                    <span class="badge bg-success rounded-pill px-3 py-2">Ativo</span>
                                @else
                                    <span class="badge bg-danger rounded-pill px-3 py-2">Inativo</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small mb-2">Descrição</label>
                    <div class="border rounded p-3 bg-light">
                        <p class="mb-0">{{ $product->description ?: 'Nenhuma descrição fornecida.' }}</p>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Voltar
                    </a>
                    {{-- <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i> Editar
                    </a> --}}
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow-sm fade-in">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-chart-pie me-2 text-primary"></i>
                    Estatísticas
                </h5>
            </div>
            <div class="card-body">
                <div class="stats-card info mb-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Margem de Lucro</h6>
                            <h4 class="mb-0">
                                @if($product->purchase_price > 0)
                                    {{ number_format((($product->selling_price - $product->purchase_price) / $product->purchase_price) * 100, 2) }}%
                                @else
                                    N/A
                                @endif
                            </h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-percentage fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stats-card warning mb-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Valor em Estoque</h6>
                            <h4 class="mb-0">MZN {{ number_format($product->stock_quantity * $product->purchase_price, 2) }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-calculator fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stats-card {{ $product->stock_quantity <= $product->min_stock_level ? 'danger' : 'success' }}">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Nível de Estoque</h6>
                            <h4 class="mb-0">
                                @if($product->stock_quantity <= $product->min_stock_level)
                                    <span class="text-danger">Crítico</span>
                                @elseif($product->stock_quantity <= $product->min_stock_level * 2)
                                    <span class="text-warning">Atenção</span>
                                @else
                                    <span class="text-success">Normal</span>
                                @endif
                            </h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-warehouse fa-2x {{ $product->stock_quantity <= $product->min_stock_level ? 'text-danger' : 'text-success' }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($product->stockMovements->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm fade-in">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-exchange-alt me-2 text-primary"></i>
                    Movimentos de Estoque
                </h5>
                <small>Total: {{ $product->stockMovements->count() }} registros</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
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
                                    <td>{{ \Carbon\Carbon::parse($movement->movement_date)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @switch($movement->movement_type)
                                            @case('in')
                                                <span class="badge bg-success rounded-pill">Entrada</span>
                                                @break
                                            @case('out')
                                                <span class="badge bg-danger rounded-pill">Saída</span>
                                                @break
                                            @case('adjustment')
                                                <span class="badge bg-warning rounded-pill">Ajuste</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary rounded-pill">{{ ucfirst($movement->movement_type) }}</span>
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
        </div>
    </div>
</div>
@endif
@endsection

@section('css')
<style>
    .stats-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 1.25rem;
        box-shadow: var(--shadow-card);
        transition: var(--transition);
        border-left: 4px solid transparent;
    }
    
    .stats-card.info {
        border-left-color: var(--print-cyan);
    }
    
    .stats-card.success {
        border-left-color: var(--print-green);
    }
    
    .stats-card.warning {
        border-left-color: var(--print-orange);
    }
    
    .stats-card.danger {
        border-left-color: #dc3545;
    }
    
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-strong);
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
    }
    
    .table td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-out;
    }
</style>
@endsection