@extends('layouts.app')

@section('title', 'Pedidos')
@section('page-title', 'Gestão de Pedidos')
@section('title-icon', 'fa-clipboard-list')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Pedidos</li>
@endsection

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-clipboard-list me-2"></i> Gestão de Pedidos
            </h2>
            <p class="text-muted mb-0">Controle e acompanhamento de pedidos da reprografia</p>
        </div>
        <div class="d-flex gap-2">
            {{-- O botão agora é um link direto para a página de criação --}}
            <a href="{{ route('orders.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i> Novo Pedido
            </a>
            <a href="{{ route('orders.report') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-bar me-2"></i> Relatório
            </a>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Pendentes</h6>
                            <h3 class="mb-0 text-primary fw-bold">{{ $stats['pending'] }}</h3>
                            <small class="text-muted">em espera</small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Em Andamento</h6>
                            <h3 class="mb-0 text-cyan fw-bold">{{ $stats['in_progress'] }}</h3>
                            <small class="text-muted">em produção</small>
                        </div>
                        <div class="text-cyan">
                            <i class="fas fa-cog fa-spin fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Concluídos</h6>
                            <h3 class="mb-0 text-success fw-bold">{{ $stats['completed'] }}</h3>
                            <small class="text-muted">finalizados</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Atrasados</h6>
                            <h3 class="mb-0 text-warning fw-bold">{{ $stats['overdue'] }}</h3>
                            <small class="text-muted">fora do prazo</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4 fade-in">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filtros de Pedidos
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('orders.index') }}" id="filters-form">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente
                            </option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Em
                                Andamento</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluído
                            </option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Entregue
                            </option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Prioridade</label>
                        <select class="form-select" name="priority">
                            <option value="">Todas</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Baixa</option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Média</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Cliente</label>
                        <input type="text" class="form-control" name="customer" placeholder="Nome..."
                            value="{{ request('customer') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Data Início</label>
                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Data Fim</label>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Lista de Pedidos -->
    <div class="card fade-in">
        <div class="card-header bg-white">
            {{-- Header da tabela... --}}
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Descrição</th>
                            <th class="text-end">Valor Est.</th>
                            <th>Entrega</th>
                            <th>Prioridade</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                {{-- Colunas de dados do pedido... --}}
                                <td><strong class="text-primary">#{{ $order->id }}</strong></td>
                                <td>{{ $order->customer_name }}</td>
                                <td>{{ Str::limit($order->description, 50) }}</td>
                                <td class="text-end">MT {{ number_format($order->estimated_amount, 2, ',', '.') }}</td>
                                <td>{{ $order->delivery_date ? $order->delivery_date->format('d/m/Y') : '-' }}</td>
                                <td><span class="badge {{ $order->priority_badge }}">{{ $order->priority_text }}</span></td>
                                <td><span class="badge {{ $order->status_badge }}">{{ $order->status_text }}</span></td>
                                
                                {{-- AÇÕES REATORADAS --}}
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <!-- Ver detalhes (link para a página show) -->
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-info" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Editar (link para a página edit) -->
                                        @if ($order->canBeEdited())
                                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        <!-- Concluir (formulário para a rota de status) -->
                                        @if ($order->canBeCompleted())
                                            <form action="{{ route('orders.update-status', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-outline-success" title="Concluir Pedido">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Cancelar (formulário com confirmação JS) -->
                                        @if ($order->canBeCancelled())
                                            <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem a certeza que deseja cancelar este pedido?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Cancelar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <p>Nenhum pedido encontrado.</p>
                                    <a href="{{ route('orders.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Criar Primeiro Pedido
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($orders->hasPages())
                <div class="card-footer bg-light">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .stats-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .stats-card.primary {
            border-left-color: #1e3a8a;
        }

        .stats-card.info {
            border-left-color: #0891b2;
        }

        .stats-card.success {
            border-left-color: #059669;
        }

        .stats-card.warning {
            border-left-color: #ea580c;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .loading-spinner {
            width: 30px;
            height: 30px;
            border: 3px solid #f3f4f6;
            border-top: 3px solid #0d6efd;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .text-cyan {
            color: #0891b2 !important;
        }

        /* Melhorias responsivas */
        @media (max-width: 768px) {

            #orderFormOffcanvas,
            #orderViewOffcanvas {
                width: 100% !important;
            }

            .stats-card {
                margin-bottom: 1rem;
            }

            .btn-group-sm .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }
        }

        /* Toast container posicionamento */
        .toast-container {
            z-index: 9999;
        }

        /* Estilo para campos inválidos */
        .is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* Animação suave para offcanvas */
        .offcanvas {
            transition: transform 0.3s ease-in-out;
        }

        /* Badges personalizados */
        .badge {
            font-size: 0.75em;
            font-weight: 500;
        }

        /* Botões de ação hover */
        .btn-outline-info:hover,
        .btn-outline-warning:hover,
        .btn-outline-primary:hover,
        .btn-outline-danger:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease;
        }

        /* Loading state para botões */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Scrollbar personalizada */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
@endpush
