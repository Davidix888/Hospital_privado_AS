<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hospital Privado AS')</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: #f0f2f8;
            color: #1f2937;
            min-height: 100vh;
        }

        /* ── Layout ── */
        .layout { display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar {
            width: 230px;
            background: #fff;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
        }

        .sidebar-logo {
            padding: 22px 20px 18px;
            border-bottom: 1px solid #e5e7eb;
        }
        .sidebar-logo-title {
            font-size: 15px;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.2;
        }
        .sidebar-logo-sub {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 3px;
        }

        .sidebar-nav { padding: 12px 0; flex: 1; }

        .nav-section {
            padding: 8px 20px 4px;
            font-size: 10px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-top: 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: #6b7280;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all .15s;
        }
        .nav-item:hover { background: #f9fafb; color: #1f2937; }
        .nav-item.active { color: #5b50d6; background: #f3f2fe; border-left-color: #5b50d6; }
        .nav-item svg { flex-shrink: 0; }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid #e5e7eb;
        }

        /* ── Main ── */
        .main { margin-left: 230px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 0 28px;
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-left { font-size: 14px; color: #6b7280; }
        .topbar-left strong { color: #1f2937; }

        .topbar-right { display: flex; align-items: center; gap: 12px; }

        .role-badge {
            background: #f3f2fe;
            color: #5b50d6;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .user-name { font-size: 14px; font-weight: 600; color: #1f2937; }

        .btn-logout {
            background: none;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 13px;
            color: #6b7280;
            cursor: pointer;
            text-decoration: none;
            transition: all .15s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-logout:hover { background: #fee2e2; color: #ef4444; border-color: #fca5a5; }

        /* ── Content ── */
        .content { padding: 32px 28px; flex: 1; }

        /* ── Alerts ── */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-danger  { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }

        /* ── Cards ── */
        .card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            padding: 24px;
        }
        .card-header { margin-bottom: 20px; }
        .card-title {
            font-size: 17px;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card-subtitle { font-size: 13px; color: #6b7280; margin-top: 4px; }

        /* ── Page header ── */
        .page-header { margin-bottom: 24px; }
        .page-title  { font-size: 24px; font-weight: 700; color: #1f2937; }
        .page-sub    { font-size: 14px; color: #6b7280; margin-top: 4px; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all .15s;
            white-space: nowrap;
        }
        .btn-primary { background: #5b50d6; color: #fff; }
        .btn-primary:hover { background: #4a40c5; color: #fff; }
        .btn-dark    { background: #1a1a2e; color: #fff; }
        .btn-dark:hover { background: #0f0f20; color: #fff; }
        .btn-danger  { background: #fee2e2; color: #dc2626; }
        .btn-danger:hover { background: #fca5a5; color: #dc2626; }
        .btn-success { background: #d1fae5; color: #059669; }
        .btn-success:hover { background: #a7f3d0; color: #059669; }
        .btn-outline { background: #fff; color: #6b7280; border: 1px solid #e5e7eb; }
        .btn-outline:hover { background: #f9fafb; color: #1f2937; }
        .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 6px; }

        /* ── Badges ── */
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger  { background: #fee2e2; color: #dc2626; }
        .badge-info    { background: #dbeafe; color: #1e40af; }
        .badge-gray    { background: #f3f4f6; color: #6b7280; }
        .badge-purple  { background: #f3f2fe; color: #5b50d6; }

        /* ── Stats ── */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card  { background: #fff; border-radius: 14px; box-shadow: 0 1px 4px rgba(0,0,0,.06); padding: 20px 24px; }
        .stat-value { font-size: 34px; font-weight: 700; color: #1f2937; }
        .stat-label { font-size: 13px; color: #6b7280; margin-top: 4px; }

        /* ── Table ── */
        .table-container { overflow-x: auto; }
        table  { width: 100%; border-collapse: collapse; }
        th {
            text-align: left;
            padding: 10px 16px;
            font-size: 11px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: .06em;
            border-bottom: 1px solid #e5e7eb;
        }
        td { padding: 14px 16px; font-size: 14px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }

        /* ── Forms ── */
        .form-row   { display: grid; gap: 16px; }
        .form-row-2 { grid-template-columns: 1fr 1fr; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-label { font-size: 13px; font-weight: 600; color: #374151; }
        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            color: #1f2937;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            background: #fff;
        }
        .form-input:focus { border-color: #5b50d6; box-shadow: 0 0 0 3px rgba(91,80,214,.12); }
        .form-input.is-invalid { border-color: #ef4444; }
        .form-error { font-size: 12px; color: #ef4444; }
        .form-hint  { font-size: 12px; color: #9ca3af; }

        /* ── Helpers ── */
        .flex-between { display: flex; justify-content: space-between; align-items: center; }
        .flex-center  { display: flex; align-items: center; gap: 8px; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .mt-4  { margin-top: 16px; }
        .mt-6  { margin-top: 24px; }
        .mb-4  { margin-bottom: 16px; }
        .text-muted { color: #6b7280; }
        .text-sm    { font-size: 13px; }
        .text-xs    { font-size: 12px; }
        .fw-600     { font-weight: 600; }

        /* ── Pagination ── */
        .pagination { display: flex; gap: 4px; margin-top: 20px; justify-content: center; flex-wrap: wrap; }
        .pagination a, .pagination span {
            display: flex; align-items: center; justify-content: center;
            min-width: 36px; height: 36px; border-radius: 8px;
            font-size: 14px; text-decoration: none; color: #6b7280;
            border: 1px solid #e5e7eb; padding: 0 8px;
        }
        .pagination a:hover { background: #f9fafb; color: #1f2937; }
        .pagination .active { background: #5b50d6; color: #fff !important; border-color: #5b50d6; }
        .pagination .disabled { color: #d1d5db; pointer-events: none; }

        /* ── Search bar ── */
        .search-bar {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .search-bar .form-input { max-width: 280px; }

        /* ── Divider ── */
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 20px 0; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr; }
            .form-row-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="layout">

    {{-- Sidebar --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-title">🏥 Hospital Privado AS</div>
            <div class="sidebar-logo-sub">Sistema de Gestión</div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">General</div>

            <a href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Inicio
            </a>

            @if(strtolower($webUsuario->rol?->nombre_rol ?? '') === 'administracion')
            <div class="nav-section">Administración</div>
            <a href="{{ route('usuarios.index') }}"
               class="nav-item {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
                Usuarios
            </a>
            @endif

            <div class="nav-section">Mi Cuenta</div>
            <a href="{{ route('mi-cuenta') }}"
               class="nav-item {{ request()->routeIs('mi-cuenta*') ? 'active' : '' }}">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                </svg>
                Mi Cuenta
            </a>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout" style="width:100%; justify-content:center;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="main">
        <header class="topbar">
            <div class="topbar-left">
                Bienvenido, <strong>{{ $webUsuario->nombres }} {{ $webUsuario->apellidos }}</strong>
            </div>
            <div class="topbar-right">
                <span class="role-badge">{{ ucfirst($webUsuario->rol?->nombre_rol ?? 'Sin rol') }}</span>
                <span class="user-name text-sm text-muted">{{ $webUsuario->nombre_usuario }}</span>
            </div>
        </header>

        <main class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any() && !$errors->has('correo') && !$errors->has('contrasena_actual') && !$errors->has('nombres') && !$errors->has('apellidos') && !$errors->has('contrasena'))
                <div class="alert alert-danger">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</div>
</body>
</html>
