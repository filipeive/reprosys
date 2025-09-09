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
    <link
        href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: #4A5C7A;
            --sidebar-bg-dark: #3D4E68;
            --sidebar-text: #FFFFFF;
            --sidebar-text-muted: rgba(255, 255, 255, 0.7);
            --sidebar-active: #2C3E56;
            --sidebar-hover: rgba(255, 255, 255, 0.1);

            --primary-blue: #5B9BD5;
            --success-green: #28A745;
            --warning-orange: #FFA500;
            --danger-red: #DC3545;
            --info-blue: #17A2B8;

            --content-bg: #F8F9FA;
            --card-bg: #FFFFFF;
            --border-color: #E9ECEF;
            --text-primary: #212529;
            --text-secondary: #6C757D;
            --text-muted: #ADB5BD;

            --sidebar-width: 240px;
            --sidebar-collapsed-width: 60px;
            --header-height: 60px;

            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);

            --border-radius: 6px;
            --border-radius-lg: 10px;

            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-bs-theme="dark"] {
            --sidebar-bg: #2C3E56;
            --sidebar-bg-dark: #1A252F;
            --content-bg: #1A1D23;
            --card-bg: #2D3748;
            --border-color: #4A5568;
            --text-primary: #F7FAFC;
            --text-secondary: #CBD5E0;
            --text-muted: #718096;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--content-bg);
            color: var(--text-primary);
            line-height: 1.6;
            font-size: 14px;
            overflow-x: hidden;
        }

        /* ===== PROFESSIONAL SIDEBAR ===== */
        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, var(--sidebar-bg-dark) 100%);
            z-index: 1040;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow);
        }

        .app-sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .app-sidebar.mobile-hidden {
            transform: translateX(-100%);
        }

        /* Professional Sidebar Header */
        .sidebar-header {
            height: var(--header-height);
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--sidebar-bg-dark);
        }

        .sidebar.collapsed .toggle-icon {
            position: relative;
            z-index: 10;
        }

        .sidebar.collapsed .toggle-icon::before {
            content: "";
            position: absolute;
            top: -4px;
            left: -4px;
            width: 28px;
            height: 28px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            z-index: -1;
            /* fica por baixo do ícone */
        }


        .brand-container {
            display: flex;
            align-items: center;
            flex: 1;
            min-width: 0;
        }

        .brand-logo {
            width: 36px;
            height: 36px;
            background: linear-gradient(45deg, #5B9BD5, #4A90E2);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
            box-shadow: var(--shadow-sm);
        }

        .brand-logo i {
            color: white;
            font-size: 18px;
        }

        .brand-text {
            overflow: hidden;
            transition: var(--transition);
        }

        .app-sidebar.collapsed .brand-text {
            opacity: 0;
            width: 0;
        }

        .brand-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--sidebar-text);
            line-height: 1.2;
            margin: 0;
        }

        .brand-subtitle {
            font-size: 12px;
            color: var(--sidebar-text-muted);
            line-height: 1.2;
            font-weight: 500;
        }

        .sidebar-toggle {
            width: 32px;
            height: 32px;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--sidebar-text);
            cursor: pointer;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .sidebar-toggle,
        .mobile-menu-btn {
            cursor: pointer;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            color: var(--sidebar-text);
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover,
        .mobile-menu-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        .sidebar-toggle:active,
        .mobile-menu-btn:active {
            transform: scale(0.95);
        }

        /* Estados do sidebar */
        .app-sidebar.collapsed .nav-text,
        .app-sidebar.collapsed .nav-badge,
        .app-sidebar.collapsed .nav-section-title,
        .app-sidebar.collapsed .brand-text,
        .app-sidebar.collapsed .user-info,
        .app-sidebar.collapsed .logout-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .app-sidebar.collapsed .nav-icon {
            margin-right: 0;
        }

        .app-sidebar.collapsed .nav-link {
            justify-content: center;
            padding-left: 12px;
            padding-right: 12px;
        }

        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Professional Navigation */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 15px 0;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
        }

        .nav-section {
            margin-bottom: 25px;
        }

        .nav-section-title {
            padding: 0 20px 8px;
            font-size: 11px;
            font-weight: 600;
            color: var(--sidebar-text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: var(--transition);
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
            margin-bottom: 2px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: var(--transition);
            border-radius: 0;
            position: relative;
            min-height: 48px;
            font-weight: 500;
        }

        .app-sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 12px;
        }

        .nav-link:hover {
            background: var(--sidebar-hover);
            color: var(--sidebar-text);
            padding-left: 25px;
        }

        .app-sidebar.collapsed .nav-link:hover {
            padding: 12px;
            background: var(--sidebar-hover);
        }

        .nav-link.active {
            background: var(--sidebar-active);
            color: var(--sidebar-text);
            box-shadow: inset 3px 0 0 var(--primary-blue);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary-blue);
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
            opacity: 0.9;
        }

        .app-sidebar.collapsed .nav-icon {
            margin-right: 0;
        }

        .nav-text {
            flex: 1;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            transition: var(--transition);
        }

        .app-sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
        }

        .nav-badge {
            margin-left: auto;
            font-size: 10px;
            padding: 3px 7px;
            border-radius: 12px;
            font-weight: 600;
            transition: var(--transition);
            min-width: 20px;
            text-align: center;
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
            background: var(--success-green);
            color: white;
        }

        .badge-warning {
            background: var(--warning-orange);
            color: white;
        }

        .badge-danger {
            background: var(--danger-red);
            color: white;
        }

        .badge-secondary {
            background: var(--text-secondary);
            color: white;
        }

        /* Professional User Area */
        .user-area {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px;
            background: var(--sidebar-bg-dark);
        }

        .user-profile {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .app-sidebar.collapsed .user-profile {
            justify-content: center;
            margin-bottom: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary-blue), #4A90E2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 16px;
            font-weight: 700;
            flex-shrink: 0;
            box-shadow: var(--shadow-sm);
        }

        .app-sidebar.collapsed .user-avatar {
            margin-right: 0;
        }

        .user-info {
            flex: 1;
            min-width: 0;
            transition: var(--transition);
        }

        .app-sidebar.collapsed .user-info {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--sidebar-text);
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 12px;
            color: var(--sidebar-text-muted);
            line-height: 1.3;
            font-weight: 500;
        }

        .logout-btn {
            width: 100%;
            padding: 10px 15px;
            border: none;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border-radius: var(--border-radius);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .app-sidebar.collapsed .logout-btn {
            padding: 10px;
        }

        .logout-btn:hover {
            background: #dc3545;
            transform: translateY(-1px);
        }

        .logout-text {
            margin-left: 8px;
            transition: var(--transition);
        }

        .app-sidebar.collapsed .logout-text {
            opacity: 0;
            width: 0;
            margin-left: 0;
        }

        /* ===== PROFESSIONAL MAIN CONTENT ===== */
        .app-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            background: var(--content-bg);
        }

        .app-content.collapsed {
            margin-left: var(--sidebar-collapsed-width);
        }

        .app-content.expanded {
            margin-left: 0;
        }

        /* Professional Header */
        .app-header {
            height: var(--header-height);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: var(--shadow-sm);
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .mobile-menu-btn {
            width: 40px;
            height: 40px;
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            border-radius: var(--border-radius);
            display: none;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
        }

        .mobile-menu-btn:hover {
            background: var(--content-bg);
            color: var(--text-primary);
        }

        .page-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
        }

        .page-title i {
            margin-right: 10px;
            color: var(--primary-blue);
            font-size: 18px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-search {
            position: relative;
        }

        .search-input {
            width: 300px;
            padding: 8px 40px 8px 15px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-lg);
            font-size: 14px;
            background: var(--card-bg);
            color: var(--text-primary);
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(91, 155, 213, 0.1);
        }

        .search-input::placeholder {
            color: var(--text-muted);
        }

        .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
        }

        .header-btn {
            width: 40px;
            height: 40px;
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }

        .header-btn:hover {
            background: var(--content-bg);
            color: var(--text-primary);
            transform: translateY(-1px);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-red);
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Professional Content Area */
        .content-area {
            flex: 1;
            padding: 30px;
            background: var(--content-bg);
        }

        /* Professional Dashboard Cards */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-lg);
            padding: 25px;
            display: flex;
            align-items: center;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-blue);
        }

        .stat-card.success::before {
            background: var(--success-green);
        }

        .stat-card.warning::before {
            background: var(--warning-orange);
        }

        .stat-card.danger::before {
            background: var(--danger-red);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: var(--border-radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 24px;
            color: white;
        }

        .stat-icon.primary {
            background: linear-gradient(45deg, var(--primary-blue), #4A90E2);
        }

        .stat-icon.success {
            background: linear-gradient(45deg, var(--success-green), #22C55E);
        }

        .stat-icon.warning {
            background: linear-gradient(45deg, var(--warning-orange), #F59E0B);
        }

        .stat-icon.danger {
            background: linear-gradient(45deg, var(--danger-red), #EF4444);
        }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 5px;
            line-height: 1;
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 8px;
        }

        .stat-change {
            font-size: 12px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 12px;
            display: inline-block;
        }

        .stat-change.positive {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success-green);
        }

        .stat-change.negative {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger-red);
        }

        /* Professional Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow);
        }

        .card-header {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 20px 25px;
            font-weight: 600;
            font-size: 16px;
            color: var(--text-primary);
        }

        .card-body {
            padding: 25px;
        }

        /* Professional Buttons */
        .btn {
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition);
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--primary-blue);
            color: white;
        }

        .btn-primary:hover {
            background: #4A90E2;
            color: white;
        }

        .btn-success {
            background: var(--success-green);
            color: white;
        }

        .btn-success:hover {
            background: #22C55E;
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

        /* Professional Table */
        .table-container {
            background: var(--card-bg);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .table-header {
            background: var(--content-bg);
            padding: 20px 25px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background: var(--content-bg);
            color: var(--text-primary);
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
            padding: 15px 20px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 15px 20px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
        }

        .table tbody tr:hover {
            background: var(--content-bg);
        }

        /* Professional Alerts */
        .alert {
            border: none;
            border-radius: var(--border-radius);
            padding: 15px 20px;
            margin-bottom: 20px;
            border-left: 4px solid;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success-green);
            border-left-color: var(--success-green);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger-red);
            border-left-color: var(--danger-red);
        }

        .alert-warning {
            background: rgba(255, 165, 0, 0.1);
            color: var(--warning-orange);
            border-left-color: var(--warning-orange);
        }

        .alert-info {
            background: rgba(91, 155, 213, 0.1);
            color: var(--primary-blue);
            border-left-color: var(--primary-blue);
        }

        /* Professional Footer */
        footer {
            background: var(--card-bg);
            border-top: 1px solid var(--border-color);
            padding: 20px 0;
            margin-top: auto;
        }

        /* Responsive Design */
        @media (max-width: 1199.98px) {
            .app-content {
                margin-left: var(--sidebar-collapsed-width);
            }

            .app-sidebar:not(.mobile-visible) {
                width: var(--sidebar-collapsed-width);
            }

            .content-area {
                padding: 20px;
            }

            .mobile-menu-btn {
                display: flex !important;
            }

            .app-header {
                padding: 0 20px;
            }

            .app-sidebar:not(.mobile-visible) {
                width: var(--sidebar-collapsed-width);
                display: none !important;
            }

            .app-content.expanded {
                margin-left: 0 !important;
            }

            .search-input {
                position: absolute;
                width: 200px;
                right: 50%;
                top: 50%;
                transform: translateY(-50%);
            }

            .search-icon {
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: var(--text-muted);
                font-size: 14px;
            }

        }

        @media (max-width: 991.98px) {
            .app-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .app-sidebar.mobile-visible {
                transform: translateX(0);
            }

            .app-sidebar.mobile-hidden {
                transform: translateX(-100%);
            }

            .app-content.expanded {
                margin-left: 0 !important;
            }

            .mobile-menu-btn {
                display: flex !important;
            }

            .sidebar-overlay.show {
                opacity: 1;
                visibility: visible;
                pointer-events: all;
            }
        }

        @media (max-width: 767.98px) {
            .content-area {
                padding: 20px;
            }

            .app-header {
                padding: 0 20px;
            }

            .search-input {
                width: 180px !important;
                right: 40% !important;
            }

            .page-title {
                font-size: 12px;
                display: block !important;
            }

            .themet {
                display: none !important;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }

            .stat-value {
                font-size: 24px;
            }
        }

        @media (max-width: 479.98px) {
            .content-area {
                padding: 20px;
            }

            .app-header {
                padding: 0 20px;
            }

            .header-search {
                display: none !important;
            }

            .page-title {
                display: block !important;
                font-size: 16px;
            }

            .themet {
                display: none !important;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }

            .stat-value {
                font-size: 20px;
            }
        }

        /* Loading Animation */
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
            transition: var(--transition);
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Toast Styling */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            background: var(--card-bg);
            margin-bottom: 10px;
            min-width: 300px;
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Toast Container -->
    <div class="toast-container" id="toast-container"></div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Professional Sidebar -->
    <nav class="app-sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="brand-container">
                <div class="brand-logo">
                    <i class="fas fa-print m-0"></i>
                </div>
                <div class="brand-text">
                    <div class="brand-title" style="margin-left: -2px !important">FDS+</div>
                    <div class="brand-subtitle">MULTSERVICES</div>
                </div>
            </div>
            <button class="sidebar-toggle" type="button" onclick="toggleSidebar()">
                <i class="fas fa-chevron-left" id="toggle-icon"></i>
            </button>
        </div>

        <div class="sidebar-nav">
            <div class="nav-section">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-tachometer-alt"></i>
                            </span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>

                    @if (userCan('create_sales'))
                        <li class="nav-item">
                            <a href="{{ route('sales.create') }}"
                                class="nav-link {{ request()->routeIs('sales.create') ? 'active' : '' }}">
                                <span class="nav-icon">
                                    <i class="fas fa-cash-register"></i>
                                </span>
                                <span class="nav-text">Ponto de Venda</span>
                                <span class="nav-badge badge-success">PDV</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            @if (userCanAny(['view_products', 'manage_categories', 'view_stock_movements']))
                <div class="nav-section">
                    <div class="nav-section-title">Gestão de Produtos</div>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="{{ route('products.index') }}"
                                class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                <span class="nav-icon">
                                    <i class="fas fa-cube"></i>
                                </span>
                                <span class="nav-text">Produtos</span>
                                @if (userCan('create_products'))
                                    <span class="nav-badge badge-primary">Criar</span>
                                @elseif(userCan('edit_products'))
                                    <span class="nav-badge badge-secondary">Editar</span>
                                @else
                                    <span class="nav-badge badge-secondary">Ver</span>
                                @endif
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
                                </a>
                            </li>
                        @endif

                        @if (userCan('view_stock_movements'))
                            <li class="nav-item">
                                <a href="{{ route('stock-movements.index') }}"
                                    class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-boxes"></i>
                                    </span>
                                    <span class="nav-text">Controle de Estoque</span>
                                    @if (userCan('create_stock_movements'))
                                        <span class="nav-badge badge-primary">Gerir</span>
                                    @else
                                        <span class="nav-badge badge-secondary">Ver</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif

            @if (userCanAny(['view_sales', 'view_orders', 'create_orders', 'create_sales']))
                <div class="nav-section">
                    <div class="nav-section-title">Vendas e Pedidos</div>
                    <ul class="nav-list">
                        @if (userCanAny(['view_orders', 'create_orders']))
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
                                    @if (userCan('create_orders'))
                                        <span class="nav-badge badge-success">Criar</span>
                                    @endif
                                </a>
                            </li>
                        @endif

                        @if (userCan('view_sales'))
                            <li class="nav-item">
                                <a href="{{ route('sales.index') }}"
                                    class="nav-link {{ request()->routeIs('sales.*') && !request()->routeIs('sales.create') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </span>
                                    <span class="nav-text">Histórico de Vendas</span>
                                    @if (userCan('edit_sales'))
                                        <span class="nav-badge badge-success">Completo</span>
                                    @elseif(userCan('edit_own_sales'))
                                        <span class="nav-badge badge-secondary">Próprias</span>
                                    @else
                                        <span class="nav-badge badge-secondary">Ver</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif

            @if (userCanAny(['manage_debts', 'view_expenses', 'view_reports']))
                <div class="nav-section">
                    <div class="nav-section-title">Gestão Financeira</div>
                    <ul class="nav-list">
                        @if (userCanAny(['manage_debts', 'view_debts']))
                            <li class="nav-item">
                                <a href="{{ route('debts.index') }}"
                                    class="nav-link {{ request()->routeIs('debts.*') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </span>
                                    <span class="nav-text">Contas a Receber</span>
                                    @php
                                        $overdueDebts = \App\Models\Debt::where('status', 'overdue')->count();
                                    @endphp
                                    @if ($overdueDebts > 0)
                                        <span class="nav-badge badge-danger">{{ $overdueDebts }}</span>
                                    @endif
                                    @if (userCan('create_debts'))
                                        <span class="nav-badge badge-primary">Criar</span>
                                    @endif
                                </a>
                            </li>
                        @endif

                        @if (userCan('view_expenses'))
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
                                </a>
                            </li>
                        @endif

                        @if (userCanAny(['view_reports', 'export_reports']))
                            <li class="nav-item">
                                <a href="{{ route('reports.index') }}"
                                    class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </span>
                                    <span class="nav-text">Relatórios</span>
                                    @if (userCan('export_reports'))
                                        <span class="nav-badge badge-success">Export</span>
                                    @elseif(userCan('view_reports'))
                                        <span class="nav-badge badge-primary">Avançado</span>
                                    @elseif(userCan('view_basic_reports'))
                                        <span class="nav-badge badge-secondary">Básico</span>
                                    @else
                                        <span class="nav-badge badge-secondary">Ver</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif

            @if (userCanAny(['manage_users', 'manage_settings', 'backup_system', 'view_logs']))
                <div class="nav-section">
                    <div class="nav-section-title">Administração</div>
                    <ul class="nav-list">
                        @if (userCan('manage_users'))
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}"
                                    class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    <span class="nav-text">Usuários</span>
                                    <span class="nav-badge badge-danger">Admin</span>
                                </a>
                            </li>
                        @endif

                        @if (userCan('manage_settings'))
                            <li class="nav-item">
                                <a href="#" onclick="showSettings()" class="nav-link">
                                    <span class="nav-icon">
                                        <i class="fas fa-cog"></i>
                                    </span>
                                    <span class="nav-text">Configurações</span>
                                    <span class="nav-badge badge-warning">Config</span>
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
                                </a>
                            </li>
                        @endif

                        @if (userCan('view_logs'))
                            <li class="nav-item">
                                <a href="#" onclick="showLogsModal()" class="nav-link">
                                    <span class="nav-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </span>
                                    <span class="nav-text">Logs do Sistema</span>
                                    <span class="nav-badge badge-primary">Log</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif

            <div class="nav-section">
                <div class="nav-section-title">Configurações Pessoais</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}"
                            class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-user-cog"></i>
                            </span>
                            <span class="nav-text">Meu Perfil</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="user-area">
            <div class="user-profile">
                <div class="user-avatar">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ explode(' ', auth()->user()->name)[0] }}</div>
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

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="logout-text">Sair do Sistema</span>
                </button>
            </form>
        </div>
    </nav>

    <!-- Professional Main Content -->
    <div class="app-content" id="main-content">
        <!-- Professional Header -->
        <header class="app-header">
            <div class="header-left">
                <button class="mobile-menu-btn" type="button" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">
                    <i class="{{ $titleIcon ?? 'fas fa-tachometer-alt' }}"></i>
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>

            <div class="header-right">
                <div class="header-search">
                    <input type="text" class="search-input" placeholder="Pesquisar produtos, clientes, vendas...">
                    <i class="fas fa-search search-icon"></i>
                </div>

                <button class="header-btn" id="notification-btn" data-bs-toggle="dropdown" title="Notificações">
                    <i class="fas fa-bell"></i>
                    @if (auth()->user()->unreadNotifications->count() > 0)
                        <span class="notification-badge" id="notification-badge">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="width: 380px;" id="notification-list">
                    <li class="dropdown-header d-flex justify-content-between align-items-center p-3">
                        <strong>Notificações</strong>
                        <a href="#" class="text-decoration-none text-primary small"
                            onclick="markAllAsRead(event)">
                            Marcar todas como lidas
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider m-0">
                    </li>

                    @forelse(auth()->user()->notifications->take(8) as $notification)
                        <li>
                            <a class="dropdown-item d-flex align-items-start py-3 {{ $notification->read ? '' : 'bg-light' }}"
                                href="{{ $notification->action_url ?? '#' }}"
                                onclick="markAsRead({{ $notification->id }}, event)">
                                <div class="flex-shrink-0 me-3">
                                    <i
                                        class="{{ $notification->icon ?? 'fas fa-bell' }} text-{{ $notification->type === 'success' ? 'success' : ($notification->type === 'error' ? 'danger' : ($notification->type === 'warning' ? 'warning' : 'info')) }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold mb-1">{{ $notification->title }}</div>
                                    <div class="text-muted small mb-1">{{ $notification->message }}</div>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                @if (!$notification->read)
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-primary">NOVO</span>
                                    </div>
                                @endif
                            </a>
                        </li>
                    @empty
                        <li class="dropdown-item-text text-center text-muted py-4">
                            <i class="fas fa-bell-slash fs-3 mb-2 d-block"></i>
                            Nenhuma notificação
                        </li>
                    @endforelse

                    <li>
                        <hr class="dropdown-divider m-0">
                    </li>
                    <li class="text-center p-2">
                        <a href="#" class="small text-decoration-none text-muted"
                            onclick="clearAllNotifications(event)">
                            Limpar todas as notificações
                        </a>
                    </li>
                </ul>

                <button class="header-btn themet" onclick="toggleTheme()" title="Alternar Tema">
                    <i class="fas fa-moon" id="theme-icon"></i>
                </button>

                <div class="dropdown">
                    <button class="header-btn" data-bs-toggle="dropdown" title="Menu do Usuário">
                        <div class="user-avatar" style="width: 28px; height: 28px; font-size: 12px;">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li class="dropdown-header">
                            <strong>{{ explode(' ', auth()->user()->name)[0] }}</strong>
                            <small class="d-block text-muted">
                                @if (auth()->user()->role === 'admin')
                                    Administrador do Sistema
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
                                <i class="fas fa-user me-3"></i>Meu Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('sales.create') }}">
                                <i class="fas fa-plus me-3"></i>Nova Venda
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="toggleTheme()">
                                <i class="fas fa-moon me-3" id="theme-icon-dropdown"></i>
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
                                    <i class="fas fa-sign-out-alt me-3"></i>Sair do Sistema
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Professional Content -->
        <div class="content-area">
            <!-- Professional Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}" class="text-decoration-none">
                            <i class="fas fa-home"></i> Início
                        </a>
                    </li>
                    @yield('breadcrumbs')
                    @if (!View::hasSection('breadcrumbs'))
                        <li class="breadcrumb-item active">Dashboard</li>
                    @endif
                </ol>
            </nav>

            <!-- Professional Alerts -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Sucesso!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Erro!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atenção!</strong> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Informação!</strong> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <!-- Sistema de Toasts Moderno -->
            @include('partials.toasts')
            <!-- Page Content -->
            @yield('content')
        </div>

        <!-- Professional Footer -->
        <footer class="border-top bg-white">
            <div class="content-area">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center py-3">
                    <div class="text-center text-sm-start mb-2 mb-sm-0">
                        <small class="text-muted">
                            © {{ date('Y') }} <strong class="text-primary">FDSMULTSERVICES+</strong> - Sistema de
                            Reprografia e Serigrafia
                        </small>
                        <br class="d-block d-sm-none">
                        <small class="text-muted">
                            Desenvolvido por <strong>Eng. Filipe dos Santos</strong>
                        </small>
                    </div>
                    <div class="text-center text-sm-end">
                        <small class="text-muted">
                            <span class="badge bg-success me-2">v2.0.0</span>
                            <a href="http://163.192.7.41/" class="text-decoration-none me-2" target="_blank">Suporte
                                Técnico</a>
                            <a href="#" class="text-decoration-none" onclick="showHelp()">Manual do Usuário</a>
                        </small>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        // ===== VARIÁVEIS GLOBAIS =====
        let sidebarCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        let mobileMenuOpen = false;

        // ===== PROFESSIONAL SIDEBAR MANAGER =====
        class ProfessionalSidebar {
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

                if (this.sidebar && this.mainContent) {
                    this.init();
                }
            }

            init() {
                this.updateLayout();
                this.bindEvents();
                window.addEventListener('resize', () => this.handleResize());
            }

            bindEvents() {
                if (this.toggleBtn) {
                    this.toggleBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.toggle();
                    });
                }

                if (this.mobileToggle) {
                    this.mobileToggle.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.toggleMobile();
                    });
                }

                if (this.overlay) {
                    this.overlay.addEventListener('click', () => this.closeMobile());
                }

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
                if (!this.sidebar || !this.mainContent) return;

                if (window.innerWidth >= this.breakpoint) {
                    this.sidebar.classList.remove('mobile-visible');
                    if (this.overlay) this.overlay.classList.remove('show');

                    if (this.isCollapsed) {
                        this.sidebar.classList.add('collapsed');
                        this.mainContent.classList.add('collapsed');
                        if (this.toggleIcon) this.toggleIcon.className = 'fas fa-chevron-right';
                    } else {
                        this.sidebar.classList.remove('collapsed');
                        this.mainContent.classList.remove('collapsed');
                        if (this.toggleIcon) this.toggleIcon.className = 'fas fa-chevron-left';
                    }
                } else {
                    this.sidebar.classList.add('collapsed');
                    this.mainContent.classList.add('collapsed');
                    if (this.toggleIcon) this.toggleIcon.className = 'fas fa-chevron-right';
                }
            }

            updateMobileState() {
                if (!this.sidebar || !this.overlay) return;

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
                    this.closeMobile();
                    if (this.mainContent) this.mainContent.classList.add('expanded');
                } else {
                    if (this.mainContent) this.mainContent.classList.remove('expanded');
                    this.updateLayout();
                }
            }
        }

        // ===== PROFESSIONAL THEME MANAGER =====
        class ProfessionalTheme {
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
                const icons = ['theme-icon', 'theme-icon-dropdown'];
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

        // ===== PROFESSIONAL SEARCH MANAGER =====
        class ProfessionalSearch {
            constructor() {
                this.searchInput = document.querySelector('.search-input');
                this.searchTimeout = null;
                this.init();
            }

            init() {
                if (!this.searchInput) return;

                this.searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
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

                // Para Laravel, adapte a rota conforme necessário
                const searchUrl = window.location.origin + '/search?q=' + encodeURIComponent(query);
                window.location.href = searchUrl;
            }
        }

        // ===== PROFESSIONAL NOTIFICATION MANAGER =====
        class ProfessionalNotifications {
            static async markAsRead(notificationId, event) {
                if (event) event.preventDefault();

                const url = event && event.target.closest('a') ? event.target.closest('a').href : '#';

                try {
                    const response = await fetch(`/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const badge = document.getElementById('notification-badge');
                        if (badge) {
                            const count = parseInt(badge.textContent) - 1;
                            if (count <= 0) {
                                badge.remove();
                            } else {
                                badge.textContent = count;
                            }
                        }

                        // Remover indicador visual de não lido
                        if (event && event.target.closest('.dropdown-item')) {
                            event.target.closest('.dropdown-item').classList.remove('bg-light');
                            const newBadge = event.target.closest('.dropdown-item').querySelector('.badge.bg-primary');
                            if (newBadge) newBadge.remove();
                        }

                        if (url !== '#' && url !== 'javascript:void(0)') {
                            window.location.href = url;
                        }
                    }
                } catch (error) {
                    console.error('Erro ao marcar notificação como lida:', error);
                    if (url !== '#' && url !== 'javascript:void(0)') {
                        window.location.href = url;
                    }
                }
            }

            static async markAllAsRead(event) {
                if (event) event.preventDefault();

                if (!confirm('Marcar todas as notificações como lidas?')) return;

                try {
                    const response = await fetch('/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const badge = document.getElementById('notification-badge');
                        if (badge) badge.remove();

                        document.querySelectorAll('.dropdown-item.bg-light').forEach(item => {
                            item.classList.remove('bg-light');
                        });
                        document.querySelectorAll('.badge.bg-primary').forEach(badge => badge.remove());

                        ProfessionalToast.show('Todas as notificações foram marcadas como lidas', 'success');
                    } else {
                        throw new Error('Erro na resposta do servidor');
                    }
                } catch (error) {
                    console.error('Erro ao marcar notificações:', error);
                    ProfessionalToast.show('Erro ao marcar notificações como lidas', 'error');
                }
            }

            static async clearAll(event) {
                if (event) event.preventDefault();

                if (!confirm('Limpar todas as notificações? Esta ação não pode ser desfeita.')) return;

                try {
                    const response = await fetch('/notifications/clear-all', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const notificationList = document.querySelector('#notification-list');
                        if (notificationList) {
                            notificationList.innerHTML = `
                        <li class="dropdown-header d-flex justify-content-between align-items-center p-3">
                            <strong>Notificações</strong>
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li class="dropdown-item-text text-center text-muted py-4">
                            <i class="fas fa-bell-slash fs-3 mb-2 d-block"></i>
                            Nenhuma notificação
                        </li>
                    `;
                        }

                        const badge = document.getElementById('notification-badge');
                        if (badge) badge.remove();

                        ProfessionalToast.show('Histórico de notificações limpo', 'success');
                    } else {
                        throw new Error('Erro na resposta do servidor');
                    }
                } catch (error) {
                    console.error('Erro ao limpar notificações:', error);
                    ProfessionalToast.show('Erro ao limpar notificações', 'error');
                }
            }
        }

        // ===== PROFESSIONAL TOAST MANAGER =====
        class ProfessionalToast {
            static show(message, type = 'success') {
                const container = document.getElementById('toast-container');
                if (!container) {
                    console.warn('Container de toast não encontrado');
                    return;
                }

                const iconMap = {
                    success: 'check-circle',
                    error: 'exclamation-circle',
                    warning: 'exclamation-triangle',
                    info: 'info-circle'
                };

                const colorMap = {
                    success: 'text-bg-success',
                    error: 'text-bg-danger',
                    warning: 'text-bg-warning',
                    info: 'text-bg-primary'
                };

                const toastId = 'toast-' + Date.now();
                const toastHtml = `
            <div class="toast ${colorMap[type]}" role="alert" id="${toastId}" data-bs-autohide="true" data-bs-delay="${type === 'error' ? 8000 : 5000}">
                <div class="toast-body d-flex align-items-center">
                    <i class="fas fa-${iconMap[type]} me-2"></i>
                    <span class="flex-grow-1">${message}</span>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Fechar"></button>
                </div>
            </div>
        `;

                container.insertAdjacentHTML('beforeend', toastHtml);

                const toastElement = document.getElementById(toastId);
                if (toastElement && window.bootstrap) {
                    const toast = new bootstrap.Toast(toastElement);
                    toast.show();

                    toastElement.addEventListener('hidden.bs.toast', () => {
                        toastElement.remove();
                    });
                } else {
                    console.error('Bootstrap não encontrado ou elemento do toast inválido');
                }
            }
        }

        // ===== PROFESSIONAL ADMIN FUNCTIONS =====
        function showSettings() {
            const modalHtml = `
        <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="settingsModalLabel">
                            <i class="fas fa-cog me-2"></i>Configurações do Sistema
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-store me-2"></i>Configurações da Loja</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nome da Empresa</label>
                                            <input type="text" class="form-control" value="FDSMULTSERVICES+" id="companyName">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Endereço</label>
                                            <textarea class="form-control" rows="2" id="companyAddress">Maputo, Moçambique</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Telefone</label>
                                            <input type="text" class="form-control" placeholder="(+258) 84 123 4567" id="companyPhone">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Configurações do Sistema</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enableNotifications" checked>
                                                <label class="form-check-label" for="enableNotifications">
                                                    Notificações em Tempo Real
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enableAutoBackup">
                                                <label class="form-check-label" for="enableAutoBackup">
                                                    Backup Automático Diário
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Moeda Padrão</label>
                                            <select class="form-select" id="defaultCurrency">
                                                <option value="MZN" selected>Metical (MZN)</option>
                                                <option value="USD">Dólar Americano (USD)</option>
                                                <option value="EUR">Euro (EUR)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="saveSettings()">
                            <i class="fas fa-save me-2"></i>Salvar Configurações
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

            const existingModal = document.getElementById('settingsModal');
            if (existingModal) existingModal.remove();

            document.body.insertAdjacentHTML('beforeend', modalHtml);

            if (window.bootstrap) {
                const modal = new bootstrap.Modal(document.getElementById('settingsModal'));
                modal.show();
            }
        }

        function saveSettings() {
            const settings = {
                companyName: document.getElementById('companyName')?.value,
                companyAddress: document.getElementById('companyAddress')?.value,
                companyPhone: document.getElementById('companyPhone')?.value,
                enableNotifications: document.getElementById('enableNotifications')?.checked,
                enableAutoBackup: document.getElementById('enableAutoBackup')?.checked,
                defaultCurrency: document.getElementById('defaultCurrency')?.value
            };

            // Para Laravel, adapte a rota conforme necessário
            fetch('/admin/settings', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(settings)
                })
                .then(response => response.json())
                .then(data => {
                    ProfessionalToast.show('Configurações salvas com sucesso!', 'success');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('settingsModal'));
                    if (modal) modal.hide();
                })
                .catch(error => {
                    console.error('Erro ao salvar configurações:', error);
                    ProfessionalToast.show('Erro ao salvar configurações', 'error');
                });
        }

        // ===== UTILITY FUNCTIONS =====
        function updateCounters() {
            fetch('/api/dashboard/counters', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Erro na resposta');
                    return response.json();
                })
                .then(data => {
                    // Atualizar badge de notificações
                    const notificationBadge = document.getElementById('notification-badge');
                    if (data.notifications > 0) {
                        if (notificationBadge) {
                            notificationBadge.textContent = data.notifications;
                        } else {
                            const btn = document.getElementById('notification-btn');
                            if (btn) {
                                btn.insertAdjacentHTML('beforeend',
                                    `<span class="notification-badge" id="notification-badge">${data.notifications}</span>`
                                );
                            }
                        }
                    } else if (notificationBadge) {
                        notificationBadge.remove();
                    }

                    // Atualizar outros contadores se necessário
                    if (data.orders_pending) {
                        const ordersBadges = document.querySelectorAll('.nav-badge');
                        ordersBadges.forEach(badge => {
                            if (badge.closest('a[href*="orders"]')) {
                                badge.textContent = data.orders_pending;
                                badge.className = 'nav-badge badge-danger';
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro ao atualizar contadores:', error);
                });
        }

        // ===== GLOBAL FUNCTIONS =====
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleIcon = document.getElementById('toggle-icon');

            if (!sidebar || !mainContent) {
                console.error('Elementos do sidebar não encontrados');
                return;
            }

            // Verificar se estamos em mobile
            if (window.innerWidth < 992) {
                toggleMobileMenu();
                return;
            }

            sidebarCollapsed = !sidebarCollapsed;
            localStorage.setItem('sidebar-collapsed', sidebarCollapsed);

            if (sidebarCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('collapsed');
                if (toggleIcon) toggleIcon.className = 'fas fa-chevron-right';
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('collapsed');
                if (toggleIcon) toggleIcon.className = 'fas fa-chevron-left';
            }
        }

        // ===== FUNÇÃO DE TOGGLE MOBILE =====
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            if (!sidebar || !overlay) {
                console.error('Elementos mobile não encontrados');
                return;
            }

            mobileMenuOpen = !mobileMenuOpen;

            if (mobileMenuOpen) {
                sidebar.classList.add('mobile-visible');
                sidebar.classList.remove('mobile-hidden');
                overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.remove('mobile-visible');
                sidebar.classList.add('mobile-hidden');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        // ===== FUNÇÃO DE TOGGLE DE TEMA =====
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';

            document.documentElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            // Atualizar ícones
            const icons = document.querySelectorAll('#theme-icon, #theme-icon-dropdown');
            const text = document.getElementById('theme-text');

            icons.forEach(icon => {
                if (icon) {
                    icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                }
            });

            if (text) {
                text.textContent = newTheme === 'dark' ? 'Modo Claro' : 'Modo Escuro';
            }
        }

        function markAsRead(notificationId, event) {
            ProfessionalNotifications.markAsRead(notificationId, event);
        }

        function markAllAsRead(event) {
            ProfessionalNotifications.markAllAsRead(event);
        }

        function clearAllNotifications(event) {
            ProfessionalNotifications.clearAll(event);
        }

        // ===== GLOBAL UTILITIES =====
        window.ProfessionalUtils = {
            formatCurrency: function(value) {
                return new Intl.NumberFormat('pt-MZ', {
                    style: 'currency',
                    currency: 'MZN',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
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

            formatNumber: function(number) {
                return new Intl.NumberFormat('pt-PT').format(number || 0);
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
            }
        };

        // ===== PROFESSIONAL INITIALIZATION =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Inicializando FDSMULTSERVICES+...');

            // Inicializar classes principais
            const sidebar = new ProfessionalSidebar();
            const theme = new ProfessionalTheme();
            const search = new ProfessionalSearch();

            // Aplicar estado inicial do sidebar
            const sidebarElement = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleIcon = document.getElementById('toggle-icon');

            if (sidebarElement && mainContent) {
                if (window.innerWidth >= 992 && sidebarCollapsed) {
                    sidebarElement.classList.add('collapsed');
                    mainContent.classList.add('collapsed');
                    if (toggleIcon) {
                        toggleIcon.className = 'fas fa-chevron-right toggle-bg';
                    }
                }

                // Para mobile, sempre ocultar inicialmente
                if (window.innerWidth < 992) {
                    sidebarElement.classList.add('mobile-hidden');
                    mainContent.classList.add('expanded');
                }
            }

            // Event listener para redimensionamento
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    // Desktop: fechar mobile menu se estiver aberto
                    if (mobileMenuOpen) {
                        toggleMobileMenu();
                    }

                    // Remover classes mobile
                    if (sidebarElement) {
                        sidebarElement.classList.remove('mobile-visible', 'mobile-hidden');
                    }
                    if (mainContent) {
                        mainContent.classList.remove('expanded');
                    }
                } else {
                    // Mobile: garantir que não está expandido
                    if (sidebarElement) {
                        sidebarElement.classList.add('mobile-hidden');
                        sidebarElement.classList.remove('mobile-visible');
                    }
                    if (mainContent) {
                        mainContent.classList.add('expanded');
                    }
                    const overlay = document.getElementById('sidebar-overlay');
                    if (overlay) {
                        overlay.classList.remove('show');
                    }
                    document.body.style.overflow = '';
                    mobileMenuOpen = false;
                }
            });

            // Auto-hide alerts
            setTimeout(() => {
                document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
                    if (window.bootstrap && bootstrap.Alert) {
                        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                        if (bsAlert) bsAlert.close();
                    }
                });
            }, 8000);

            console.log('✅ Sistema inicializado com sucesso!');
        });

        // ===== ERROR HANDLING =====
        window.addEventListener('error', function(e) {
            console.error('Erro JavaScript:', e.error);

            // Log de erro crítico para o servidor (opcional)
            if (e.error && e.error.stack && navigator.onLine) {
                fetch('/api/log-js-error', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        message: e.error.message,
                        stack: e.error.stack,
                        url: window.location.href,
                        userAgent: navigator.userAgent,
                        timestamp: new Date().toISOString()
                    })
                }).catch(() => {}); // Falha silenciosa
            }
        });

        // API Global para uso em outros scripts
        window.FDSMULTSERVICES = {
            Toast: ProfessionalToast,
            Notifications: ProfessionalNotifications,
            Utils: window.ProfessionalUtils,
            toggleSidebar,
            toggleTheme,
            showSettings,
            version: '2.0.0'
        };
    </script>
    @stack('scripts')
</body>

</html>
