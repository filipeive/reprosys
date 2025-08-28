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
        <button class="btn btn-success" onclick="openCreateExpenseOffcanvas()">
            <i class="fas fa-plus me-2"></i> Nova Despesa
        </button>
    </div>

    <!-- Offcanvas para Criar/Editar Despesa -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="expenseFormOffcanvas" style="width: 500px;">
        <div class="offcanvas-header bg-success text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-money-bill-wave me-2"></i><span id="offcanvas-title">Nova Despesa</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="expense-form" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <input type="hidden" name="id" id="expense-id">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Categoria *</label>
                    <select class="form-select" name="expense_category_id" id="expense-category" required>
                        <option value="">Selecione uma categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Descrição *</label>
                    <input type="text" class="form-control" name="description" id="expense-description" required>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Valor (MT) *</label>
                    <input type="number" class="form-control" name="amount" id="expense-amount" step="0.01" min="0" required>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Data *</label>
                        <input type="date" class="form-control" name="expense_date" id="expense-date" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Número do Recibo</label>
                        <input type="text" class="form-control" name="receipt_number" id="receipt-number">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Observações</label>
                    <textarea class="form-control" name="notes" id="expense-notes" rows="3" maxlength="500"></textarea>
                </div>
            </form>
        </div>
        <div class="offcanvas-footer p-3 border-top">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="submit" form="expense-form" class="btn btn-success flex-fill">
                    <i class="fas fa-save me-2"></i>Salvar
                </button>
            </div>
        </div>
    </div>

    <!-- Offcanvas para Detalhes da Despesa -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="expenseDetailsOffcanvas" style="width: 500px;">
        <div class="offcanvas-header bg-info text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-eye me-2"></i>Detalhes da Despesa #<span id="expense-details-id"></span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body" id="expense-details-content">
            <div class="text-center py-5">
                <div class="loading-spinner mb-3"></div>
                <p class="text-muted">Carregando detalhes...</p>
            </div>
        </div>
        <div class="offcanvas-footer p-3 border-top bg-light">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i>Fechar
                </button>
                <button type="button" class="btn btn-warning flex-fill" id="edit-from-details-btn" data-bs-dismiss="offcanvas" onclick="editBtn.onclick = () => openEditCategoryOffcanvas(categoryId);">
                    <i class="fas fa-edit me-2"></i>Editar
                </button>
            </div>
        </div>
    </div>

    <!-- Toast para Confirmação de Exclusão -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
        <div id="deleteToast" class="toast hide" role="alert">
            <div class="toast-header bg-danger text-white">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong class="me-auto">Confirmar Exclusão</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                <p class="mb-2">Excluir despesa: <strong id="delete-expense-description"></strong>?</p>
                <small class="text-muted">Esta ação não pode ser desfeita.</small>
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-sm btn-secondary" data-bs-dismiss="toast">Cancelar</button>
                    <form id="delete-form" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                    </form>
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
            <form id="filters-form">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Pesquisar Descrição</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" placeholder="Descrição da despesa...">
                            <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Inicial</label>
                        <input type="date" class="form-control" id="date_from">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Final</label>
                        <input type="date" class="form-control" id="date_to">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary" onclick="filterExpenses()">
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
                    <tbody id="expenses-tbody">
                        @forelse($expenses as $expense)
                            <tr data-id="{{ $expense->id }}"
                                data-category="{{ $expense->category?->name }}"
                                data-description="{{ $expense->description }}"
                                data-amount="{{ $expense->amount }}"
                                data-date="{{ $expense->expense_date->format('Y-m-d') }}"
                                data-receipt="{{ $expense->receipt_number }}"
                                data-notes="{{ $expense->notes }}"
                                data-user="{{ $expense->user?->name }}">
                                <td><strong class="text-danger">#{{ $expense->id }}</strong></td>
                                <td><strong>{{ $expense->expense_date->format('d/m/Y') }}</strong></td>
                                <td><span class="badge bg-light text-dark">{{ $expense->category?->name ?? 'N/A' }}</span></td>
                                <td>{{ $expense->description }}</td>
                                <td><strong class="text-danger">{{ number_format($expense->amount, 2, ',', '.') }} MT</strong></td>
                                <td><small>{{ $expense->user?->name ?? 'N/A' }}</small></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info view-btn" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning edit-btn" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger delete-btn" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <i class="fas fa-money-bill-wave fa-3x mb-3 opacity-50"></i>
                                        <h5>Nenhuma despesa encontrada</h5>
                                        <p class="mb-3">Registre sua primeira despesa.</p>
                                        <button class="btn btn-success" onclick="openCreateExpenseOffcanvas()">
                                            <i class="fas fa-plus me-2"></i>Adicionar Despesa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando {{ $expenses->firstItem() ?? 0 }} a {{ $expenses->lastItem() ?? 0 }} de {{ $expenses->total() }}
                </small>
                {{ $expenses->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Função para abrir o offcanvas de criação
        function openCreateExpenseOffcanvas() {
            document.getElementById('offcanvas-title').textContent = 'Nova Despesa';
            document.getElementById('form-method').value = 'POST';
            document.getElementById('expense-form').action = '{{ route('expenses.store') }}';
            resetForm();
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('expenseFormOffcanvas'));
            offcanvas.show();
        }

        // Resetar formulário
        function resetForm() {
            document.getElementById('expense-form').reset();
            document.getElementById('expense-id').value = '';
            clearValidation();
        }

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
            toastEl.style = 'position: fixed; top: 20px; right: 20px; z-index: 10000; width: 350px;';
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
        // Função GLOBAL para abrir o offcanvas de edição
            function openEditCategoryOffcanvas(categoryId) {
                const tr = document.querySelector(`tr[data-id="${categoryId}"]`);
                if (!tr) {
                    showToast('Categoria não encontrada na tabela.', 'error');
                    return;
                }

                // Preencher formulário
                document.getElementById('offcanvas-title').textContent = 'Editar Categoria';
                document.getElementById('form-method').value = 'PUT';
                document.getElementById('category-form').action = `/categories/${categoryId}`;
                document.getElementById('category-id').value = categoryId;
                document.getElementById('category-name').value = tr.dataset.name;
                document.getElementById('category-description').value = tr.dataset.description;
                document.getElementById('category-type').value = tr.dataset.type;
                document.getElementById('category-color').value = tr.dataset.color;
                document.getElementById('category-icon').value = tr.dataset.icon;
                document.getElementById('category-active').checked = tr.dataset.status === 'active';

                // Limpar validação
                clearValidation();

                // Abrir offcanvas
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('categoryFormOffcanvas'));
                offcanvas.show();

                // Fechar o offcanvas de detalhes
                const detailsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('categoryDetailsOffcanvas'));
                if (detailsOffcanvas) {
                    detailsOffcanvas.hide();
                }
            }
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('search');
            const dateFrom = document.getElementById('date_from');
            const dateTo = document.getElementById('date_to');

            // Filtros
            const filterExpenses = () => {
                const search = searchInput.value.toLowerCase();
                const from = dateFrom.value;
                const to = dateTo.value;

                document.querySelectorAll('#expenses-tbody tr').forEach(tr => {
                    if (tr.querySelector('.text-muted')) return;

                    const description = tr.dataset.description.toLowerCase();
                    const date = tr.dataset.date;

                    const matchesSearch = !search || description.includes(search);
                    const matchesDateFrom = !from || date >= from;
                    const matchesDateTo = !to || date <= to;

                    tr.style.display = matchesSearch && matchesDateFrom && matchesDateTo ? '' : 'none';
                });
            };

            searchInput.addEventListener('input', filterExpenses);
            dateFrom.addEventListener('change', filterExpenses);
            dateTo.addEventListener('change', filterExpenses);

            // Editar despesa
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tr = this.closest('tr');
                    const id = tr.dataset.id;

                    document.getElementById('offcanvas-title').textContent = 'Editar Despesa';
                    document.getElementById('form-method').value = 'PUT';
                    document.getElementById('expense-form').action = `/expenses/${id}`;
                    document.getElementById('expense-id').value = id;
                    document.getElementById('expense-category').value = Array.from(document.querySelectorAll('#expense-category option'))
                        .find(opt => opt.text === tr.dataset.category)?.value || '';
                    document.getElementById('expense-description').value = tr.dataset.description;
                    document.getElementById('expense-amount').value = tr.dataset.amount;
                    document.getElementById('expense-date').value = tr.dataset.date;
                    document.getElementById('receipt-number').value = tr.dataset.receipt || '';
                    document.getElementById('expense-notes').value = tr.dataset.notes || '';

                    clearValidation();
                    const offcanvas = new bootstrap.Offcanvas(document.getElementById('expenseFormOffcanvas'));
                    offcanvas.show();
                });
            });

            // Ver detalhes
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tr = this.closest('tr');
                    const id = tr.dataset.id;

                    const offcanvas = new bootstrap.Offcanvas(document.getElementById('expenseDetailsOffcanvas'));
                    const content = document.getElementById('expense-details-content');
                    const idSpan = document.getElementById('expense-details-id');
                    const editBtn = document.getElementById('edit-from-details-btn');

                    idSpan.textContent = id;
                    editBtn.onclick = () => openEditExpenseOffcanvas(id);

                    content.innerHTML = `
                        <div class="text-center py-5">
                            <div class="loading-spinner mb-3"></div>
                            <p class="text-muted">Carregando detalhes...</p>
                        </div>
                    `;

                    offcanvas.show();

                    // Simular dados (ou usar API real)
                    content.innerHTML = `
                        <div class="card mb-4">
                            <div class="card-body text-center">
                                <h4 class="card-title text-danger">${tr.dataset.description}</h4>
                                <p class="text-muted">${tr.dataset.category}</p>
                                <span class="badge bg-danger fs-5">${tr.dataset.amount} MT</span>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="alert alert-light">
                                    <strong>Data:</strong> ${tr.dataset.date.split('-').reverse().join('/')}<br>
                                    <strong>Recibo:</strong> ${tr.dataset.receipt || 'N/A'}<br>
                                    <strong>Usuário:</strong> ${tr.dataset.user || 'Você'}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Observações</h6>
                                        <p class="text-muted">${tr.dataset.notes || 'Nenhuma observação registrada.'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            });

            // Exclusão com Toast
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tr = this.closest('tr');
                    const id = tr.dataset.id;
                    const description = tr.dataset.description;

                    document.getElementById('delete-expense-description').textContent = description;
                    document.getElementById('delete-form').action = `/expenses/${id}`;

                    const toast = new bootstrap.Toast(document.getElementById('deleteToast'));
                    toast.show();
                });
            });

            // Submit do formulário
            document.getElementById('expense-form').addEventListener('submit', function(e) {
                e.preventDefault();
                clearValidation();

                const formData = new FormData(this);
                if (document.getElementById('form-method').value === 'PUT') {
                    formData.append('_method', 'PUT');
                }

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const offcanvasEl = document.getElementById('expenseFormOffcanvas');
                        const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasEl);
                        if (offcanvasInstance) offcanvasInstance.hide();

                        showToast(data.message || 'Despesa salva com sucesso!', 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                showFieldError(`#expense-${field.replace('_', '-')}`, data.errors[field][0]);
                            });
                        }
                        showToast(data.message || 'Erro ao salvar despesa.', 'error');
                    }
                })
                .catch(() => showToast('Erro de conexão.', 'error'));
            });

            window.clearSearch = () => {
                searchInput.value = '';
                filterExpenses();
            };
        });
    </script>
@endpush

@push('styles')
    <style>
        .stats-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .stats-card.danger { border-left-color: #dc2626; }
        .stats-card.success { border-left-color: #059669; }
        .stats-card.warning { border-left-color: #ea580c; }
        .stats-card.info { border-left-color: #0891b2; }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .offcanvas-end { width: 500px !important; }
        @media (max-width: 768px) { .offcanvas-end { width: 100% !important; } }
        .table-hover tbody tr:hover { background-color: rgba(220, 38, 38, 0.05); }
        .loading-spinner {
            width: 40px; height: 40px; border: 3px solid #f3f4f6; border-top: 3px solid #ef4444; border-radius: 50%; animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
@endpush