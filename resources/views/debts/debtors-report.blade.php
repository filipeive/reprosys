@extends('layouts.app')

@section('title', 'Relatório de Devedores')
@section('page-title', 'Devedores')
@section('title-icon', 'fa-chart-bar')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('debts.index') }}">Dívidas</a></li>
    <li class="breadcrumb-item active">Devedores</li>
@endsection

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-chart-bar me-2"></i>
                Relatório de Devedores
            </h2>
            <p class="text-muted mb-0">Clientes com dívidas em aberto</p>
        </div>
        <a href="{{ route('debts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Voltar
        </a>
    </div>

    <!-- Filtros -->
    <div class="card mb-4 fade-in">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filtro por Cliente
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('debts.debtors-report') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Cliente</label>
                        <input type="text" class="form-control" name="customer" value="{{ request('customer') }}" placeholder="Nome do cliente...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Total em Aberto</h6>
                            <h3 class="mb-0 text-danger fw-bold">MT {{ number_format($debtors->sum('total_debt'), 2, ',', '.') }}</h3>
                            <small class="text-muted">de todas as dívidas</small>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Total de Devedores</h6>
                            <h3 class="mb-0 text-warning fw-bold">{{ $debtors->count() }}</h3>
                            <small class="text-muted">clientes em débito</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Média por Cliente</h6>
                            <h3 class="mb-0 text-primary fw-bold">
                                MT {{ $debtors->count() > 0 ? number_format($debtors->sum('total_debt') / $debtors->count(), 2, ',', '.') : '0,00' }}
                            </h3>
                            <small class="text-muted">valor médio</small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Dívida Mais Antiga</h6>
                            <h3 class="mb-0 text-success fw-bold">
                                {{ $debtors->min('oldest_debt') ? \Carbon\Carbon::parse($debtors->min('oldest_debt'))->format('d/m/Y') : 'N/A' }}
                            </h3>
                            <small class="text-muted">desde</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Devedores -->
    <div class="card fade-in">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-users me-2 text-primary"></i>
                Lista de Devedores
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Cliente</th>
                            <th>Telefone</th>
                            <th class="text-end">Total em Dívida</th>
                            <th class="text-center">Qtd Dívidas</th>
                            <th>Primeira Dívida</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debtors as $debtor)
                            <tr>
                                <td><strong>{{ $debtor->customer_name }}</strong></td>
                                <td>{{ $debtor->customer_phone ?? 'N/A' }}</td>
                                <td class="text-end text-danger fw-bold">
                                    MT {{ number_format($debtor->total_debt, 2, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning">{{ $debtor->debt_count }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($debtor->oldest_debt)->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('debts.index', ['customer' => $debtor->customer_name]) }}" 
                                       class="btn btn-outline-info btn-sm" title="Ver Dívidas">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-users fa-2x mb-3 opacity-50"></i>
                                    <p>Nenhum devedor encontrado.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light">
                {{ $debtors->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection