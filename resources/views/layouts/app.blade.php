<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FDSMULTSERVICES+') }} - @yield('title', 'Sistema de Reprografia')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #0078d4;
            --primary-blue-dark: #106ebe;
            --primary-blue-light: #deecf9;
            --secondary-gray: #323130;
            --neutral-light: #f3f2f1;
            --neutral-lightest: #faf9f8;
            --border-color: #edebe9;
            --text-primary: #323130;
            --text-secondary: #605e5c;
            --success-color: #107c10;
            --warning-color: #ff8c00;
            --danger-color: #d13438;

            --sidebar-width: 280px;
            --sidebar-collapsed-width: 48px;
            --header-height: 56px;

            --shadow-depth-4: 0 1.6px 3.6px 0 rgba(0, 0, 0, .132), 0 0.3px 0.9px 0 rgba(0, 0, 0, .108);
            --shadow-depth-8: 0 3.2px 7.2px 0 rgba(0, 0, 0, .132), 0 0.6px 1.8px 0 rgba(0, 0, 0, .108);
            --shadow-depth-16: 0 6.4px 14.4px 0 rgba(0, 0, 0, .132), 0 1.2px 3.6px 0 rgba(0, 0, 0, .108);

            --border-radius-small: 2px;
            --border-radius-medium: 4px;
            --border-radius-large: 8px;

            --transition-fast: 0.15s cubic-bezier(0.4, 0.0, 0.2, 1);
            --transition-normal: 0.2s cubic-bezier(0.4, 0.0, 0.2, 1);
            --transition-slow: 0.3s cubic-bezier(0.4, 0.0, 0.2, 1);
        }

        /* Dark theme */
        [data-bs-theme="dark"] {
            --primary-blue: #4fc3f7;
            --primary-blue-dark: #0288d1;
            --primary-blue-light: #1a1a1a;
            --secondary-gray: #e0e0e0;
            --neutral-light: #2d2d2d;
            --neutral-lightest: #1e1e1e;
            --border-color: #404040;
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--neutral-lightest);
            color: var(--text-primary);
            line-height: 1.5;
            font-weight: 400;
            overflow-x: hidden;
        }

        /* ===== SIDEBAR ===== */
        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--neutral-lightest);
            border-right: 1px solid var(--border-color);
            z-index: 1040;
            transition: all var(--transition-normal);
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-depth-8);
        }

        .app-sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .app-sidebar.mobile-hidden {
            transform: translateX(-100%);
        }

        /* Sidebar Header */
        .sidebar-header {
            height: var(--header-height);
            padding: 8px 16px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--neutral-lightest);
        }

        .brand-container {
            display: flex;
            align-items: center;
            flex: 1;
            min-width: 0;
        }

        .brand-logo {
            width: 32px;
            height: 32px;
            background: var(--primary-blue);
            border-radius: var(--border-radius-medium);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .brand-logo i {
            color: white;
            font-size: 16px;
        }

        .brand-text {
            overflow: hidden;
            transition: all var(--transition-normal);
        }

        .app-sidebar.collapsed .brand-text {
            opacity: 0;
            width: 0;
        }

        .brand-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.2;
            margin: 0;
        }

        .brand-subtitle {
            font-size: 12px;
            color: var(--text-secondary);
            line-height: 1.2;
        }

        .sidebar-toggle {
            width: 32px;
            height: 32px;
            border: none;
            background: transparent;
            border-radius: var(--border-radius-medium);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all var(--transition-fast);
            flex-shrink: 0;
        }

        .sidebar-toggle:hover {
            background: var(--neutral-light);
            color: var(--text-primary);
        }

        /* Navigation */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 8px 0;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }

        .nav-section {
            margin-bottom: 16px;
        }

        .nav-section-title {
            padding: 8px 16px 4px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all var(--transition-normal);
        }

        .app-sidebar.collapsed .nav-section-title {
            opacity: 0;
            height: 0;
            padding: 0;
            margin: 0;
            overflow: hidden;
        }

        .nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 8px 16px;
            color: var(--text-primary);
            text-decoration: none;
            transition: all var(--transition-fast);
            border-radius: 0;
            position: relative;
            min-height: 40px;
        }

        .app-sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 8px 0;
        }

        .nav-link:hover {
            background: var(--neutral-light);
            color: var(--text-primary);
        }

        .nav-link.active {
            background: var(--primary-blue-light);
            color: var(--primary-blue);
            border-right: 3px solid var(--primary-blue);
        }

        .nav-icon {
            width: 16px;
            height: 16px;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
        }

        .app-sidebar.collapsed .nav-icon {
            margin-right: 0;
        }

        .nav-text {
            flex: 1;
            font-size: 14px;
            font-weight: 400;
            white-space: nowrap;
            overflow: hidden;
            transition: all var(--transition-normal);
        }

        .app-sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
        }

        .nav-badge {
            margin-left: auto;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 600;
            transition: all var(--transition-normal);
        }

        .app-sidebar.collapsed .nav-badge {
            opacity: 0;
            transform: scale(0);
        }

        .badge-primary {
            background: var(--primary-blue);
            color: white;
        }

        .badge-success {
            background: var(--success-color);
            color: white;
        }

        .badge-warning {
            background: var(--warning-color);
            color: white;
        }

        .badge-danger {
            background: var(--danger-color);
            color: white;
        }

        .badge-secondary {
            background: var(--text-secondary);
            color: white;
        }

        /* Tooltip for collapsed sidebar */
        .nav-tooltip {
            position: absolute;
            left: 52px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--secondary-gray);
            color: white;
            padding: 6px 8px;
            border-radius: var(--border-radius-medium);
            font-size: 12px;
            white-space: nowrap;
            z-index: 1050;
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition-fast);
            pointer-events: none;
        }

        .nav-tooltip::before {
            content: '';
            position: absolute;
            left: -4px;
            top: 50%;
            transform: translateY(-50%);
            border: 4px solid transparent;
            border-right-color: var(--secondary-gray);
        }

        .app-sidebar.collapsed .nav-link:hover .nav-tooltip {
            opacity: 1;
            visibility: visible;
        }

        /* User Area */
        .user-area {
            border-top: 1px solid var(--border-color);
            padding: 16px;
            background: var(--neutral-lightest);
        }

        .user-profile {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .app-sidebar.collapsed .user-profile {
            justify-content: center;
            margin-bottom: 8px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-blue);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 14px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .app-sidebar.collapsed .user-avatar {
            margin-right: 0;
        }

        .user-info {
            flex: 1;
            min-width: 0;
            transition: all var(--transition-normal);
        }

        .app-sidebar.collapsed .user-info {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 12px;
            color: var(--text-secondary);
            line-height: 1.2;
        }

        .user-actions {
            display: flex;
            gap: 4px;
            margin-bottom: 12px;
            transition: all var(--transition-normal);
        }

        .app-sidebar.collapsed .user-actions {
            opacity: 0;
            height: 0;
            margin: 0;
            overflow: hidden;
        }

        .user-action-btn {
            flex: 1;
            padding: 6px;
            border: 1px solid var(--border-color);
            background: transparent;
            border-radius: var(--border-radius-medium);
            color: var(--text-secondary);
            font-size: 12px;
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .user-action-btn:hover {
            background: var(--neutral-light);
            color: var(--text-primary);
        }

        .logout-btn {
            width: 100%;
            padding: 8px;
            border: none;
            background: var(--danger-color);
            color: white;
            border-radius: var(--border-radius-medium);
            font-size: 13px;
            cursor: pointer;
            transition: all var(--transition-fast);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .app-sidebar.collapsed .logout-btn {
            padding: 8px 4px;
        }

        .logout-btn:hover {
            background: #b71c1c;
        }

        .logout-text {
            margin-left: 8px;
            transition: all var(--transition-normal);
        }

        .app-sidebar.collapsed .logout-text {
            opacity: 0;
            width: 0;
            margin-left: 0;
        }

        /* ===== MAIN CONTENT ===== */
        .app-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left var(--transition-normal);
        }

        .app-content.collapsed {
            margin-left: var(--sidebar-collapsed-width);
        }

        .app-content.expanded {
            margin-left: 0;
        }

        /* Header */
        .app-header {
            height: var(--header-height);
            background: var(--neutral-lightest);
            border-bottom: 1px solid var(--border-color);
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: var(--shadow-depth-4);
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .mobile-menu-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: transparent;
            border-radius: var(--border-radius-medium);
            display: none;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .mobile-menu-btn:hover {
            background: var(--neutral-light);
            color: var(--text-primary);
        }

        .page-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
        }

        .page-title i {
            margin-right: 8px;
            color: var(--primary-blue);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-search {
            position: relative;
            margin-right: 16px;
        }

        .search-input {
            width: 240px;
            padding: 6px 32px 6px 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-medium);
            font-size: 14px;
            background: var(--neutral-lightest);
            color: var(--text-primary);
            transition: all var(--transition-fast);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 1px var(--primary-blue);
        }

        .search-icon {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 14px;
        }

        .header-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: transparent;
            border-radius: var(--border-radius-medium);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all var(--transition-fast);
            position: relative;
        }

        .header-btn:hover {
            background: var(--neutral-light);
            color: var(--text-primary);
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: var(--danger-color);
            color: white;
            font-size: 10px;
            padding: 2px 4px;
            border-radius: 8px;
            min-width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Content Area */
        .content-area {
            flex: 1;
            padding: 24px;
            background: var(--neutral-light);
        }

        /* Breadcrumb */
        .breadcrumb-nav {
            background: transparent;
            padding: 0 0 16px 0;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            font-size: 13px;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: ">";
            color: var(--text-secondary);
        }

        .breadcrumb-item a {
            color: var(--primary-blue);
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        .breadcrumb-item.active {
            color: var(--text-secondary);
        }

        /* Cards */
        .card {
            background: var(--neutral-lightest);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-large);
            box-shadow: var(--shadow-depth-4);
            transition: all var(--transition-fast);
        }

        .card:hover {
            box-shadow: var(--shadow-depth-8);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 16px 20px;
            font-weight: 600;
        }

        .card-body {
            padding: 20px;
        }

        /* Buttons */
        .btn {
            border-radius: var(--border-radius-medium);
            font-weight: 500;
            transition: all var(--transition-fast);
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-blue-dark);
            color: white;
        }

        .btn-outline-primary {
            background: transparent;
            color: var(--primary-blue);
            border: 1px solid var(--primary-blue);
        }

        .btn-outline-primary:hover {
            background: var(--primary-blue);
            color: white;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: var(--border-radius-medium);
            padding: 12px 16px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #f3f9ff;
            color: var(--success-color);
            border-left-color: var(--success-color);
        }

        .alert-danger {
            background: #fdf2f2;
            color: var(--danger-color);
            border-left-color: var(--danger-color);
        }

        .alert-warning {
            background: #fffbf0;
            color: var(--warning-color);
            border-left-color: var(--warning-color);
        }

        .alert-info {
            background: #f0f9ff;
            color: var(--primary-blue);
            border-left-color: var(--primary-blue);
        }

        /* Form Controls */
        .form-control,
        .form-select {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-medium);
            padding: 8px 12px;
            font-size: 14px;
            transition: all var(--transition-fast);
            background: var(--neutral-lightest);
            color: var(--text-primary);
        }

        .form-control:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 1px var(--primary-blue);
        }

        /* Dropdown */
        .dropdown-menu {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-medium);
            box-shadow: var(--shadow-depth-16);
            background: var(--neutral-lightest);
            padding: 4px 0;
        }

        .dropdown-item {
            padding: 8px 16px;
            font-size: 14px;
            color: var(--text-primary);
            transition: all var(--transition-fast);
        }

        .dropdown-item:hover {
            background: var(--neutral-light);
            color: var(--text-primary);
        }

        .dropdown-divider {
            border-color: var(--border-color);
            margin: 4px 0;
        }

        /* Toast Container */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            border: none;
            border-radius: var(--border-radius-medium);
            box-shadow: var(--shadow-depth-16);
            background: var(--neutral-lightest);
            margin-bottom: 8px;
        }

        /* Sidebar Overlay */
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
            transition: all var(--transition-normal);
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1199.98px) {
            .app-content {
                margin-left: var(--sidebar-collapsed-width);
            }

            .app-sidebar:not(.mobile-visible) {
                width: var(--sidebar-collapsed-width);
            }
        }

        @media (max-width: 991.98px) {
            .app-sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width);
            }

            .app-sidebar.mobile-visible {
                transform: translateX(0);
            }

            .app-content {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: flex;
            }

            .header-search {
                display: none;
            }

            .search-input {
                width: 160px;
            }
        }

        @media (max-width: 767.98px) {
            .content-area {
                padding: 16px;
            }

            .app-header {
                padding: 0 16px;
            }

            .page-title {
                font-size: 16px;
            }
        }

        /* Loading States */
        .loading {
            position: relative;
            color: transparent !important;
        }

        .loading::after {
            content: "";
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid var(--primary-blue);
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        /* Utilities */
        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .d-flex {
            display: flex !important;
        }

        .align-items-center {
            align-items: center !important;
        }

        .justify-content-between {
            justify-content: space-between !important;
        }

        .justify-content-center {
            justify-content: center !important;
        }

        .ms-auto {
            margin-left: auto !important;
        }

        .me-2 {
            margin-right: 8px !important;
        }

        .me-3 {
            margin-right: 12px !important;
        }

        .mb-3 {
            margin-bottom: 12px !important;
        }

        .mb-4 {
            margin-bottom: 16px !important;
        }

        .p-3 {
            padding: 12px !important;
        }

        .fw-bold {
            font-weight: 600 !important;
        }

        .fw-semibold {
            font-weight: 500 !important;
        }

        .text-muted {
            color: var(--text-secondary) !important;
        }

        .text-primary {
            color: var(--primary-blue) !important;
        }

        .bg-light {
            background-color: var(--neutral-light) !important;
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
    <nav class="app-sidebar" id="sidebar">
        <!-- Header -->
        <div class="sidebar-header">
            <div class="brand-container">
                <div class="brand-logo">
                    <i class="fas fa-print"></i>
                </div>
                <div class="brand-text">
                    <div class="brand-title">FDSMS+</div>
                    <div class="brand-subtitle">Reprografia</div>
                </div>
            </div>
            <button class="sidebar-toggle" id="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-chevron-left" id="toggle-icon"></i>
            </button>
        </div>

        <!-- Navigation -->
        <div class="sidebar-nav">
            <!-- Dashboard -->
            <div class="nav-section">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-home"></i>
                            </span>
                            <span class="nav-text">Dashboard</span>
                            <div class="nav-tooltip">Dashboard</div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('sales.create') }}"
                            class="nav-link {{ request()->routeIs('sales.create') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-cash-register"></i>
                            </span>
                            <span class="nav-text">Ponto de Venda</span>
                            <span class="nav-badge badge-success">PDV</span>
                            <div class="nav-tooltip">Ponto de Venda</div>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Produtos & Vendas -->
            <div class="nav-section">
                <div class="nav-section-title">Produtos & Vendas</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('products.index') }}"
                            class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-box"></i>
                            </span>
                            <span class="nav-text">Produtos</span>
                            @if (userCan('create_products'))
                                <span class="nav-badge badge-primary">Criar</span>
                            @elseif(userCan('edit_products'))
                                <span class="nav-badge badge-secondary">Editar</span>
                            @else
                                <span class="nav-badge badge-secondary">Ver</span>
                            @endif
                            <div class="nav-tooltip">Produtos</div>
                        </a>
                    </li>

                    @if (userCan('manage_categories'))
                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}"
                                class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                <span class="nav-icon">
                                    <i class="fas fa-tags"></i>
                                </span>
                                <span class="nav-text">Categorias</span>
                                <span class="nav-badge badge-warning">Admin</span>
                                <div class="nav-tooltip">Categorias</div>
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a href="{{ route('orders.index') }}"
                            class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </span>
                            <span class="nav-text">Pedidos</span>
                            @php
                                $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
                            @endphp
                            @if ($pendingOrders > 0)
                                <span class="nav-badge badge-danger">{{ $pendingOrders }}</span>
                            @endif
                            <div class="nav-tooltip">Pedidos</div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('sales.index') }}"
                            class="nav-link {{ request()->routeIs('sales.*') && !request()->routeIs('sales.create') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </span>
                            <span class="nav-text">Vendas</span>
                            @if (userCan('edit_sales'))
                                <span class="nav-badge badge-success">Full</span>
                            @else
                                <span class="nav-badge badge-secondary">Próprias</span>
                            @endif
                            <div class="nav-tooltip">Vendas</div>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Financeiro -->
            <div class="nav-section">
                <div class="nav-section-title">Financeiro</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('debts.index') }}"
                            class="nav-link {{ request()->routeIs('debts.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-credit-card"></i>
                            </span>
                            <span class="nav-text">Dívidas</span>
                            @php
                                $overdueDebts = \App\Models\Debt::where('status', 'overdue')->count();
                            @endphp
                            @if ($overdueDebts > 0)
                                <span class="nav-badge badge-danger">{{ $overdueDebts }}</span>
                            @endif
                            <div class="nav-tooltip">Dívidas</div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('expenses.index') }}"
                            class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-receipt"></i>
                            </span>
                            <span class="nav-text">Despesas</span>
                            @if (userCan('edit_expenses'))
                                <span class="nav-badge badge-primary">Editar</span>
                            @elseif(userCan('create_expenses'))
                                <span class="nav-badge badge-secondary">Criar</span>
                            @else
                                <span class="nav-badge badge-secondary">Ver</span>
                            @endif
                            <div class="nav-tooltip">Despesas</div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('stock-movements.index') }}"
                            class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-exchange-alt"></i>
                            </span>
                            <span class="nav-text">Estoque</span>
                            @if (userCan('create_stock_movements'))
                                <span class="nav-badge badge-primary">Gerir</span>
                            @else
                                <span class="nav-badge badge-secondary">Ver</span>
                            @endif
                            <div class="nav-tooltip">Estoque</div>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Sistema -->
            <div class="nav-section">
                <div class="nav-section-title">Sistema</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('reports.index') }}"
                            class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-chart-bar"></i>
                            </span>
                            <span class="nav-text">Relatórios</span>
                            @if (userCan('export_reports'))
                                <span class="nav-badge badge-success">Export</span>
                            @elseif(userCan('view_reports'))
                                <span class="nav-badge badge-primary">Avançado</span>
                            @else
                                <span class="nav-badge badge-secondary">Básico</span>
                            @endif
                            <div class="nav-tooltip">Relatórios</div>
                        </a>
                    </li>

                    @if (userCan('manage_users'))
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}"
                                class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <span class="nav-icon">
                                    <i class="fas fa-users"></i>
                                </span>
                                <span class="nav-text">Usuários</span>
                                <span class="nav-badge badge-danger">Admin</span>
                                <div class="nav-tooltip">Usuários</div>
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}"
                            class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-user-cog"></i>
                            </span>
                            <span class="nav-text">Perfil</span>
                            <div class="nav-tooltip">Perfil</div>
                        </a>
                    </li>

                    @if (userCan('manage_settings'))
                        <li class="nav-item">
                            <a href="#" onclick="showSettings()" class="nav-link">
                                <span class="nav-icon">
                                    <i class="fas fa-cog"></i>
                                </span>
                                <span class="nav-text">Configurações</span>
                                <span class="nav-badge badge-warning">Config</span>
                                <div class="nav-tooltip">Configurações</div>
                            </a>
                        </li>
                    @endif

                    @if (userCan('backup_system'))
                        <li class="nav-item">
                            <a href="#" onclick="showBackupModal()" class="nav-link">
                                <span class="nav-icon">
                                    <i class="fas fa-database"></i>
                                </span>
                                <span class="nav-text">Backup</span>
                                <span class="nav-badge badge-warning">DB</span>
                                <div class="nav-tooltip">Backup</div>
                            </a>
                        </li>
                    @endif

                    @if (userCan('view_logs'))
                        <li class="nav-item">
                            <a href="#" onclick="showLogsModal()" class="nav-link">
                                <span class="nav-icon">
                                    <i class="fas fa-file-alt"></i>
                                </span>
                                <span class="nav-text">Logs</span>
                                <span class="nav-badge badge-primary">Log</span>
                                <div class="nav-tooltip">Logs</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- User Area -->
        <div class="user-area">
            <div class="user-profile">
                <div class="user-avatar">
                    {{ auth()->user()->initials }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->first_name }}</div>
                    <div class="user-role">
                        @if (auth()->user()->role === 'admin')
                            Administrador
                        @elseif(auth()->user()->role === 'manager')
                            Gerente
                        @else
                            Funcionário
                        @endif
                    </div>
                </div>
            </div>

            <div class="user-actions">
                <button class="user-action-btn" onclick="toggleTheme()" title="Alternar Tema">
                    <i class="fas fa-moon" id="theme-icon-sidebar"></i>
                </button>
                <a href="{{ route('sales.create') }}" class="user-action-btn" title="Nova Venda">
                    <i class="fas fa-plus"></i>
                </a>
                <a href="{{ route('profile.edit') }}" class="user-action-btn" title="Perfil">
                    <i class="fas fa-user"></i>
                </a>
                @if (userCan('view_reports'))
                    <a href="{{ route('reports.index') }}" class="user-action-btn" title="Relatórios">
                        <i class="fas fa-chart-line"></i>
                    </a>
                @endif
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="logout-text">Sair</span>
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="app-content" id="main-content">
        <!-- Header -->
        <header class="app-header">
            <div class="header-left">
                <button class="mobile-menu-btn" id="mobile-toggle" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">
                    <i class="@yield('title-icon', 'fas fa-home')"></i>
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>

            <div class="header-right">
                <div class="header-search">
                    <input type="text" class="search-input" placeholder="Buscar...">
                    <i class="fas fa-search search-icon"></i>
                </div>

                <button class="header-btn" id="notification-btn" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    @if (auth()->user()->unreadNotifications->count() > 0)
                        <span class="notification-badge" id="notification-badge">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="width: 380px;" id="notification-list">
                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                        <strong>Notificações</strong>
                        <a href="#" class="text-decoration-none text-primary" onclick="markAllAsRead(event)">
                            Marcar todas como lidas
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    @forelse(auth()->user()->notifications->take(10) as $notification)
                        <li>
                            <a class="dropdown-item d-flex align-items-start py-2 px-3 {{ $notification->read ? '' : 'bg-light' }}"
                                href="{{ $notification->action_url ?? '#' }}"
                                onclick="markAsRead({{ $notification->id }}, event)">
                                <i
                                    class="{{ $notification->icon ?? 'fas fa-bell' }} mt-1 me-3 text-{{ $notification->type === 'success' ? 'success' : ($notification->type === 'error' ? 'danger' : ($notification->type === 'warning' ? 'warning' : 'info')) }}"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $notification->title }}</div>
                                    <div class="text-muted small">{{ $notification->message }}</div>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                @if (!$notification->read)
                                    <span class="nav-badge badge-primary ms-2">NOVO</span>
                                @endif
                            </a>
                        </li>
                    @empty
                        <li class="dropdown-item-text text-center text-muted py-3">
                            Nenhuma notificação
                        </li>
                    @endforelse

                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li class="text-center">
                        <a href="#" class="small text-decoration-none text-muted"
                            onclick="clearAllNotifications(event)">
                            Limpar histórico
                        </a>
                    </li>
                </ul>

                <button class="header-btn" onclick="toggleTheme()" title="Alternar Tema">
                    <i class="fas fa-moon" id="theme-icon"></i>
                </button>

                <div class="dropdown">
                    <button class="header-btn" data-bs-toggle="dropdown">
                        <div class="user-avatar" style="width: 24px; height: 24px; font-size: 12px;">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-header">
                            <strong>{{ explode(' ', auth()->user()->name)[0] }}</strong>
                            <small class="d-block text-muted">
                                @if (auth()->user()->role === 'admin')
                                    Administrador
                                @elseif(auth()->user()->role === 'manager')
                                    Gerente
                                @else
                                    Funcionário
                                @endif
                            </small>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user me-2"></i>Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="toggleTheme()">
                                <i class="fas fa-moon me-2" id="theme-icon-dropdown"></i>
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
                                    <i class="fas fa-sign-out-alt me-2"></i>Sair
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="content-area">
            <!-- Breadcrumb -->
            <nav class="breadcrumb-nav">
                <ol class="breadcrumb">
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
                <div class="alert alert-success alert-dismissible fade show fade-in" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show fade-in" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="mt-auto bg-light border-top py-3">
            <div class="content-area">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">
                            © {{ date('Y') }} <strong>FDSMULTSERVICES+</strong> - Sistema de Reprografia
                        </small>
                        <br>
                        <small class="text-muted">
                            Desenvolvido por <strong>Eng. Filipe dos Santos</strong>
                        </small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">
                            v1.1.0 |
                            <a href="http://163.192.7.41/" class="text-decoration-none">Suporte</a> |
                            <a href="#" class="text-decoration-none">Manual</a>
                        </small>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ===== SIDEBAR MANAGER =====
        class ModernSidebar {
            constructor() {
                this.sidebar = document.getElementById('sidebar');
                this.mainContent = document.getElementById('main-content');
                this.overlay = document.getElementById('sidebar-overlay');
                this.toggleBtn = document.getElementById('sidebar-toggle');
                this.mobileToggle = document.getElementById('mobile-toggle');
                this.toggleIcon = document.getElementById('toggle-icon');

                this.isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                this.isMobileOpen = false;
                this.breakpoint = 1200;

                this.init();
            }

            init() {
                this.updateLayout();
                this.bindEvents();
                window.addEventListener('resize', () => this.handleResize());
            }

            bindEvents() {
                this.toggleBtn?.addEventListener('click', () => this.toggle());
                this.mobileToggle?.addEventListener('click', () => this.toggleMobile());
                this.overlay?.addEventListener('click', () => this.closeMobile());

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.isMobileOpen) {
                        this.closeMobile();
                    }
                });
            }

            toggle() {
                if (window.innerWidth >= this.breakpoint) {
                    this.isCollapsed = !this.isCollapsed;
                    localStorage.setItem('sidebar-collapsed', this.isCollapsed);
                    this.updateLayout();
                }
            }

            toggleMobile() {
                this.isMobileOpen = !this.isMobileOpen;
                this.updateMobileState();
            }

            closeMobile() {
                this.isMobileOpen = false;
                this.updateMobileState();
            }

            updateLayout() {
                if (window.innerWidth >= this.breakpoint) {
                    // Desktop behavior
                    this.sidebar.classList.remove('mobile-visible');
                    this.overlay.classList.remove('show');

                    if (this.isCollapsed) {
                        this.sidebar.classList.add('collapsed');
                        this.mainContent.classList.add('collapsed');
                        this.toggleIcon.className = 'fas fa-chevron-right';
                    } else {
                        this.sidebar.classList.remove('collapsed');
                        this.mainContent.classList.remove('collapsed');
                        this.toggleIcon.className = 'fas fa-chevron-left';
                    }
                } else {
                    // Tablet behavior - always collapsed
                    this.sidebar.classList.add('collapsed');
                    this.mainContent.classList.add('collapsed');
                    this.toggleIcon.className = 'fas fa-chevron-right';
                }
            }

            updateMobileState() {
                if (this.isMobileOpen) {
                    this.sidebar.classList.add('mobile-visible');
                    this.overlay.classList.add('show');
                    document.body.style.overflow = 'hidden';
                } else {
                    this.sidebar.classList.remove('mobile-visible');
                    this.overlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
            }

            handleResize() {
                if (window.innerWidth < 992) {
                    // Mobile - close sidebar
                    this.closeMobile();
                    this.mainContent.classList.add('expanded');
                } else {
                    this.mainContent.classList.remove('expanded');
                    this.updateLayout();
                }
            }
        }

        // ===== THEME MANAGER =====
        class ThemeManager {
            constructor() {
                this.currentTheme = localStorage.getItem('theme') || 'light';
                this.init();
            }

            init() {
                this.applyTheme();
                this.updateIcons();
            }

            toggle() {
                this.currentTheme = this.currentTheme === 'light' ? 'dark' : 'light';
                localStorage.setItem('theme', this.currentTheme);
                this.applyTheme();
                this.updateIcons();
            }

            applyTheme() {
                document.documentElement.setAttribute('data-bs-theme', this.currentTheme);
            }

            updateIcons() {
                const icons = ['theme-icon', 'theme-icon-sidebar', 'theme-icon-dropdown'];
                const text = document.getElementById('theme-text');

                icons.forEach(id => {
                    const icon = document.getElementById(id);
                    if (icon) {
                        icon.className = this.currentTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                    }
                });

                if (text) {
                    text.textContent = this.currentTheme === 'dark' ? 'Modo Claro' : 'Modo Escuro';
                }
            }
        }

        // ===== NOTIFICATION MANAGER =====
        class NotificationManager {
            static async markAsRead(notificationId, event) {
                event.preventDefault();
                const url = event.target.closest('a').href;

                try {
                    await fetch(`/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                        }
                    });

                    const badge = document.getElementById('notification-badge');
                    if (badge) {
                        const count = parseInt(badge.textContent) - 1;
                        if (count <= 0) {
                            badge.remove();
                        } else {
                            badge.textContent = count;
                        }
                    }

                    event.target.querySelector('.nav-badge')?.remove();
                    event.target.closest('.dropdown-item').classList.remove('bg-light');

                    if (url !== '#') window.location.href = url;
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                    if (url !== '#') window.location.href = url;
                }
            }

            static async markAllAsRead(event) {
                event.preventDefault();
                if (!confirm('Marcar todas as notificações como lidas?')) return;

                try {
                    await fetch('/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    document.getElementById('notification-badge')?.remove();
                    document.querySelectorAll('.dropdown-item.bg-light').forEach(item => {
                        item.classList.remove('bg-light');
                    });
                    document.querySelectorAll('.nav-badge.badge-primary').forEach(badge => badge.remove());

                    ToastManager.show('Todas as notificações foram marcadas como lidas', 'success');
                } catch (error) {
                    ToastManager.show('Erro ao marcar notificações como lidas', 'error');
                }
            }

            static async clearAll(event) {
                event.preventDefault();
                if (!confirm('Limpar todas as notificações? Esta ação não pode ser desfeita.')) return;

                try {
                    await fetch('/notifications/clear-all', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    document.querySelector('#notification-list').innerHTML = `
                        <li class="dropdown-item-text text-center text-muted py-3">
                            Nenhuma notificação
                        </li>
                    `;

                    ToastManager.show('Histórico de notificações limpo', 'success');
                } catch (error) {
                    ToastManager.show('Erro ao limpar notificações', 'error');
                }
            }
        }

        // ===== TOAST MANAGER =====
        class ToastManager {
            static show(message, type = 'success') {
                const container = document.getElementById('toast-container');
                if (!container) return;

                const iconMap = {
                    success: 'check-circle',
                    error: 'exclamation-circle',
                    warning: 'exclamation-triangle',
                    info: 'info-circle'
                };

                const colorMap = {
                    success: 'bg-success',
                    error: 'bg-danger',
                    warning: 'bg-warning',
                    info: 'bg-primary'
                };

                const toastId = 'toast-' + Date.now();
                const toastHtml = `
                    <div class="toast ${colorMap[type]} text-white fade-in" role="alert" id="${toastId}">
                        <div class="toast-body d-flex align-items-center">
                            <i class="fas fa-${iconMap[type]} me-2"></i>
                            <span class="flex-grow-1">${message}</span>
                            <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;

                container.insertAdjacentHTML('beforeend', toastHtml);

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
        }

        // ===== ADMIN FUNCTIONS =====
        function showSettings() {
            if (!{{ userCan('manage_settings') ? 'true' : 'false' }}) {
                ToastManager.show('Acesso negado às configurações', 'error');
                return;
            }
            ToastManager.show('Módulo de configurações em desenvolvimento', 'info');
        }

        function showBackupModal() {
            if (!{{ userCan('backup_system') ? 'true' : 'false' }}) {
                ToastManager.show('Acesso negado ao backup', 'error');
                return;
            }

            if (confirm('Gerar backup completo do sistema? Esta operação pode demorar alguns minutos.')) {
                ToastManager.show('Iniciando backup do sistema...', 'info');
            }
        }

        function showLogsModal() {
            if (!{{ userCan('view_logs') ? 'true' : 'false' }}) {
                ToastManager.show('Acesso negado aos logs', 'error');
                return;
            }

            ToastManager.show('Módulo de logs em desenvolvimento', 'info');
        }

        // ===== SEARCH MANAGER =====
        class SearchManager {
            constructor() {
                this.searchInput = document.querySelector('.search-input');
                this.init();
            }

            init() {
                if (!this.searchInput) return;

                this.searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        this.performSearch(e.target.value);
                    }
                });

                this.searchInput.addEventListener('input', (e) => {
                    if (e.target.value.length > 2) {
                        this.debounceSearch(e.target.value);
                    }
                });
            }

            debounceSearch(query) {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.performSearch(query);
                }, 500);
            }

            performSearch(query) {
                if (query.trim().length === 0) return;

                // Redirecionar para página de busca do Laravel
                window.location.href = `/search?q=${encodeURIComponent(query)}`;
            }
        }

        // ===== UTILITY FUNCTIONS =====
        function loadLogs() {
            const logType = document.getElementById('logType')?.value || 'all';
            const content = document.getElementById('logsContent');

            if (!content) return;

            // Fazer requisição AJAX para carregar logs reais
            fetch(`/admin/logs?type=${logType}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    let logsHtml = '<div class="list-group list-group-flush">';

                    if (data.logs && data.logs.length > 0) {
                        data.logs.forEach(log => {
                            const iconMap = {
                                info: 'info-circle text-primary',
                                success: 'check-circle text-success',
                                error: 'exclamation-circle text-danger',
                                warning: 'exclamation-triangle text-warning'
                            };

                            logsHtml += `
                            <div class="list-group-item">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-${iconMap[log.type] || 'info-circle text-primary'} mt-1 me-2"></i>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-medium">${log.message}</span>
                                            <small class="text-muted">${log.created_at}</small>
                                        </div>
                                        ${log.user ? `<small class="text-muted">Usuário: ${log.user}</small>` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                        });
                    } else {
                        logsHtml += '<div class="text-center py-4 text-muted">Nenhum log encontrado</div>';
                    }

                    logsHtml += '</div>';
                    content.innerHTML = logsHtml;
                })
                .catch(error => {
                    console.error('Erro ao carregar logs:', error);
                    content.innerHTML = '<div class="text-center py-4 text-danger">Erro ao carregar logs</div>';
                });
        }

        function refreshLogs() {
            loadLogs();
            ToastManager.show('Logs atualizados', 'success');
        }

        function clearLogs() {
            if (!confirm('Tem certeza que deseja limpar todos os logs? Esta ação não pode ser desfeita.')) {
                return;
            }

            fetch('/admin/logs/clear', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        ToastManager.show('Logs limpos com sucesso', 'success');
                        loadLogs();
                    } else {
                        ToastManager.show('Erro ao limpar logs', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    ToastManager.show('Erro ao limpar logs', 'error');
                });
        }

        function downloadLogs() {
            ToastManager.show('Preparando download dos logs...', 'info');

            // Criar link de download
            const link = document.createElement('a');
            link.href = '/admin/logs/download';
            link.download = `logs_${new Date().toISOString().split('T')[0]}.txt`;
            link.style.display = 'none';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            setTimeout(() => {
                ToastManager.show('Download iniciado', 'success');
            }, 500);
        }

        // ===== MODAL FUNCTIONS =====
        function showLogsModal() {
            if (!@json(userCan('view_logs'))) {
                ToastManager.show('Acesso negado aos logs', 'error');
                return;
            }

            const modalHtml = `
                <div class="modal fade" id="logsModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-file-alt me-2"></i>Logs do Sistema
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <select class="form-select" id="logType" style="width: auto;" onchange="loadLogs()">
                                        <option value="all">Todos os Logs</option>
                                        <option value="error">Erros</option>
                                        <option value="access">Acesso</option>
                                        <option value="sales">Vendas</option>
                                        <option value="system">Sistema</option>
                                    </select>
                                    <button class="btn btn-outline-primary btn-sm" onclick="refreshLogs()">
                                        <i class="fas fa-sync-alt"></i> Atualizar
                                    </button>
                                </div>
                                <div id="logsContent" style="max-height: 400px; overflow-y: auto;">
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-outline-danger" onclick="clearLogs()">
                                    <i class="fas fa-trash"></i> Limpar Logs
                                </button>
                                <button class="btn btn-primary" onclick="downloadLogs()">
                                    <i class="fas fa-download"></i> Download
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remover modal existente
            const existingModal = document.getElementById('logsModal');
            if (existingModal) existingModal.remove();

            // Adicionar modal ao DOM
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Mostrar modal e carregar logs
            const modal = new bootstrap.Modal(document.getElementById('logsModal'));
            modal.show();
            loadLogs();
        }

        // ===== GLOBAL FUNCTIONS =====
        function toggleSidebar() {
            window.sidebarManager.toggle();
        }

        function toggleMobileMenu() {
            window.sidebarManager.toggleMobile();
        }

        function toggleTheme() {
            window.themeManager.toggle();
        }

        function markAsRead(notificationId, event) {
            NotificationManager.markAsRead(notificationId, event);
        }

        function markAllAsRead(event) {
            NotificationManager.markAllAsRead(event);
        }

        function clearAllNotifications(event) {
            NotificationManager.clearAll(event);
        }

        // ===== INITIALIZATION =====
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar managers
            window.sidebarManager = new ModernSidebar();
            window.themeManager = new ThemeManager();
            window.searchManager = new SearchManager();

            // Auto-hide alerts após 5 segundos
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    if (bsAlert) bsAlert.close();
                });
            }, 5000);

            // Loading states para formulários
            document.addEventListener('submit', function(e) {
                const submitBtn = e.target.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                }
            });

            // Confirmar logout
            const logoutForms = document.querySelectorAll('form[action*="logout"]');
            logoutForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Tem certeza que deseja sair do sistema?')) {
                        e.preventDefault();
                    }
                });
            });

            // Inicializar tooltips para sidebar colapsada
            document.querySelectorAll('.nav-tooltip').forEach(tooltip => {
                new bootstrap.Tooltip(tooltip.parentElement, {
                    title: tooltip.textContent,
                    placement: 'right',
                    boundary: 'viewport'
                });
            });

            // Atualizar contadores em tempo real (se necessário)
            setInterval(() => {
                updateCounters();
            }, 30000); // A cada 30 segundos

            console.log('FDSMULTSERVICES+ Layout inicializado');
        });

        // ===== UTILITY FUNCTIONS =====
        function updateCounters() {
            // Atualizar contadores de notificações, pedidos pendentes, etc.
            fetch('/api/counters', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Atualizar badge de notificações
                    const notificationBadge = document.getElementById('notification-badge');
                    if (data.notifications > 0) {
                        if (notificationBadge) {
                            notificationBadge.textContent = data.notifications;
                        } else {
                            const btn = document.getElementById('notification-btn');
                            if (btn) {
                                btn.insertAdjacentHTML('beforeend', `
                                <span class="notification-badge" id="notification-badge">${data.notifications}</span>
                            `);
                            }
                        }
                    } else if (notificationBadge) {
                        notificationBadge.remove();
                    }

                    // Atualizar outros contadores conforme necessário
                    updateNavBadges(data);
                })
                .catch(error => {
                    console.error('Erro ao atualizar contadores:', error);
                });
        }

        function updateNavBadges(data) {
            // Atualizar badge de pedidos pendentes
            if (data.pending_orders !== undefined) {
                const ordersLink = document.querySelector('a[href*="orders"]');
                if (ordersLink) {
                    const existingBadge = ordersLink.querySelector('.nav-badge.badge-danger');
                    if (data.pending_orders > 0) {
                        if (existingBadge) {
                            existingBadge.textContent = data.pending_orders;
                        } else {
                            const navText = ordersLink.querySelector('.nav-text');
                            if (navText) {
                                navText.insertAdjacentHTML('afterend',
                                    `<span class="nav-badge badge-danger">${data.pending_orders}</span>`
                                );
                            }
                        }
                    } else if (existingBadge) {
                        existingBadge.remove();
                    }
                }
            }

            // Atualizar badge de dívidas em atraso
            if (data.overdue_debts !== undefined) {
                const debtsLink = document.querySelector('a[href*="debts"]');
                if (debtsLink) {
                    const existingBadge = debtsLink.querySelector('.nav-badge.badge-danger');
                    if (data.overdue_debts > 0) {
                        if (existingBadge) {
                            existingBadge.textContent = data.overdue_debts;
                        } else {
                            const navText = debtsLink.querySelector('.nav-text');
                            if (navText) {
                                navText.insertAdjacentHTML('afterend',
                                    `<span class="nav-badge badge-danger">${data.overdue_debts}</span>`
                                );
                            }
                        }
                    } else if (existingBadge) {
                        existingBadge.remove();
                    }
                }
            }
        }

        // ===== ERROR HANDLING =====
        window.addEventListener('error', function(e) {
            console.error('Erro JavaScript:', e.error);

            // Log de erro para o servidor (opcional)
            if (e.error && e.error.message) {
                fetch('/api/log-error', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        message: e.error.message,
                        stack: e.error.stack,
                        url: window.location.href,
                        user_agent: navigator.userAgent
                    })
                }).catch(() => {}); // Falha silenciosa no log de erro
            }
        });

        // ===== KEYBOARD SHORTCUTS =====
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + K para busca
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('.search-input');
                if (searchInput) {
                    searchInput.focus();
                }
            }

            // Ctrl/Cmd + Shift + N para nova venda
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'N') {
                e.preventDefault();
                window.location.href = @json(route('sales.create'));
            }

            // Esc para fechar modais
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal.show');
                if (openModal) {
                    const modal = bootstrap.Modal.getInstance(openModal);
                    if (modal) modal.hide();
                }
            }
        });

        // ===== UTILITIES =====
        window.Utils = {
            formatCurrency: function(value) {
                return new Intl.NumberFormat('pt-MZ', {
                    style: 'currency',
                    currency: 'MZN',
                    minimumFractionDigits: 2
                }).format(value || 0);
            },

            formatDate: function(date) {
                return new Intl.DateTimeFormat('pt-PT', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                }).format(new Date(date));
            },

            debounce: function(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            },

            copyToClipboard: async function(text) {
                try {
                    await navigator.clipboard.writeText(text);
                    ToastManager.show('Copiado para a área de transferência', 'success');
                } catch (err) {
                    console.error('Erro ao copiar:', err);
                    ToastManager.show('Erro ao copiar texto', 'error');
                }
            }
        };
    </script>

    @stack('scripts')
</body>
</html>
