@extends('layouts.app')

@section('title', 'Gestão de Despesas')
@section('page-title', 'Despesas')
@section('title-icon', 'fa-money-bill-wave')
@section('breadcrumbs')
    <li class="breadcrumb-item active">Despesas</li>
@endsection

@section('content')
    <!-- Header com botões de ação -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-danger fw-bold">
                <i class="fas fa-money-bill-wave me-2"></i>
                Gestão de Despesas
            </h2>
            <p class="text-muted mb-0">Registre e acompanhe todas as despesas da reprografia</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createExpenseModal">
                <i class="fas fa-plus me-2"></i> Nova Despesa
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="fas fa-folder-plus me-2"></i> Nova Categoria
            </button>
            <a href="{{ route('expense-categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-tags me-2"></i> Gerir Categorias
            </a>
        </div>
    </div>

    <!-- Modal para Criar Categoria -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title" id="createCategoryModalLabel">
                        <i class="fas fa-folder-plus me-2"></i>Nova Categoria
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="category-form" method="POST" action="{{ route('expense-categories.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nome da Categoria *</label>
                            <input type="text" class="form-control" name="name" required placeholder="Ex: Material de Escritório">
                            <div class="invalid-feedback"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" form="category-form" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Salvar Categoria
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Criar Despesa -->
    <div class="modal fade" id="createExpenseModal" tabindex="-1" aria-labelledby="createExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title" id="createExpenseModalLabel">
                        <i class="fas fa-money-bill-wave me-2"></i>Nova Despesa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="expense-form" method="POST" action="{{ route('expenses.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tag text-muted me-1"></i> Categoria *
                            </label>
                            <select class="form-select" name="expense_category_id" required>
                                <option value="">Selecione uma categoria</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-pen text-muted me-1"></i> Descrição *
                            </label>
                            <input type="text" class="form-control" name="description" required placeholder="Descreva a despesa...">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-dollar-sign text-muted me-1"></i> Valor (MT) *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">MT</span>
                                    <input type="number" class="form-control" name="amount" step="0.01" min="0.01" required placeholder="0,00">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-calendar text-muted me-1"></i> Data *
                                </label>
                                <input type="date" class="form-control" name="expense_date" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-receipt text-muted me-1"></i> Número do Recibo
                            </label>
                            <input type="text" class="form-control" name="receipt_number" placeholder="Opcional">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-sticky-note text-muted me-1"></i> Observações
                            </label>
                            <textarea class="form-control" name="notes" rows="3" maxlength="500" placeholder="Notas adicionais..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" form="expense-form" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Salvar Despesa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Detalhes da Despesa -->
    <div class="modal fade" id="expenseDetailsModal" tabindex="-1" aria-labelledby="expenseDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-info text-white border-0">
                    <h5 class="modal-title" id="expenseDetailsModalLabel">
                        <i class="fas fa-eye me-2"></i>Detalhes da Despesa #<span id="expense-details-id"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body p-4" id="expense-details-content">
                    <div class="text-center py-5">
                        <div class="loading-spinner mb-3"></div>
                        <p class="text-muted">Carregando detalhes...</p>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Total de Despesas</h6>
                            <h3 class="mb-0 text-danger fw-bold">{{ number_format($totalExpenses, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">registradas no período</small>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Média de Despesas</h6>
                            <h3 class="mb-0 text-success fw-bold">{{ number_format($averageExpense, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">por ocorrência</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Maior Despesa</h6>
                            <h3 class="mb-0 text-warning fw-bold">{{ number_format($highestExpense, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">única ocorrência</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-arrow-up fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Menor Despesa</h6>
                            <h3 class="mb-0 text-info fw-bold">{{ number_format($lowestExpense, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">única ocorrência</small>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-arrow-down fa-2x"></i>
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
                Filtros e Pesquisa
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('expenses.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Pesquisar Descrição</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="search"
                                value="{{ request('search') }}" placeholder="Descrição da despesa...">
                            <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Inicial</label>
                        <input type="date" class="form-control" name="date_from"
                            value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Final</label>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Despesas -->
    <div class="card fade-in">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Despesas Registradas
                </h5>
                <span class="badge bg-primary">Total: {{ $expenses->total() }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Data</th>
                            <th>Categoria</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Usuário</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr data-id="{{ $expense->id }}" data-category="{{ $expense->category?->name }}"
                                data-description="{{ $expense->description }}" data-amount="{{ $expense->amount }}"
                                data-date="{{ $expense->expense_date->format('Y-m-d') }}"
                                data-receipt="{{ $expense->receipt_number }}" data-notes="{{ $expense->notes }}"
                                data-user="{{ $expense->user?->name }}">
                                <td><strong class="text-danger">#{{ $expense->id }}</strong></td>
                                <td><strong>{{ $expense->expense_date->format('d/m/Y') }}</strong></td>
                                <td><span class="badge bg-light text-dark">{{ $expense->category?->name ?? 'N/A' }}</span></td>
                                <td>{{ Str::limit($expense->description, 50) }}</td>
                                <td><strong class="text-danger">{{ number_format($expense->amount, 2, ',', '.') }} MT</strong></td>
                                <td><small>{{ $expense->user?->name ?? 'N/A' }}</small></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info view-btn" title="Ver Detalhes"
                                            data-bs-toggle="tooltip" data-bs-placement="top">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('expenses.edit', $expense) }}"
                                            class="btn btn-outline-warning" title="Editar" data-bs-toggle="tooltip"
                                            data-bs-placement="top">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}"
                                            class="d-inline"
                                            onsubmit="return confirmDelete('{{ $expense->description }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Excluir"
                                                data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <i class="fas fa-money-bill-wave fa-3x mb-3 opacity-50"></i>
                                        <h5>Nenhuma despesa encontrada</h5>
                                        <p class="mb-3">Registre sua primeira despesa ou ajuste os filtros.</p>
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createExpenseModal">
                                            <i class="fas fa-plus me-2"></i>Adicionar Despesa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($expenses->hasPages())
                <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Mostrando {{ $expenses->firstItem() ?? 0 }} a {{ $expenses->lastItem() ?? 0 }} de
                        {{ $expenses->total() }}
                    </small>
                    {{ $expenses->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Limpar validação
        function clearValidation() {
            document.querySelectorAll('.form-control, .form-select').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }

        // Mostrar erro de campo
        function showFieldError(selector, message) {
            const el = document.querySelector(selector);
            if (el) {
                el.classList.add('is-invalid');
                const feedback = el.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = message;
                }
            }
        }

        // Função para exibir toast
        function showToast(message, type = 'info') {
            const bg = type === 'success' ? 'bg-success' :
                type === 'error' ? 'bg-danger' :
                type === 'warning' ? 'bg-warning' : 'bg-primary';

            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-white ${bg} border-0`;
            toastEl.style = 'position: fixed; top: 20px; right: 20px; z-index: 10060; width: 350px;';
            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            document.body.appendChild(toastEl);
            const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
            toast.show();

            toastEl.addEventListener('hidden.bs.toast', () => {
                toastEl.remove();
            });
        }

        // Confirmação de exclusão
        function confirmDelete(description) {
            return confirm(`Tem certeza que deseja excluir a despesa "${description}"?\n\nEsta ação não pode ser desfeita.`);
        }

        // Limpar pesquisa
        function clearSearch() {
            document.querySelector('input[name="search"]').value = '';
            document.querySelector('form[method="GET"]').submit();
        }

        // Limpar formulários quando modais são fechados
        document.getElementById('createExpenseModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('expense-form').reset();
            clearValidation();
            // Restaurar data de hoje
            const today = new Date().toISOString().split('T')[0];
            document.querySelector('#expense-form input[name="expense_date"]').value = today;
        });

        document.getElementById('createCategoryModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('category-form').reset();
            clearValidation();
        });

        // Submeter o formulário de criação de categoria
        document.getElementById('category-form').addEventListener('submit', function(e) {
            e.preventDefault();
            clearValidation();

            const formData = new FormData(this);

            fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('createCategoryModal'));
                        if (modal) modal.hide();

                        showToast(data.message || 'Categoria criada com sucesso!', 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const selector = `#category-form input[name="${field}"]`;
                                showFieldError(selector, data.errors[field][0]);
                            });
                        }
                        showToast(data.message || 'Erro ao salvar categoria.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showToast('Erro de conexão.', 'error');
                });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Auto-complete data atual no campo de data
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.querySelector('#expense-form input[name="expense_date"]');
            if (dateInput) dateInput.value = today;

            // Ver detalhes (clique no botão de visualizar)
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tr = this.closest('tr');
                    const id = tr.dataset.id;

                    const content = document.getElementById('expense-details-content');
                    const idSpan = document.getElementById('expense-details-id');

                    idSpan.textContent = id;

                    content.innerHTML = `
                        <div class="text-center py-5">
                            <div class="loading-spinner mb-3"></div>
                            <p class="text-muted">Carregando detalhes...</p>
                        </div>
                    `;

                    const modal = new bootstrap.Modal(document.getElementById('expenseDetailsModal'));
                    modal.show();

                    // Preencher dados do data attributes
                    setTimeout(() => {
                        content.innerHTML = `
                            <div class="text-center mb-4 p-4 rounded-3" style="background: linear-gradient(135deg, rgba(220,53,69,0.05), rgba(220,53,69,0.1));">
                                <h4 class="text-danger fw-bold mb-2">${tr.dataset.description}</h4>
                                <span class="badge bg-light text-dark fs-6 mb-2">${tr.dataset.category || 'Sem categoria'}</span>
                                <div class="mt-2">
                                    <span class="badge bg-danger fs-4 px-4 py-2">
                                        ${parseFloat(tr.dataset.amount).toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} MT
                                    </span>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                                <strong>Data</strong>
                                            </div>
                                            <p class="mb-0 ms-4">${tr.dataset.date.split('-').reverse().join('/')}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-receipt text-warning me-2"></i>
                                                <strong>Recibo</strong>
                                            </div>
                                            <p class="mb-0 ms-4">${tr.dataset.receipt || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-user text-info me-2"></i>
                                                <strong>Registrado por</strong>
                                            </div>
                                            <p class="mb-0 ms-4">${tr.dataset.user || 'Sistema'}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-tag text-success me-2"></i>
                                                <strong>Categoria</strong>
                                            </div>
                                            <p class="mb-0 ms-4">${tr.dataset.category || 'Sem categoria'}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-sticky-note text-secondary me-2"></i>
                                                <strong>Observações</strong>
                                            </div>
                                            <p class="mb-0 ms-4 text-muted">${tr.dataset.notes || 'Nenhuma observação registrada.'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }, 400);
                });
            });

            // Submit do formulário de criação de despesa (via AJAX)
            document.getElementById('expense-form').addEventListener('submit', function(e) {
                e.preventDefault();
                clearValidation();

                const formData = new FormData(this);
                const submitBtn = document.querySelector('#createExpenseModal .btn-success');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';
                submitBtn.disabled = true;

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;

                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('createExpenseModal'));
                            if (modal) modal.hide();

                            showToast(data.message || 'Despesa criada com sucesso!', 'success');
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            if (data.errors) {
                                Object.keys(data.errors).forEach(field => {
                                    let selector;
                                    if (field === 'expense_category_id') {
                                        selector = '#expense-form select[name="expense_category_id"]';
                                    } else {
                                        selector = `#expense-form input[name="${field}"], #expense-form textarea[name="${field}"]`;
                                    }
                                    showFieldError(selector, data.errors[field][0]);
                                });
                            }
                            showToast(data.message || 'Erro ao salvar despesa.', 'error');
                        }
                    })
                    .catch(error => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                        console.error('Erro:', error);
                        showToast('Erro de conexão.', 'error');
                    });
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .stats-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .stats-card.danger {
            border-left-color: #dc2626;
        }

        .stats-card.success {
            border-left-color: #059669;
        }

        .stats-card.warning {
            border-left-color: #ea580c;
        }

        .stats-card.info {
            border-left-color: #0891b2;
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

        /* Modal customizado */
        .modal-content {
            border-radius: 12px;
            overflow: hidden;
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-radius: 0 0 12px 12px;
        }

        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        }

        @media (max-width: 768px) {
            .modal-dialog {
                margin: 0.5rem;
            }
        }

        .table-hover tbody tr:hover {
            background-color: rgba(220, 38, 38, 0.05);
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f3f4f6;
            border-top: 3px solid #ef4444;
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

        .btn-group .btn {
            transition: all 0.3s ease;
        }

        .btn-group .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .toast {
            backdrop-filter: blur(10px);
        }
    </style>
@endpush
