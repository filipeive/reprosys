<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --print-blue: #1e3a8a;
            --print-cyan: #0891b2;
            --print-orange: #ea580c;
            --primary-gradient: linear-gradient(135deg, var(--print-blue) 0%, var(--print-cyan) 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-strong: 0 30px 60px rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .welcome-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: var(--shadow-strong);
            max-width: 1000px;
            width: 100%;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .welcome-brand {
            background: var(--primary-gradient);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .welcome-brand i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .welcome-brand h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: .5rem;
        }

        .welcome-brand p {
            font-size: 1rem;
            opacity: 0.9;
        }

        .welcome-content {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .welcome-content h2 {
            color: var(--print-blue);
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .welcome-content p {
            color: #374151;
            margin-bottom: 2rem;
            font-size: 1rem;
            line-height: 1.6;
        }

        .welcome-buttons a {
            display: block;
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 12px;
            text-decoration: none;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .btn-primary-custom {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-primary-custom:hover {
            opacity: 0.9;
        }

        .btn-secondary-custom {
            background: #f8fafc;
            border: 2px solid var(--print-blue);
            color: var(--print-blue);
        }

        .btn-secondary-custom:hover {
            background: #e0e7ff;
        }

        .welcome-links {
            margin-top: 1rem;
        }

        .welcome-links a {
            margin: 0 0.5rem;
            color: var(--print-blue);
            text-decoration: none;
            font-weight: 500;
        }

        .welcome-links a:hover {
            color: var(--print-cyan);
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .welcome-card {
                grid-template-columns: 1fr;
            }
            .welcome-brand {
                padding: 2rem;
            }
            .welcome-content {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-card">
        <!-- Brand -->
        <div class="welcome-brand">
            <i class="fas fa-print"></i>
            <h1>{{ config('app.name', 'Laravel') }}</h1>
            <p>Sistema de Reprografia Completo</p>
        </div>

        <!-- Content -->
        <div class="welcome-content">
            <h2>Bem-vindo ao Sistema</h2>
            <p>
                Gerencie utilizadores, controle despesas e tenha insights valiosos para o seu negócio com uma interface moderna e intuitiva.
            </p>

            <div class="welcome-buttons">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary-custom">Ir para o Painel</a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary-custom">Entrar</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-secondary-custom">Registar-se</a>
                    @endif
                @endauth
            </div>

            @guest
                @if (Route::has('password.request'))
                    <div class="mt-2">
                        <a href="{{ route('password.request') }}">Esqueceu a senha?</a>
                    </div>
                @endif
            @endguest

            <div class="welcome-links">
                <a href="#">Saiba Mais</a> | 
                <a href="#">Contacte-nos</a>
            </div>
        </div>
    </div>
</body>
</html>
