@extends('layouts.app')

@section('title', 'Perfil do Usuário')
@section('page-title', 'Perfil do Usuário')
@section('title-icon', 'fa-user')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
    <li class="breadcrumb-item active">Perfil</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <img src="{{ $user->avatar_url }}" class="rounded-circle mb-3" width="150" height="150">
                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <span
                        class="badge bg-{{ $user->role?->name === 'admin' ? 'danger' : ($user->role?->name === 'manager' ? 'primary' : 'success') }} mb-3">
                        {{ $user->role_display }}
                    </span>

                    <div class="d-flex justify-content-center gap-2 mb-4">
                        @if (auth()->user()->canEdit($user))
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                        @endif
                        @if (auth()->user()->canDelete($user))
                            <button class="btn btn-outline-danger btn-sm" onclick="showDeleteModal()">
                                <i class="fas fa-trash me-1"></i>Excluir
                            </button>
                        @endif
                    </div>

                    <div class="alert alert-{{ $user->is_active ? 'success' : 'danger' }} text-center">
                        <i class="fas fa-{{ $user->is_active ? 'check' : 'times' }} me-2"></i>
                        <strong>Status:</strong> {{ $user->status_display }}
                    </div>

                    @if ($user->hasActiveTemporaryPassword())
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-key me-2"></i>
                            <strong>Senha Temporária Ativa</strong><br>
                            <small>{{ $user->getActiveTemporaryPassword()->expiration_status }}</small>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-info text-primary me-2"></i>Informações do Sistema</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">ID do Usuário:</span>
                        <span class="fw-semibold">#{{ $user->id }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Email:</span>
                        <span>{{ $user->email }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Criado em:</span>
                        <span>{{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Última atualização:</span>
                        <span>{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Último login:</span>
                        <span class="{{ $user->last_login_at ? 'text-success' : 'text-muted' }}">
                            {{ $user->last_login_formatted }}
                        </span>
                    </div>

                    @if ($user->photo_path)
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Foto de Perfil:</span>
                            <span class="text-success"><i class="fas fa-check"></i> Sim</span>
                        </div>
                    @endif

                    @if ($user->temporaryPasswords()->count() > 0)
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Senhas Temporárias:</span>
                            <span class="badge bg-info">{{ $user->temporaryPasswords()->count() }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-history text-primary me-2"></i>Atividades Recentes</h6>
                    <a href="{{ route('users.activity', $user) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-chart-line me-1"></i>Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    @if ($user->activities->count() > 0)
                        <div class="timeline">
                            @foreach ($user->activities->take(5) as $activity)
                                <div class="timeline-item mb-3">
                                    <div class="d-flex">
                                        <div class="timeline-marker">
                                            <i class="fas {{ $activity->icon }} text-{{ $activity->badge_color }}"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">{{ $activity->description }}</h6>
                                            <small class="text-muted">
                                                {{ $activity->created_at->diffForHumans() }}
                                                @if ($activity->ip_address)
                                                    • IP: {{ $activity->ip_address }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            Nenhuma atividade registrada ainda
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-cog text-primary me-2"></i>Permissões e Ações</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if (auth()->user()->canEdit($user))
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3">
                                            <i class="fas fa-history text-info me-2"></i>
                                            Histórico de Senhas
                                        </h6>
                                        <p class="text-muted small mb-3">Ver todas as senhas temporárias</p>
                                        <a href="{{ route('users.temporary-passwords', $user) }}"
                                            class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-history me-1"></i> Ver Histórico
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Exclusão
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        Tem certeza que deseja excluir o usuário <strong>{{ $user->name }}</strong>?
                    </p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Esta ação não pode ser desfeita e todos os dados relacionados serão perdidos.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Excluir Usuário
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .timeline {
            position: relative;
        }

        .timeline-item {
            position: relative;
            padding-left: 3rem;
        }

        .timeline-marker {
            position: absolute;
            left: 0;
            top: 0;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .timeline-item:not(:last-child):before {
            content: '';
            position: absolute;
            left: 0.9rem;
            top: 2rem;
            bottom: -1rem;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-content h6 {
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .timeline-content small {
            font-size: 0.75rem;
            line-height: 1.2;
        }

        .card .card-body .btn[disabled] {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function showDeleteModal() {
            const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
            modal.show();
        }

        function toggleUserStatus() {
            // Mostrar confirmação
            if (!confirm('Tem certeza que deseja alterar o status deste usuário?')) {
                return;
            }

            const button = document.getElementById('toggleStatusBtn');
            const originalContent = button.innerHTML;

            // Mostrar loading
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processando...';

            fetch(`/users/{{ $user->id }}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        FDSMULTSERVICES.Toast.show(data.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        FDSMULTSERVICES.Toast.show(data.error || 'Erro ao alterar status', 'error');
                        button.disabled = false;
                        button.innerHTML = originalContent;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    FDSMULTSERVICES.Toast.show('Erro de conexão ao alterar status', 'error');
                    button.disabled = false;
                    button.innerHTML = originalContent;
                });
        }

        function resetPassword(userId) {
            if (!confirm(
                    'Tem certeza que deseja resetar a senha deste usuário? Uma senha temporária será gerada válida por 24 horas.'
                    )) {
                return;
            }

            const button = document.getElementById('resetPasswordBtn');
            const originalContent = button.innerHTML;

            // Mostrar loading
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Gerando...';

            fetch(`/users/${userId}/reset-password`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    button.disabled = false;
                    button.innerHTML = originalContent;

                    if (data.success) {
                        showPasswordModal(data.password, data.expires_at);
                        FDSMULTSERVICES.Toast.show(data.message, 'success');
                    } else {
                        FDSMULTSERVICES.Toast.show(data.error || 'Erro ao resetar senha', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    button.disabled = false;
                    button.innerHTML = originalContent;
                    FDSMULTSERVICES.Toast.show('Erro de conexão ao resetar senha', 'error');
                });
        }

        function showPasswordModal(password, expiresAt) {
            const modalHtml = `
                <div class="modal fade" id="passwordModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-key me-2"></i>Senha Temporária Gerada
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                                <div class="alert alert-warning border-0">
                                    <div class="mb-4">
                                        <h6><strong>Nova Senha Temporária:</strong></h6>
                                        <div class="bg-dark text-light p-4 rounded my-3 position-relative">
                                            <h2 class="fw-bold font-monospace mb-0" style="letter-spacing: 2px;">${password}</h2>
                                            <button type="button" class="btn btn-sm btn-outline-light position-absolute top-0 end-0 m-2" 
                                                    onclick="copyPassword('${password}')" title="Copiar senha">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <strong>Válida até:</strong> ${expiresAt}
                                        </small>
                                    </div>
                                    
                                    <div class="alert alert-info border-0 text-start">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Instruções importantes:</strong>
                                        <ul class="mt-2 mb-0 ps-3">
                                            <li>Esta senha expira em 24 horas</li>
                                            <li>Entregue com segurança ao usuário</li>
                                            <li>O usuário deve alterar no primeiro login</li>
                                            <li>Esta janela não aparecerá novamente</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>Fechar
                                </button>
                                <button type="button" class="btn btn-warning" onclick="copyPassword('${password}')">
                                    <i class="fas fa-copy me-2"></i>Copiar Senha
                                </button>
                                <button type="button" class="btn btn-primary" onclick="printPassword('${password}', '${expiresAt}')">
                                    <i class="fas fa-print me-2"></i>Imprimir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove modal anterior se existir
            const existingModal = document.getElementById('passwordModal');
            if (existingModal) {
                existingModal.remove();
            }

            document.body.insertAdjacentHTML('beforeend', modalHtml);

            const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
            modal.show();
        }

        function copyPassword(password) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(password).then(() => {
                    FDSMULTSERVICES.Toast.show('Senha copiada para a área de transferência!', 'success');
                }).catch(err => {
                    console.error('Erro ao copiar:', err);
                    fallbackCopyTextToClipboard(password);
                });
            } else {
                fallbackCopyTextToClipboard(password);
            }
        }

        function fallbackCopyTextToClipboard(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    FDSMULTSERVICES.Toast.show('Senha copiada para a área de transferência!', 'success');
                } else {
                    FDSMULTSERVICES.Toast.show('Erro ao copiar senha', 'error');
                }
            } catch (err) {
                console.error('Fallback: Oops, unable to copy', err);
                FDSMULTSERVICES.Toast.show('Erro ao copiar senha', 'error');
            }

            document.body.removeChild(textArea);
        }

        function printPassword(password, expiresAt) {
            const currentDate = new Date().toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Senha Temporária</title>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            margin: 40px; 
                            text-align: center; 
                        }
                        .header { 
                            border-bottom: 3px solid #000; 
                            padding-bottom: 20px; 
                            margin-bottom: 30px; 
                        }
                        .password-box { 
                            border: 3px solid #000; 
                            background: #f8f9fa; 
                            padding: 30px; 
                            margin: 30px 0; 
                            border-radius: 10px;
                        }
                        .password { 
                            font-family: 'Courier New', monospace; 
                            font-size: 28px; 
                            font-weight: bold; 
                            letter-spacing: 3px; 
                            color: #000;
                            margin: 20px 0;
                        }
                        .info { 
                            font-size: 14px; 
                            margin: 10px 0; 
                        }
                        .footer { 
                            border-top: 1px solid #ccc; 
                            padding-top: 20px; 
                            margin-top: 40px; 
                            font-size: 12px; 
                            color: #666;
                        }
                        .warning {
                            background: #fff3cd;
                            border: 1px solid #ffeaa7;
                            padding: 15px;
                            margin: 20px 0;
                            border-radius: 5px;
                        }
                        @media print {
                            body { margin: 20px; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>SENHA TEMPORÁRIA</h1>
                        <h2>Sistema de Gestão</h2>
                    </div>
                    
                    <div class="info">
                        <strong>Usuário:</strong> {{ $user->name }}<br>
                        <strong>Email:</strong> {{ $user->email }}<br>
                        <strong>Data de Geração:</strong> ${currentDate}
                    </div>
                    
                    <div class="password-box">
                        <h3>NOVA SENHA:</h3>
                        <div class="password">${password}</div>
                        <p class="info"><strong>Válida até:</strong> ${expiresAt}</p>
                    </div>
                    
                    <div class="warning">
                        <h4>⚠️ INSTRUÇÕES IMPORTANTES:</h4>
                        <ul style="text-align: left; display: inline-block;">
                            <li>Esta senha é temporária e expira em 24 horas</li>
                            <li>O usuário deve alterar a senha no primeiro login</li>
                            <li>Mantenha esta informação em local seguro</li>
                            <li>Destrua este documento após o uso</li>
                        </ul>
                    </div>
                    
                    <div class="footer">
                        <p>Documento gerado automaticamente pelo sistema em ${currentDate}</p>
                        <p>Este documento contém informações confidenciais</p>
                    </div>
                </body>
                </html>
            `;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(printContent);
            printWindow.document.close();

            // Aguardar carregamento e imprimir
            printWindow.onload = function() {
                printWindow.print();
                // Opcional: fechar janela após impressão
                // printWindow.onafterprint = function() { printWindow.close(); }
            };
        }

        function invalidateTemporaryPasswords() {
            if (!confirm('Tem certeza que deseja invalidar todas as senhas temporárias ativas deste usuário?')) {
                return;
            }

            const button = document.getElementById('invalidateTempBtn');
            const originalContent = button.innerHTML;

            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Invalidando...';

            fetch(`/users/{{ $user->id }}/invalidate-temporary-passwords`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        FDSMULTSERVICES.Toast.show(data.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        FDSMULTSERVICES.Toast.show(data.error || 'Erro ao invalidar senhas', 'error');
                        button.disabled = false;
                        button.innerHTML = originalContent;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    FDSMULTSERVICES.Toast.show('Erro de conexão ao invalidar senhas', 'error');
                    button.disabled = false;
                    button.innerHTML = originalContent;
                });
        }

        // Inicialização do documento
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar se há toast messages do servidor
            @if (session('success'))
                FDSMULTSERVICES.Toast.show('{{ session('success') }}', 'success');
            @endif

            @if (session('error'))
                FDSMULTSERVICES.Toast.show('{{ session('error') }}', 'error');
            @endif

            @if (session('temp_password_used'))
                FDSMULTSERVICES.Toast.show('Você está usando uma senha temporária. Recomendamos alterá-la.',
                    'warning');
            @endif
        });
    </script>
@endpush
