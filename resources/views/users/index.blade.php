@extends('layouts.app')
@section('title', 'Gestão de Usuários')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark"> <i class="fas fa-users mr-2"></i>Gestão de Usuários</h1>
            <ol class="breadcrumb mt-2">
                <li class="breadcrumb-item"><a href="{{ url('/painel') }}"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active">Usuários</li>
            </ol>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus mr-1"></i> Novo Usuário
        </a>
    </div>
@endsection

@section('content')
    {{-- Alertas de Sistema --}}
    <div id="alerts-container">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @foreach (['error', 'updated', 'deleted'] as $msg)
            @if (session($msg))
                <div class="alert alert-{{ $msg == 'error' ? 'danger' : ($msg == 'deleted' ? 'warning' : 'info') }} alert-dismissible fade show"
                    role="alert">
                    <i
                        class="fas fa-{{ $msg == 'error' ? 'times' : ($msg == 'deleted' ? 'trash' : 'info') }}-circle mr-2"></i>
                    {{ session($msg) }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Card Principal --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i> Lista de Usuários
                </h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" id="search" class="form-control float-right"
                            placeholder="Pesquisar usuário...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th><i class="fas fa-user mr-1"></i>Nome</th>
                            <th><i class="fas fa-envelope mr-1"></i>Email</th>
                            <th><i class="fas fa-phone mr-1"></i>Telefone</th>
                            <th><i class="fas fa-user-tag mr-1"></i>Tipo</th>
                            <th style="width: 150px">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="user-list">
                                                @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($user->foto_perfil)
                                            <img src="{{ asset($user->foto_perfil) }}" class="img-circle mr-2" style="width: 32px; height: 32px;">
                                        @else
                                            <div class="bg-secondary rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->telefone ?? 'Não informado' }}</td>
                                <td>
                                    <span class="badge badge-{{ $user->role == 'admin' ? 'danger' : 'info' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        @if ($loggedId !== intval($user->id))
                                            <form class="d-inline" action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-right" id="pagination">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .table td,
        .table th {
            vertical-align: middle;
        }

        .btn-group .btn {
            margin-right: 2px;
        }

        .badge {
            padding: 6px 10px;
            font-weight: 500;
        }

        #alerts-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
        }

        .alert {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const userList = document.getElementById('user-list');
            const pagination = document.getElementById('pagination');
            let searchTimeout;

            // Função para buscar usuários com debounce
            const fetchUsers = (query = '') => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('users.index') }}?search=${query}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            userList.innerHTML = data.users.data.map(user => `
                    <tr>
                        <td>${user.id}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-secondary rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                ${user.name}
                            </div>
                        </td>
                        <td>${user.email}</td>
                        <td>${user.telefone || 'Não informado'}</td>
                        <td>
                            <span class="badge badge-${
                                user.tipo == 'admin' ? 'danger' :
                                (user.tipo == 'docente' ? 'success' :
                                (user.tipo == 'estudante' ? 'primary' :
                                (user.tipo == 'financeiro' ? 'warning' : 'info')))
                            }">
                                ${user.tipo.charAt(0).toUpperCase() + user.tipo.slice(1)}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="btn-group">
                                <a href="/users/${user.id}" class="btn btn-info btn-sm" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/users/${user.id}/edit" class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                ${user.id != {{ $loggedId }} ? `
                                        <form class="d-inline" action="/users/${user.id}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    ` : ''}
                            </div>
                        </td>
                    </tr>
                `).join('') || `
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-search mr-2"></i>Nenhum usuário encontrado
                            </div>
                        </td>
                    </tr>
                `;

                            pagination.innerHTML = data.pagination;
                        })
                        .catch(error => console.error('Erro ao buscar usuários:', error));
                }, 300);
            };

            // Listener para digitação no campo de busca
            searchInput.addEventListener('input', (event) => {
                fetchUsers(event.target.value);
            });

            // Auto-hide para alertas após 5 segundos
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    setTimeout(() => bsAlert.close(), 5000);
                });
            }, 100);
        });
    </script>
@endsection
