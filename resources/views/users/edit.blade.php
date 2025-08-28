@extends('layouts.app')
@section('title', 'Editar Usuário')

@section('content_header')
    <h1><i class="fas fa-user-edit mr-2"></i>Editar Usuário</h1>
    <a href="{{ route('users.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    {{-- error all --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card card-outline card-primary mt-3">
        <div class="card-header">Atualize os dados do usuário</div>
        <div class="card-body">
            <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label for="name">Nome</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}">
                </div>
                <div class="form-group mb-3">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email', $user->email) }}">
                </div>
                <div class="form-group mb-3">
                    <label for="telefone">Telefone</label>
                    <input type="text" name="telefone" class="form-control" value="{{ old('telefone', $user->telefone) }}">
                </div>
                <div class="form-group mb-3">
                    <label for="role">Tipo</label>
                    <select name="role" class="form-control" required>
                        <option value="admin" @if($user->role == 'admin') selected @endif>Administrador</option>
                        <option value="staff" @if($user->role == 'staff') selected @endif>Funcionário</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="password">Senha <small>(preencha para alterar)</small></label>
                    <input type="password" name="password" class="form-control">
                </div>
                {{-- password confirmation eh igual ao password --}}
                <div class="form-group mb-3">
                    <label for="password_confirmation">Confirmação de Senha</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
                <div class="form-group mb-3">
                    <label for="foto_perfil">Foto de Perfil</label>
                    <input type="file" name="foto_perfil" class="form-control">
                    @if($user->foto_perfil)
                        <img src="{{ asset($user->foto_perfil) }}" class="img-circle mt-2" style="width: 48px; height: 48px;">
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label>
                        <input type="checkbox" name="is_active" value="1" @if($user->is_active) checked @endif> Ativo
                    </label>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Atualizar</button>
            </form>
        </div>
    </div>
@endsection