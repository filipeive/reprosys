<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReproSys Pro | Gestão Inteligente para Reprografia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #ec4899;
            --dark: #0f172a;
            --glass: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--dark);
            color: #f8fafc;
            overflow-x: hidden;
        }

        .navbar {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--glass-border);
            padding: 1.5rem 0;
        }

        .hero-section {
            padding: 180px 0 100px;
            background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.15), transparent),
                        radial-gradient(circle at bottom left, rgba(236, 72, 153, 0.1), transparent);
            position: relative;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, #fff, var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-premium {
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            color: white;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }

        .btn-primary-gradient:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
            color: white;
        }

        .feature-card {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2.5rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            backdrop-filter: blur(5px);
        }

        .feature-card:hover {
            transform: scale(1.05);
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--primary);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .pricing-card {
            background: #1e293b;
            border-radius: 30px;
            padding: 3rem;
            border: 1px solid var(--glass-border);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .pricing-card.popular {
            border: 2px solid var(--primary);
            transform: scale(1.05);
            z-index: 2;
        }

        .popular-badge {
            position: absolute;
            top: 20px;
            right: -35px;
            background: var(--primary);
            color: white;
            padding: 5px 40px;
            transform: rotate(45deg);
            font-size: 0.8rem;
            font-weight: bold;
        }

        .hero-img-container {
            position: relative;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: var(--primary);
            filter: blur(150px);
            opacity: 0.2;
            z-index: -1;
            border-radius: 50%;
        }

        .section-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 3rem;
            text-align: center;
        }

        footer {
            padding: 50px 0;
            border-top: 1px solid var(--glass-border);
            margin-top: 100px;
        }

        .text-gradient {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-dark">
        <div class="container uppercase">
            <a class="navbar-brand fw-bold fs-3" href="#">
                <span class="text-primary">Repro</span>Sys <small class="fs-6 text-muted">Pro</small>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link px-3" href="#features">Funcionalidades</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#pricing">Planos</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-premium btn-sm px-4">Entrar</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a href="{{ route('register') }}" class="btn btn-primary-gradient btn-premium btn-sm px-4">Começar Agora</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <header class="hero-section">
        <div class="blob" style="top: -100px; right: -100px;"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <span class="badge bg-primary bg-opacity-10 text-primary p-2 px-3 mb-3 border border-primary border-opacity-25 rounded-pill">
                        VERSÃO 2025 DISPONÍVEL
                    </span>
                    <h1 class="hero-title">Domine a Sua Reprografia com Inteligência.</h1>
                    <p class="lead text-muted mb-5 fs-4">
                        A solução definitiva para gestão de stock, vendas, dívidas e produção em tempo real. Poupamos-lhe horas de trabalho manual para que se foque no que importa: <strong>Crescer.</strong>
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#pricing" class="btn btn-primary-gradient btn-premium shadow-lg">Ver Planos</a>
                        <a href="javascript:void(0)" onclick="demoLogin()" class="btn btn-outline-light btn-premium border-2">
                            <i class="fas fa-play me-2"></i>Testar Demo
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0">
                    <div class="hero-img-container">
                        <img src="/home/fdev-ms/.gemini/antigravity/brain/66137161-0844-4ed0-aea6-c79d909a8116/reprosys_hero_banner_1775904516739.png" alt="ReproSys Portal" class="img-fluid rounded-4 shadow-2xl border border-secondary border-opacity-25">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Features -->
    <section id="features" class="py-5 mt-5">
        <div class="container">
            <h2 class="section-title">Porquê escolher o <span class="text-gradient">ReproSys?</span></h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-cubes"></i></div>
                        <h4>Gestão de Produção</h4>
                        <p class="text-muted">Controle automático de insumos. Venda uma cópia e o sistema reduz o stock de papel automaticamente.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        <h4>Controle de Dívidas</h4>
                        <p class="text-muted">Nunca mais perca dinheiro. Registe dívidas de clientes, envie lembretes e gira pagamentos parciais.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-chart-pie"></i></div>
                        <h4>Relatórios Pro</h4>
                        <p class="text-muted">Gráficos de lucro e perda, fluxo de caixa e curva ABC de produtos em segundos.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="py-5 mt-5 bg-black bg-opacity-25">
        <div class="container">
            <h2 class="section-title">Pacotes Disponíveis</h2>
            <div class="row g-4 align-items-center justify-content-center">
                <!-- Plano Starter -->
                <div class="col-lg-4">
                    <div class="pricing-card">
                        <h3>Starter</h3>
                        <div class="display-4 fw-bold mb-4">500 <small class="fs-5">MT/mês</small></div>
                        <ul class="list-unstyled mb-5 text-muted">
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Até 100 Vendas/mês</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Gestão de Stock</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>1 Utilizador</li>
                            <li class="mb-2 text-decoration-line-through"><i class="fas fa-times me-2"></i>Relatórios Avançados</li>
                        </ul>
                        <a href="#" class="btn btn-outline-light w-100 btn-premium py-2">Selecionar</a>
                    </div>
                </div>
                <!-- Plano Business -->
                <div class="col-lg-4">
                    <div class="pricing-card popular">
                        <div class="popular-badge">RECOMENDADO</div>
                        <h3 class="text-primary">Business</h3>
                        <div class="display-4 fw-bold mb-4">1,500 <small class="fs-5">MT/mês</small></div>
                        <ul class="list-unstyled mb-5">
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Vendas Ilimitadas</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Stock e Consumíveis</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Até 3 Utilizadores</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Gestão de Dívidas</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Relatórios Pro</li>
                        </ul>
                        <a href="#" class="btn btn-primary-gradient w-100 btn-premium py-2">Comprar Agora</a>
                    </div>
                </div>
                <!-- Plano Enterprise -->
                <div class="col-lg-4">
                    <div class="pricing-card">
                        <h3>Enterprise</h3>
                        <div class="display-4 fw-bold mb-4">Custom</div>
                        <ul class="list-unstyled mb-5 text-muted">
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Múltiplas Lojas</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Backups Automáticos</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Suporte 24/7</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Customização Total</li>
                        </ul>
                        <a href="mailto:geral@fds.co.mz" class="btn btn-outline-light w-100 btn-premium py-2">Contactar Vendas</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="container text-center">
        <p class="text-muted small">&copy; 2025 FDS MULTISERVICES+. Todos os direitos reservados.</p>
        <div class="d-flex justify-content-center gap-4 mt-3">
            <a href="#" class="text-muted"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="text-muted"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-muted"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </footer>

    <form id="demo-form" action="{{ route('login') }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="email" value="demo@reprosys.com">
        <input type="hidden" name="password" value="demo123">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function demoLogin() {
            // No futuro, isto pode fazer um POST automático para uma conta demo
            alert('A conta demo está a ser preparada. Utilize o login: demo@reprosys.com / senha: demo123');
            window.location.href = "{{ route('login') }}";
        }
    </script>
</body>
</html>
