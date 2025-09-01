@extends('layouts.app')

@section('title', 'Pedidos')
@section('title-icon', 'fa-clipboard-list')
@section('page-title', 'Gestão de Pedidos')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Pedidos</li>
@endsection

@section('content')
    <div class="fade-in">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-0">{{ $stats['pending'] }}</h4>
                            <small class="text-muted">Pendentes</small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-0">{{ $stats['in_progress'] }}</h4>
                            <small class="text-muted">Em Andamento</small>
                        </div>
                        <div class="text-cyan">
                            <i class="fas fa-cog fa-spin fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-0">{{ $stats['completed'] }}</h4>
                            <small class="text-muted">Concluídos</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-0">{{ $stats['overdue'] }}</h4>
                            <small class="text-muted">Atrasados</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros e Actions -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente
                                    </option>
                                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>
                                        Em Andamento</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                                        Concluído</option>
                                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>
                                        Entregue</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cliente</label>
                                <input type="text" name="customer" class="form-control" placeholder="Nome do cliente"
                                    value="{{ request('customer') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Data Início</label>
                                <input type="date" name="date_from" class="form-control"
                                    value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Data Fim</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i> Filtrar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('orders.create') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-plus me-2"></i> Novo Pedido
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Pedidos -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Descrição</th>
                                <th>Valor Est.</th>
                                <th>Entrega</th>
                                <th>Prioridade</th>
                                <th>Status</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">#{{ $order->id }}</span>
                                        <br><small class="text-muted">{{ $order->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $order->customer_name }}</div>
                                        @if ($order->customer_phone)
                                            <small class="text-muted">{{ $order->customer_phone }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ Str::limit($order->description, 50) }}</div>
                                        @if ($order->items_count > 0)
                                            <small class="text-muted">{{ $order->items_count }} itens</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">MT {{ number_format($order->estimated_amount, 2) }}</div>
                                        @if ($order->advance_payment > 0)
                                            <small class="text-success">Sinal: MT
                                                {{ number_format($order->advance_payment, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->delivery_date)
                                            <div class="{{ $order->isOverdue() ? 'text-danger' : 'text-muted' }}">
                                                {{ $order->delivery_date->format('d/m/Y') }}
                                            </div>
                                            @if ($order->isOverdue())
                                                <small class="text-danger">
                                                    <i class="fas fa-exclamation-triangle"></i> Atrasado
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">Não definida</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $order->priority_badge }}">
                                            {{ $order->priority_text }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $order->status_badge }}">
                                            {{ $order->status_text }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary"
                                                title="Ver Detalhes"
                                                onclick="showOrderDetails({{ $order->id }}); return false;">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($order->canBeCompleted())
                                                <a href="{{ route('orders.edit', $order) }}"
                                                    class="btn btn-outline-secondary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-clipboard-list fa-3x mb-3 opacity-50"></i>
                                            <p>Nenhum pedido encontrado</p>
                                            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i> Criar Primeiro Pedido
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Paginação -->
        @if ($orders->hasPages())
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
@push('scripts')
    <script>
        function showOrderDetails(orderId) {
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('orderDetailsOffcanvas'));
            const content = document.getElementById('order-details-content');
            const orderIdSpan = document.getElementById('order-id');
            const viewFullLink = document.getElementById('view-full-order');

            // Atualiza UI
            orderIdSpan.textContent = orderId;
            viewFullLink.href = `/orders/${orderId}`;

            // Loading
            content.innerHTML = `
        <div class="text-center py-5">
            <div class="loading-spinner mb-3"></div>
            <p class="text-muted">Carregando detalhes...</p>
        </div>
    `;

            offcanvas.show();

            // Busca os dados via AJAX
            fetch(`/orders/${orderId}`)
                .then(response => response.text())
                .then(html => {
                    // Extraímos apenas o conteúdo do body da view show
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const mainContent = doc.querySelector('main')?.innerHTML || '<p>Erro ao carregar dados.</p>';
                    content.innerHTML = mainContent;
                })
                .catch(err => {
                    content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Erro ao carregar detalhes do pedido.
                </div>
            `;
                });
        }
    </script>
@endpush
