@extends('layouts.app')

@section('title', 'Gestão de Usuários')
@section('page-title', 'Gestão de Usuários')
@section('title-icon', 'fa-users')
@section('breadcrumbs')
    <li class="breadcrumb-item active">Usuários</li>
@endsection

@section('content')
    <!-- Offcanvas para Criar/Editar Usuário -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="userFormOffcanvas" style="width: 600px;">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-user me-2"></i><span id="form-title">Novo Usuário</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <form id="user-form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <input type="hidden" name="user_id" id="user-id">

                <!-- Foto de Perfil -->
                <div class="p-4 border-bottom bg-light">
                    <div class="text-center mb-3">
                        <div class="avatar-upload">
                            <div class="avatar-preview mb-3">
                                <img id="avatar-preview" src="{{ asset('images/avatar-placeholder.png') }}" 
                                     class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <label class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-upload me-1"></i> Alterar Foto
                                <input type="file" name="foto_perfil" id="avatar-upload" class="d-none" accept="image/*">
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Dados do Usuário -->
                <div class="p-4">
                    <h6 class="mb-3"><i class="fas fa-user me-2"></i> Informações Pessoais</h6>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nome Completo *</label>
                                <input type="text" class="form-control" name="name" id="user-name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email *</label>
                                <input type="email" class="form-control" name="email" id="user-email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Telefone</label>
                                <input type="text" class="form-control" name="telefone" id="user-phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Senha</label>
                                <input type="password" class="form-control" name="password" id="user-password">
                                <div class="form-text">Deixe em branco para manter a senha atual</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Confirmar Senha</label>
                                <input type="password" class="form-control" name="password_confirmation" id="user-password-confirm">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tipo de Usuário *</label>
                                <select class="form-select" name="role" id="user-role" required>
                                    <option value="">Selecione</option>
                                    <option value="admin">Administrador</option>
                                    <option value="staff">Funcionário</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 form-check mt-4">
                                <input type="checkbox" class="form-check-input" name="is_active" id="user-active">
                                <label class="form-check-label fw-semibold">Usuário Ativo</label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="offcanvas-footer p-3 border-top">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" form="user-form" class="btn btn-primary flex-fill" id="save-user-btn">
                    <i class="fas fa-save me-2"></i> Salvar Usuário
                </button>
            </div>
        </div>
    </div>

    <!-- Offcanvas para Visualizar Usuário -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="userViewOffcanvas" style="width: 500px;">
        <div class="offcanvas-header bg-info text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-eye me-2"></i>Detalhes do Usuário
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body" id="user-view-content">
            <div class="text-center py-5">
                <div class="loading-spinner mb-3"></div>
                <p class="text-muted">Carregando detalhes...</p>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-users me-2"></i>
                Gestão de Usuários
            </h2>
            <p class="text-muted mb-0">Administração de usuários do sistema</p>
        </div>
        <button type="button" class="btn btn-success" onclick="openCreateUserOffcanvas()">
            <i class="fas fa-user-plus me-2"></i> Novo Usuário
        </button>
    </div>

    <!-- Filtros -->
    <div class="card mb-4 fade-in">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filtros de Usuários
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}" id="filters-form">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Buscar</label>
                        <input type="text" class="form-control" name="search" placeholder="Nome ou email..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select class="form-select" name="role">
                            <option value="">Todos os Tipos</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                            <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Funcionário</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Usuários -->
    <div class="card fade-in">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Lista de Usuários
                </h5>
                <span class="badge bg-primary">Total: {{ $users->total() }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;"></th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    @if($user->foto_perfil)
                                        <img src="{{ asset($user->foto_perfil) }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    @endif
                                </td>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->telefone ?? 'Não informado' }}</td>
                                <td>
                                    <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-info' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info" 
                                                onclick="viewUserDetails({{ $user->id }})" 
                                                title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-warning" 
                                                onclick="openEditUserOffcanvas({{ $user->id }})" 
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if($loggedId !== $user->id)
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')" 
                                                    title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-outline-secondary" disabled title="Você não pode excluir seu próprio usuário">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-users fa-3x mb-3 opacity-50"></i>
                                    <p>Nenhum usuário encontrado.</p>
                                    <button type="button" class="btn btn-primary" onclick="openCreateUserOffcanvas()">
                                        <i class="fas fa-user-plus me-2"></i> Criar Primeiro Usuário
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando {{ $users->firstItem() ?? 0 }} a {{ $users->lastItem() ?? 0 }} de {{ $users->total() }}
                </small>
                {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Função para visualizar detalhes do usuário
        function viewUserDetails(userId) {
            const content = document.getElementById('user-view-content');
            content.innerHTML = '<div class="text-center py-5"><div class="loading-spinner"></div><p class="text-muted mt-3">Carregando...</p></div>';
            
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('userViewOffcanvas'));
            offcanvas.show();
            
            fetch(`/users/${userId}/details`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        content.innerHTML = data.html;
                    } else {
                        content.innerHTML = '<div class="alert alert-danger">Erro ao carregar detalhes.</div>';
                    }
                })
                .catch(() => {
                    content.innerHTML = '<div class="alert alert-danger">Erro de conexão.</div>';
                });
        }

        // Função para abrir o offcanvas de novo usuário
        function openCreateUserOffcanvas() {
            resetUserForm();
            document.getElementById('form-title').textContent = 'Novo Usuário';
            document.getElementById('form-method').value = 'POST';
            document.getElementById('user-form').action = "{{ route('users.store') }}";
            document.getElementById('user-id').value = '';
            document.getElementById('avatar-preview').src = "{{ asset('images/avatar-placeholder.png') }}";
            document.getElementById('user-active').checked = true;
            
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('userFormOffcanvas'));
            offcanvas.show();
        }

        // Função para editar usuário
        function openEditUserOffcanvas(userId) {
            resetUserForm();
            document.getElementById('form-title').textContent = 'Editar Usuário';
            document.getElementById('form-method').value = 'PUT';
            document.getElementById('user-form').action = `/users/${userId}`;
            document.getElementById('user-id').value = userId;

            fetch(`/users/${userId}/edit-data`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const u = data.data;
                        document.getElementById('user-name').value = u.name;
                        document.getElementById('user-email').value = u.email;
                        document.getElementById('user-phone').value = u.telefone || '';
                        document.getElementById('user-role').value = u.role;
                        document.getElementById('user-active').checked = u.is_active;
                        if (u.foto_perfil) {
                            document.getElementById('avatar-preview').src = u.foto_perfil;
                        }
                    }
                })
                .catch(() => showToast('Erro ao carregar dados', 'error'));

            const offcanvas = new bootstrap.Offcanvas(document.getElementById('userFormOffcanvas'));
            offcanvas.show();
        }

        // Resetar formulário
        function resetUserForm() {
            document.getElementById('user-form').reset();
            document.getElementById('user-password').value = '';
            document.getElementById('user-password-confirm').value = '';
            document.getElementById('avatar-preview').src = "{{ asset('images/avatar-placeholder.png') }}";
            clearValidation();
        }

        // Preview da foto
        document.getElementById('avatar-upload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Validar formulário
        function validateUserForm() {
            clearValidation();
            let isValid = true;
            const name = document.getElementById('user-name').value.trim();
            const email = document.getElementById('user-email').value.trim();
            const role = document.getElementById('user-role').value;

            if (!name) {
                showFieldError('user-name', 'Nome é obrigatório');
                isValid = false;
            }
            if (!email) {
                showFieldError('user-email', 'Email é obrigatório');
                isValid = false;
            } else if (!/\S+@\S+\.\S+/.test(email)) {
                showFieldError('user-email', 'Email inválido');
                isValid = false;
            }
            if (!role) {
                showFieldError('user-role', 'Tipo de usuário é obrigatório');
                isValid = false;
            }

            return isValid;
        }

        // Submit do formulário
        document.getElementById('user-form').addEventListener('submit', function(e) {
            e.preventDefault();
            if (!validateUserForm()) return;

            const formData = new FormData(this);
            const url = this.action;
            const submitBtn = document.getElementById('save-user-btn');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Salvando...';

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Offcanvas.getInstance(document.getElementById('userFormOffcanvas')).hide();
                    showToast(data.message || 'Usuário salvo com sucesso!', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const selector = `#${field.replace('_', '-')}`;
                            showFieldError(selector, data.errors[field][0]);
                        });
                    }
                    showToast(data.message || 'Erro ao salvar usuário.', 'error');
                }
            })
            .catch(() => showToast('Erro de conexão.', 'error'))
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });

        // Excluir usuário
        function deleteUser(userId, userName) {
            if (!confirm(`Tem certeza que deseja excluir o usuário "${userName}"?`)) return;

            fetch(`/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Usuário excluído com sucesso!', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast(data.message || 'Erro ao excluir usuário.', 'error');
                }
            })
            .catch(() => showToast('Erro de conexão.', 'error'));
        }

        // Funções de utilidade
        function showFieldError(fieldId, message) {
            const field = document.querySelector(fieldId);
            if (field) {
                field.classList.add('is-invalid');
                const feedback = field.parentNode.querySelector('.invalid-feedback') || field.nextElementSibling;
                if (feedback) feedback.textContent = message;
            }
        }

        function clearValidation() {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }

        function showToast(message, type = 'info') {
            const bg = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-primary';
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white ${bg} border-0`;
            toast.style = 'position: fixed; top: 20px; right: 20px; z-index: 10000; width: 350px;';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast, { delay: 5000 });
            bsToast.show();
            toast.addEventListener('hidden.bs.toast', () => toast.remove());
        }

        // Auto-submit nos filtros
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filters-form');
            const inputs = form.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.addEventListener('change', () => form.submit());
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .avatar-preview img {
            border: 3px solid #e9ecef;
            transition: all 0.2s ease;
        }
        .avatar-preview img:hover {
            border-color: #0d6efd;
            transform: scale(1.05);
        }
        .loading-spinner {
            width: 30px; height: 30px; border: 3px solid #f3f4f6; border-top: 3px solid #0d6efd; border-radius: 50%; animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endpush