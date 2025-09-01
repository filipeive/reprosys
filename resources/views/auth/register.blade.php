<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Reprografia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .register-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .register-brand {
            background: #1e3a8a;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .register-brand h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        .register-form {
            padding: 2rem;
        }
        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }
        .btn-primary {
            background: #1e3a8a;
            border: none;
            padding: 0.75rem;
            font-size: 1.1rem;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background: #1e40af;
        }
        .form-check-input:checked {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="register-container">
                    <div class="register-brand">
                        <h1><i class="fas fa-print me-2"></i> Reprografia</h1>
                        <p class="mb-0">Sistema de Gestão</p>
                    </div>
                    <div class="register-form">
                        <h4 class="text-center mb-4">Criar Nova Conta</h4>
                        
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Nome -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nome Completo *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           name="name" value="{{ old('name') }}" required autofocus>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           name="email" value="{{ old('email') }}" required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Senha -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Senha *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           name="password" required>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirmar Senha -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Confirmar Senha *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>

                            <!-- Tipo de Usuário (apenas se usuário autenticado for admin) -->
                            @if(auth()->check() && auth()->user()->isAdmin())
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Tipo de Usuário</label>
                                    <select class="form-select" name="role">
                                        <option value="staff">Funcionário</option>
                                        <option value="admin">Administrador</option>
                                    </select>
                                </div>
                            @endif

                            <!-- Termos e Condições (apenas para cadastro público) -->
                            @unless(auth()->check())
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                                        <label class="form-check-label" for="terms">
                                            Aceito os <a href="#" class="text-primary">termos e condições</a> do sistema.
                                        </label>
                                    </div>
                                </div>
                            @endunless

                            <!-- Botão de Envio -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i> Criar Conta
                                </button>
                            </div>

                            <!-- Link para Login -->
                            <div class="text-center mt-3">
                                <small class="text-muted">Já tem uma conta? </small>
                                <a href="{{ route('login') }}" class="text-decoration-none">Entrar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>