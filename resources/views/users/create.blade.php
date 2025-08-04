@extends('adminlte::page')
@section('title', 'Novo Usuário')

@section('content_header')
    <h1><i class="fas fa-user-plus mr-2"></i>Novo Usuário</h1>
    <a href="{{ route('users.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card card-outline card-primary mt-3">
        <div class="card-header">Preencha os dados do usuário</div>
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label for="name">Nome</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                </div>
                <div class="form-group mb-3">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                </div>
                <div class="form-group mb-3">
                    <label for="telefone">Telefone</label>
                    <input type="text" name="telefone" class="form-control" value="{{ old('telefone') }}">
                </div>
                <div class="form-group mb-3">
                    <label for="role">Tipo</label>
                    <select name="role" class="form-control" required>
                        <option value="admin">Administrador</option>
                        <option value="staff">Funcionário</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="password">Senha</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group mb-3">
                    <label for="foto_perfil">Foto de Perfil</label>
                    <input type="file" name="foto_perfil" class="form-control">
                </div>
                <div class="form-group mb-3">
                    <label>
                        <input type="checkbox" name="is_active" value="1" checked> Ativo
                    </label>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Salvar</button>
            </form>
        </div>
    </div>
@endsection
