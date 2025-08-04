<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%);
            color: #343a40;
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 420px;
            margin: 4rem auto;
            padding: 2.5rem 2rem;
            background-color: #fff;
            border-radius: 1rem;
            box-shadow: 0 1rem 2rem rgba(0, 123, 255, 0.08);
            text-align: center;
        }

        h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #007bff;
        }

        h2 {
            font-size: 1.2rem;
            font-weight: 400;
            margin-bottom: 1.2rem;
            color: #495057;
        }

        p {
            margin-bottom: 2rem;
            color: #6c757d;
        }

        .buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
            margin-bottom: 1.5rem;
        }

        .button {
            padding: 0.8rem 0;
            font-size: 1.05rem;
            font-weight: 600;
            border-radius: 0.4rem;
            border: none;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
            cursor: pointer;
        }

        .button-primary {
            background: linear-gradient(90deg, #007bff 60%, #0056b3 100%);
            color: #fff;
            border: 1px solid #007bff;
        }

        .button-secondary {
            background-color: #f1f3f6;
            color: #007bff;
            border: 1px solid #007bff;
        }

        .button-primary:hover {
            background: #0056b3;
        }

        .button-secondary:hover {
            background-color: #e9ecef;
        }

        .links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .links a {
            text-decoration: none;
            color: #007bff;
            font-weight: 500;
            transition: color 0.2s;
        }

        .links a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .forgot {
            margin-top: 0.5rem;
            text-align: right;
        }

        .forgot a {
            font-size: 0.95rem;
            color: #6c757d;
            text-decoration: none;
        }

        .forgot a:hover {
            color: #007bff;
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .container {
                padding: 1.2rem 0.5rem;
                margin: 1.5rem;
            }
            h1 {
                font-size: 1.5rem;
            }
            h2 {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bem-vindo ao Sistema</h1>
        <h2>Sua solução completa para gestão de utilizadores, despesas e muito mais.</h2>

        <p>Este sistema oferece ferramentas para simplificar operações. Gerencie utilizadores com diferentes funções, controle despesas de forma eficiente e obtenha insights valiosos para o seu negócio.</p>

        <div class="buttons">
            @auth
                <a href="{{ url('/dashboard') }}" class="button button-primary">Ir para o Painel</a>
            @else
                <a href="{{ route('login') }}" class="button button-primary">Entrar</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="button button-secondary">Registar-se</a>
                @endif
            @endauth
        </div>

        @guest
            @if (Route::has('password.request'))
                <div class="forgot">
                    <a href="{{ route('password.request') }}">Esqueceu a senha?</a>
                </div>
            @endif
        @endguest

        <div class="links">
            <a href="#">Saiba Mais</a>
            <a href="#">Contacte-nos</a>
        </div>
    </div>
</body>
</html>