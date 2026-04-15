@extends('layouts.app')

@section('title', 'Folha Salarial')
@section('page-title', 'Folha Salarial Mensal')
@section('title-icon', 'fa-file-invoice-dollar')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('users.employees') }}">Funcionários</a></li>
    <li class="breadcrumb-item active">Folha Salarial</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <form method="GET" action="{{ route('users.employees.payroll') }}" class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Mês de Referência</label>
                        <input type="date" name="reference_month" value="{{ $referenceMonth->format('Y-m-d') }}" class="form-control">
                    </div>
                    <div class="col-md-8 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync-alt me-1"></i>Atualizar Folha
                        </button>
                        <a href="{{ route('users.employees') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Funcionários
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <small class="text-muted text-uppercase">Funcionários</small>
                <h3 class="mt-2 mb-0">{{ $summary['employees'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <small class="text-muted text-uppercase">Base Salarial</small>
                <h3 class="mt-2 mb-0 text-primary">MT {{ number_format($summary['base_total'], 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <small class="text-muted text-uppercase">Pago no Mês</small>
                <h3 class="mt-2 mb-0 text-success">MT {{ number_format($summary['paid_total'], 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <small class="text-muted text-uppercase">Pendente</small>
                <h3 class="mt-2 mb-0 {{ $summary['balance_total'] > 0 ? 'text-danger' : 'text-success' }}">
                    MT {{ number_format($summary['balance_total'], 2, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0">
            <i class="fas fa-table me-2 text-primary"></i>
            Folha de {{ $referenceMonth->format('m/Y') }}
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Funcionário</th>
                        <th>Cargo</th>
                        <th class="text-end">Salário Base</th>
                        <th class="text-end">Pago</th>
                        <th class="text-end">Saldo</th>
                        <th class="text-center">Estado</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrollRows as $row)
                        <tr>
                            <td>
                                <strong>{{ $row['employee']->employee_label }}</strong><br>
                                <small class="text-muted">{{ $row['employee']->email }}</small>
                            </td>
                            <td>{{ $row['employee']->job_title ?: '-' }}</td>
                            <td class="text-end">MT {{ number_format($row['base_salary'], 2, ',', '.') }}</td>
                            <td class="text-end text-success">MT {{ number_format($row['paid_amount'], 2, ',', '.') }}</td>
                            <td class="text-end {{ $row['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                                MT {{ number_format($row['balance'], 2, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeClass = match($row['status']) {
                                        'paid' => 'bg-success',
                                        'partial' => 'bg-warning',
                                        'pending' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                    $badgeText = match($row['status']) {
                                        'paid' => 'Pago',
                                        'partial' => 'Parcial',
                                        'pending' => 'Pendente',
                                        default => 'Sem salário',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
                            </td>
                            <td>
                                <a href="{{ route('users.show', $row['employee']) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Nenhum funcionário ativo encontrado para a folha salarial.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
