<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PrintCenter+') }} - @yield('title', 'Sistema de Reprografia')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --print-blue: #1e3a8a;
            --print-cyan: #0891b2;
            --print-gray: #374151;
            --print-green: #059669;
            --print-orange: #ea580c;
            --primary-gradient: linear-gradient(135deg, var(--print-blue) 0%, var(--print-cyan) 100%);
            --secondary-gradient: linear-gradient(135deg, var(--print-gray) 0%, #6b7280 100%);
            --success-gradient: linear-gradient(135deg, var(--print-green) 0%, #10b981 100%);
            --warning-gradient: linear-gradient(135deg, var(--print-orange) 0%, #f97316 100%);
            --shadow-soft: 0 8px 25px rgba(0, 0, 0, 0.08);
            --shadow-strong: 0 15px 35px rgba(0, 0, 0, 0.12);
            --shadow-card: 0 4px 15px rgba(0, 0, 0, 0.06);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            overflow-x: hidden;
            position: relative;
        }

        /* Background pattern para reprografia */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect x="45" y="20" width="10" height="60" fill="rgba(30,58,138,0.02)" rx="2"/><rect x="20" y="35" width="60" height="30" fill="rgba(30,58,138,0.015)" rx="4"/><circle cx="25" cy="25" r="3" fill="rgba(8,145,178,0.02)"/><circle cx="75" cy="75" r="3" fill="rgba(8,145,178,0.02)"/></svg>');
            background-repeat: repeat;
            background-size: 150px 150px;
            z-index: -2;
            animation: subtleMove 60s ease-in-out infinite;
        }

        @keyframes subtleMove {

            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(-10px, -10px);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Estilos para os separadores de seção */
        .nav-pills .dropdown-divider {
            margin: 0.5rem 0 0.25rem 0;
            opacity: 0.3;
        }

        .nav-pills small.text-white-50 {
            letter-spacing: 0.5px;
        }

        /* Melhor visual para itens ativos em seções */
        .nav-pills .nav-link.active {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .nav-pills .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 0 3px 3px 0;
        }

        /* Animação suave para hover nos separadores */
        .nav-pills small.text-white-50 {
            transition: all 0.3s ease;
        }

        .nav-pills:hover small.text-white-50 {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--primary-gradient);
            box-shadow: var(--shadow-strong);
            z-index: 1040;
            transition: transform 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        /* OVERLAY */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1035;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* MAIN CONTENT RESPONSIVO */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* MOBILE RESPONSIVO */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* Sidebar Brand */
        .sidebar-brand {
            background: rgba(0, 0, 0, 0.15);
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand .logo-wrapper {
            position: relative;
            display: inline-block;
            animation: pulse 3s ease-in-out infinite;
        }

        .sidebar-brand h4 {
            color: white;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .sidebar-brand small {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 400;
        }

        /* Navigation Items */
        .nav-pills .nav-link {
            color: rgba(255, 255, 255, 0.85);
            border-radius: 12px;
            margin: 4px 0;
            padding: 12px 16px;
            transition: var(--transition);
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .nav-pills .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transition: left 0.3s ease;
            z-index: 0;
        }

        .nav-pills .nav-link:hover::before {
            left: 0;
        }

        .nav-pills .nav-link:hover {
            color: white;
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-pills .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .nav-pills .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 12px;
            position: relative;
            z-index: 1;
        }

        .nav-pills .nav-link span {
            position: relative;
            z-index: 1;
        }

        /* User Area */
        .user-area {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem;
            margin-top: auto;
        }

        /* Top Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: var(--shadow-soft);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-card);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--print-blue), var(--print-cyan), var(--print-orange));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-strong);
        }

        /* Buttons */
        .btn {
            border-radius: 10px;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.25);
        }

        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-card);
            transition: var(--transition);
            border-left: 4px solid transparent;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-strong);
        }

        .stats-card.primary {
            border-left-color: var(--print-blue);
        }

        .stats-card.success {
            border-left-color: var(--print-green);
        }

        .stats-card.warning {
            border-left-color: var(--print-orange);
        }

        .stats-card.info {
            border-left-color: var(--print-cyan);
        }

        /* Toast */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--shadow-strong);
            backdrop-filter: blur(10px);
        }

        /* Alerts */
        .alert-success {
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.1), rgba(16, 185, 129, 0.1));
            color: var(--print-green);
            border: 1px solid rgba(5, 150, 105, 0.2);
            border-left: 4px solid var(--print-green);
            border-radius: var(--border-radius);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-left: 4px solid #dc2626;
            border-radius: var(--border-radius);
        }

        /* Form controls */
        .form-control,
        .form-select {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            transition: var(--transition);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--print-blue);
            box-shadow: 0 0 0 0.2rem rgba(30, 58, 138, 0.15);
        }

        /* Breadcrumb */
        .breadcrumb {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: var(--shadow-soft);
        }

        .breadcrumb-item a {
            color: var(--print-blue);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-item a:hover {
            color: var(--print-cyan);
        }

        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--print-blue);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Toast Container -->
    <div class="toast-container" id="toast-container"></div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="logo-wrapper">
                <i class="fas fa-print text-white fs-1"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">+</span>
            </div>
            <h4>FDSMS<span class="text-warning">+</span></h4>
            <small>Sistema de Reprografia Completo</small>
        </div>

        <div class="flex-grow-1 px-3 py-3">
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}" data-permission="view_dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('sales.create') ? 'active' : '' }}"
                        href="{{ route('sales.create') }}">
                        <i class="fas fa-cash-register"></i>
                        <span>Ponto de Venda</span>
                    </a>
                </li>

                <!-- Separador de seção -->
                <li class="my-3">
                    <hr class="dropdown-divider border-light opacity-25">
                    <small class="text-white-50 fw-semibold text-uppercase px-2" style="font-size: 0.75rem;">Produtos &
                        Vendas</small>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                        href="{{ route('products.index') }}">
                        <i class="fas fa-box"></i>
                        <span>Produtos</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                        href="{{ route('categories.index') }}" data-permission="manage_categories">
                        <i class="fas fa-tags"></i>
                        <span>Categorias</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}"
                        href="{{ route('orders.index') }}" data-permission="manage_orders">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Pedidos</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('sales.*') && !request()->routeIs('sales.create') ? 'active' : '' }}"
                        href="{{ route('sales.index') }}" data-permission="view_sales">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Vendas</span>
                    </a>
                </li>

                <!-- Separador de seção -->
                <li class="my-3">
                    <hr class="dropdown-divider border-light opacity-25">
                    <small class="text-white-50 fw-semibold text-uppercase px-2"
                        style="font-size: 0.75rem;">Financeiro</small>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('debts.*') ? 'active' : '' }}"
                        href="{{ route('debts.index') }}" data-permission="manage_debts">
                        <i class="fas fa-credit-card"></i>
                        <span>Dívidas</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}"
                        href="{{ route('expenses.index') }}">
                        <i class="fas fa-receipt"></i>
                        <span>Despesas</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}"
                        href="{{ route('stock-movements.index') }}" data-permission="manage_stock">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Estoque</span>
                    </a>
                </li>

                <!-- Separador de seção -->
                <li class="my-3">
                    <hr class="dropdown-divider border-light opacity-25">
                    <small class="text-white-50 fw-semibold text-uppercase px-2"
                        style="font-size: 0.75rem;">Sistema</small>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                        href="{{ route('reports.index') }}" data-permission="view-reports">
                        <i class="fas fa-chart-bar"></i>
                        <span>Relatórios</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                        href="{{ route('users.index') }}" data-permission="manage_users">
                        <i class="fas fa-users"></i>
                        <span>Usuários</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                        href="{{ route('profile.edit') }}">
                        <i class="fas fa-user-cog"></i>
                        <span>Perfil</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link" href="#" onclick="showSettings()">
                        <i class="fas fa-cog"></i>
                        <span>Configurações</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="user-area">
            <div class="d-flex align-items-center text-white mb-3">
                <div class="position-relative me-3">
                    <div class="avatar bg-white text-primary rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-light rounded-circle">
                        <span class="visually-hidden">Online</span>
                    </span>
                </div>
                <div>
                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                    <small class="opacity-75">
                        {{ auth()->user()->role === 'admin' ? 'Administrador' : 'Funcionário' }}
                    </small>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="btn btn-outline-light btn-sm w-100 d-flex align-items-center justify-content-center">
                    <i class="fas fa-sign-out-alt me-2"></i> Sair do Sistema
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid px-4">
                <button class="btn btn-outline-secondary d-lg-none me-3" type="button" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="navbar-brand mb-0 h1 fw-bold text-primary d-flex align-items-center">
                    <i class="fas @yield('title-icon', 'fa-home') me-2"></i>
                    @yield('page-title', 'Dashboard')
                </div>

                <div class="ms-auto d-flex align-items-center">
                    <!-- Data e Horário -->
                    <div class="me-4 text-end d-none d-md-block">
                        <div class="text-muted small" id="current-date"></div>
                        <div class="text-primary small fw-semibold">
                            <i class="fas fa-map-marker-alt me-1"></i> Sistema de Reprografia
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light rounded-circle p-0" type="button" data-bs-toggle="dropdown">
                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 36px; height: 36px;">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-header text-primary fw-bold">
                                <i class="fas fa-user me-2"></i>{{ explode(' ', auth()->user()->name)[0] }}
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-cog me-2 text-muted"></i>Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="toggleTheme()">
                                    <i class="fas fa-moon me-2 text-muted" id="theme-icon"></i>
                                    <span id="theme-text">Modo Escuro</span>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2 text-muted"></i>Sair do Sistema
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid px-4 py-4">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb px-3 py-2">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    @yield('breadcrumbs')
                    @if (!View::hasSection('breadcrumbs'))
                        <li class="breadcrumb-item active">Dashboard</li>
                    @endif
                </ol>
            </nav>

            <!-- Alerts -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center fade-in"
                    role="alert">
                    <i class="fas fa-check-circle me-2 fa-lg"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center fade-in"
                    role="alert">
                    <i class="fas fa-exclamation-circle me-2 fa-lg"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')

            <!-- Footer -->
            <footer class="mt-5 bg-white bg-opacity-75 backdrop-blur-sm border-top shadow-sm">
                <div class="container py-3">
                    <div class="row align-items-center">

                        <!-- Lado Esquerdo -->
                        <div class="col-md-6 text-center text-md-start">
                            <small class="text-muted">
                                © {{ date('Y') }} <strong>FDSMULTSERVICES+</strong> | Sistema de Reprografia
                            </small>
                            <br>
                            <small class="text-muted">
                                Desenvolvido por <strong>Eng. Filipe dos Santos</strong>
                            </small>
                        </div>

                        <!-- Lado Direito -->
                        <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                            <small class="text-muted">
                                Versão <span class="fw-semibold">1.1.0</span> |
                                <a href="http://163.192.7.41/" class="text-decoration-none link-primary">Suporte</a> |
                                <a href="#" class="text-decoration-none link-primary">Manual</a>
                            </small>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ===== SIDEBAR MANAGER =====
        class SidebarManager {
            constructor() {
                this.sidebar = document.getElementById('sidebar');
                this.mainContent = document.getElementById('main-content');
                this.overlay = document.getElementById('sidebar-overlay');
                this.toggle = document.getElementById('sidebar-toggle');
                this.isDesktop = window.innerWidth >= 992;
                this.isOpen = false;

                this.init();
            }

            init() {
                this.setupEventListeners();
                this.handleResize();
                window.addEventListener('resize', () => this.handleResize());
            }

            setupEventListeners() {
                this.toggle?.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggleSidebar();
                });

                this.overlay?.addEventListener('click', () => {
                    this.closeSidebar();
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.isOpen && !this.isDesktop) {
                        this.closeSidebar();
                    }
                });
            }

            toggleSidebar() {
                if (this.isOpen) {
                    this.closeSidebar();
                } else {
                    this.openSidebar();
                }
            }

            openSidebar() {
                if (!this.isDesktop) {
                    this.sidebar.classList.add('show');
                    this.overlay.classList.add('show');
                    document.body.style.overflow = 'hidden';
                }
                this.isOpen = true;
            }

            closeSidebar() {
                if (!this.isDesktop) {
                    this.sidebar.classList.remove('show');
                    this.overlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
                this.isOpen = false;
            }

            handleResize() {
                const wasDesktop = this.isDesktop;
                this.isDesktop = window.innerWidth >= 992;

                if (wasDesktop !== this.isDesktop) {
                    this.sidebar.classList.remove('show');
                    this.overlay.classList.remove('show');
                    document.body.style.overflow = '';
                    this.isOpen = false;
                }
            }
        }

        // ===== THEME TOGGLE =====
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            const themeIcon = document.getElementById('theme-icon');
            const themeText = document.getElementById('theme-text');

            if (themeIcon) {
                themeIcon.className = newTheme === 'dark' ? 'fas fa-sun me-2 text-muted' : 'fas fa-moon me-2 text-muted';
            }

            if (themeText) {
                themeText.textContent = newTheme === 'dark' ? 'Modo Claro' : 'Modo Escuro';
            }
        }

        // ===== DATE AND TIME =====
        function updateDateTime() {
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };

            const dateElement = document.getElementById('current-date');
            if (dateElement) {
                dateElement.textContent = new Date().toLocaleDateString('pt-BR', options);
            }
        }

        // ===== TOAST HELPER =====
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container');
            if (!toastContainer) return;

            const toastId = 'toast-' + Date.now();
            const icon = type === 'success' ? 'check-circle' :
                type === 'error' || type === 'danger' ? 'exclamation-circle' :
                type === 'warning' ? 'exclamation-triangle' : 'info-circle';

            const colorClass = type === 'success' ? 'bg-success' :
                type === 'error' || type === 'danger' ? 'bg-danger' :
                type === 'warning' ? 'bg-warning' : 'bg-primary';

            const toastHtml = `
                <div class="toast fade-in ${colorClass} text-white" role="alert" id="${toastId}">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas fa-${icon} me-2"></i>
                        <span class="flex-grow-1">${message}</span>
                        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;

            toastContainer.insertAdjacentHTML('beforeend', toastHtml);

            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 5000
            });

            toast.show();

            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }

        // ===== SETTINGS MODAL =====
        function showSettings() {
            showToast('Funcionalidade de configurações em desenvolvimento', 'info');
        }

        // ===== INITIALIZATION =====
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar tema salvo
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);

            // Atualizar ícone do tema
            const themeIcon = document.getElementById('theme-icon');
            const themeText = document.getElementById('theme-text');

            if (themeIcon) {
                themeIcon.className = savedTheme === 'dark' ? 'fas fa-sun me-2 text-muted' :
                    'fas fa-moon me-2 text-muted';
            }

            if (themeText) {
                themeText.textContent = savedTheme === 'dark' ? 'Modo Claro' : 'Modo Escuro';
            }

            // Inicializar componentes
            try {
                new SidebarManager();
            } catch (error) {
                console.error('Erro ao inicializar sidebar:', error);
            }

            // Atualizar data/hora
            updateDateTime();
            setInterval(updateDateTime, 60000);

            // Bootstrap tooltips
            try {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            } catch (error) {
                console.warn('Erro ao inicializar tooltips:', error);
            }

            // Auto-hide alerts
            setTimeout(() => {
                document.querySelectorAll('.alert.fade.show').forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    if (bsAlert) {
                        setTimeout(() => bsAlert.close(), 5000);
                    }
                });
            }, 100);
        });

        // Expor funções globais
        window.toggleTheme = toggleTheme;
        window.showToast = showToast;
        window.updateDateTime = updateDateTime;
        window.showSettings = showSettings;
    </script>

    @stack('scripts')
</body>

</html>
