@extends('layouts.app')

@section('title', 'Perfil')
@section('page-title', 'Meu Perfil')
@section('title-icon', 'fa-user-gear')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Perfil</li>
@endsection

@section('content')
    <div class="row">
        <!-- Informações do Perfil -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                            style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-light rounded-circle">
                            <span class="visually-hidden">Online</span>
                        </span>
                    </div>
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    @if (!empty($user->role))
                        <span class="badge bg-primary mb-3">{{ ucfirst($user->role) }}</span>
                    @endif

                    @if ($user->especialidade)
                        <div class="mb-2">
                            <i class="fas fa-stethoscope text-primary me-2"></i>
                            <small>{{ $user->especialidade }}</small>
                        </div>
                    @endif

                    @if ($user->crm)
                        <div class="mb-2">
                            <i class="fas fa-id-card text-primary me-2"></i>
                            <small>CRM: {{ $user->crm }}</small>
                        </div>
                    @endif

                    @if ($user->telefone)
                        <div class="mb-2">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <small>{{ $user->telefone }}</small>
                        </div>
                    @endif

                    <div class="text-muted small mt-3">
                        <i class="fas fa-calendar me-1"></i>
                        Membro desde {{ $user->created_at->format('M Y') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulários de Edição -->
        <div class="col-lg-8">
            <!-- Atualizar Informações do Perfil -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-user-edit text-primary me-2"></i>
                        Informações do Perfil
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                                    <div class="text-sm mt-2 text-muted">
                                        Seu endereço de email não foi verificado.
                                        <button form="send-verification" class="btn btn-link p-0 text-decoration-underline">
                                            Clique aqui para reenviar o email de verificação.
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="especialidade" class="form-label">Especialidade</label>
                                <input type="text" class="form-control @error('especialidade') is-invalid @enderror"
                                    id="especialidade" name="especialidade"
                                    value="{{ old('especialidade', $user->especialidade) }}">
                                @error('especialidade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="crm" class="form-label">CRM</label>
                                <input type="text" class="form-control @error('crm') is-invalid @enderror" id="crm"
                                    name="crm" value="{{ old('crm', $user->crm) }}">
                                @error('crm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control @error('telefone') is-invalid @enderror"
                                id="telefone" name="telefone" value="{{ old('telefone', $user->telefone) }}">
                            @error('telefone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Atualizar Senha -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-lock text-warning me-2"></i>
                        Alterar Senha
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Senha Atual</label>
                            <input type="password"
                                class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                id="current_password" name="current_password" autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nova Senha</label>
                            <input type="password"
                                class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                id="password" name="password" autocomplete="new-password">
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                            <input type="password"
                                class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                                id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                            @error('password_confirmation', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-1"></i> Atualizar Senha
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Excluir Conta -->
            <div class="card border-0 shadow-sm border-danger">
                <div class="card-header bg-light border-danger">
                    <h6 class="mb-0 text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Zona de Perigo
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Uma vez que sua conta for excluída, todos os seus recursos e dados serão permanentemente deletados.
                        Antes de excluir sua conta, faça o download de qualquer dado ou informação que você deseja manter.
                    </p>

                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                        data-bs-target="#deleteAccountModal">
                        <i class="fas fa-trash me-1"></i> Excluir Conta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmar Exclusão da Conta
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.
                    </p>
                    <form method="POST" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                        @csrf
                        @method('delete')

                        <div class="mb-3">
                            <label for="password_delete" class="form-label">Confirme sua senha para continuar:</label>
                            <input type="password"
                                class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                id="password_delete" name="password" placeholder="Senha atual" required>
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="deleteAccountForm" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Excluir Conta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Verificação de Email (se necessário) -->
    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
        <form id="send-verification" method="post" action="{{ route('verification.send') }}" style="display: none;">
            @csrf
        </form>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show modal if there are deletion errors
            @if ($errors->userDeletion->isNotEmpty())
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
                deleteModal.show();
            @endif
        });
    </script>
@endpush
