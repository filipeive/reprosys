@extends('adminlte::page')
@section('title', 'Detalhes do Usuário')

@section('content_header')
    <h1><i class="fas fa-user mr-2"></i>Detalhes do Usuário</h1>
    <a href="{{ route('users.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
@endsection

@section('content')
    <div class="card card-outline card-primary mt-3">
        <div class="card-header">Informações do Usuário</div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-auto">
                    @if($user->foto_perfil)
                        <img src="{{ asset($user->foto_perfil) }}" class="img-circle" style="width: 64px; height: 64px;">
                    @else
                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="fas fa-user text-white fa-2x"></i>
                        </div>
                    @endif
                </div>
                <div class="col">
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <span class="badge badge-{{ $user->role == 'admin' ? 'danger' : 'info' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                    @if($user->is_active)
                        <span class="badge badge-success">Ativo</span>
                    @else
                        <span class="badge badge-secondary">Inativo</span>
                    @endif
                </div>
            </div>
            <dl class="row">
                <dt class="col-sm-3">E-mail</dt>
                <dd class="col-sm-9">{{ $user->email }}</dd>
                <dt class="col-sm-3">Telefone</dt>
                <dd class="col-sm-9">{{ $user->telefone ?? 'Não informado' }}</dd>
            </dl>
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning"><i class="fas fa-pencil-alt"></i> Editar</a>
        </div>
    </div>
@endsection