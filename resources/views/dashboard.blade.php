@extends('layouts.app')

@section('title', 'Inicio — Hospital Privado AS')

@section('content')

<div class="page-header">
    <div class="page-title">
        @if($rolNombre === 'administracion') Panel de Administración
        @elseif($rolNombre === 'farmacia') Panel de Farmacia
        @elseif($rolNombre === 'laboratorio') Panel de Laboratorio
        @elseif($rolNombre === 'reportes') Panel de Reportes
        @else Panel Principal
        @endif
    </div>
    <div class="page-sub">
        Bienvenido, {{ $usuario->nombres }} {{ $usuario->apellidos }}
    </div>
</div>

{{-- ── ADMIN ── --}}
@if($rolNombre === 'administracion')

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Usuarios registrados</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:#059669">{{ $stats['activos'] }}</div>
            <div class="stat-label">Usuarios activos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:#dc2626">{{ $stats['inactivos'] }}</div>
            <div class="stat-label">Usuarios inactivos</div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

        {{-- Últimos usuarios --}}
        <div class="card">
            <div class="card-header flex-between">
                <div>
                    <div class="card-title">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        </svg>
                        Usuarios Recientes
                    </div>
                    <div class="card-subtitle">Los últimos 5 usuarios registrados</div>
                </div>
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline btn-sm">Ver todos</a>
            </div>

            @if($stats['recientes']->isEmpty())
                <p class="text-muted text-sm" style="text-align:center; padding:20px 0;">No hay usuarios registrados aún.</p>
            @else
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Rol</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recientes'] as $u)
                            <tr>
                                <td>
                                    <div class="fw-600">{{ $u->nombres }} {{ $u->apellidos }}</div>
                                    <div class="text-xs text-muted">{{ $u->correo }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-purple">{{ ucfirst($u->rol?->nombre_rol ?? '—') }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('usuarios.edit', $u->id_usuario) }}" class="btn btn-outline btn-sm">Editar</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Acciones rápidas --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                    Acciones Rápidas
                </div>
                <div class="card-subtitle">Tareas frecuentes del sistema</div>
            </div>

            <div style="display:flex; flex-direction:column; gap:10px;">
                <a href="{{ route('usuarios.create') }}" class="btn btn-primary" style="justify-content:center;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Registrar nuevo usuario
                </a>
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline" style="justify-content:center;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                    Gestionar usuarios
                </a>
                <a href="{{ route('usuarios.index', ['incluir_inactivos' => 1]) }}" class="btn btn-outline" style="justify-content:center;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/>
                        <line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/>
                    </svg>
                    Ver usuarios inactivos
                </a>
                <a href="{{ route('mi-cuenta') }}" class="btn btn-outline" style="justify-content:center;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                    Cambiar mi contraseña
                </a>
            </div>
        </div>
    </div>

{{-- ── OTROS ROLES ── --}}
@else

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

        <div class="card">
            <div class="card-title" style="margin-bottom:10px;">
                @if($rolNombre === 'farmacia') 💊 Módulo de Farmacia
                @elseif($rolNombre === 'laboratorio') 🔬 Módulo de Laboratorio
                @elseif($rolNombre === 'reportes') 📊 Módulo de Reportes
                @else 📋 Mi Módulo
                @endif
            </div>
            <p class="text-muted text-sm" style="line-height:1.7;">
                Has iniciado sesión correctamente con el rol
                <strong>{{ ucfirst($rolNombre) }}</strong>.
                Usa el menú lateral para navegar por las secciones disponibles.
            </p>

            <hr class="divider">

            <div style="display:flex; flex-direction:column; gap:10px;">
                <a href="{{ route('mi-cuenta') }}" class="btn btn-primary" style="justify-content:center;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                    </svg>
                    Ver mi cuenta
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-title" style="margin-bottom:10px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                Información de sesión
            </div>
            <div style="display:flex; flex-direction:column; gap:10px; margin-top:8px;">
                <div class="flex-center" style="justify-content:space-between;">
                    <span class="text-sm text-muted">Usuario</span>
                    <span class="text-sm fw-600">{{ $usuario->nombre_usuario }}</span>
                </div>
                <div class="flex-center" style="justify-content:space-between;">
                    <span class="text-sm text-muted">Correo</span>
                    <span class="text-sm fw-600">{{ $usuario->correo }}</span>
                </div>
                <div class="flex-center" style="justify-content:space-between;">
                    <span class="text-sm text-muted">Rol</span>
                    <span class="badge badge-purple">{{ ucfirst($rolNombre) }}</span>
                </div>
                <div class="flex-center" style="justify-content:space-between;">
                    <span class="text-sm text-muted">Estado</span>
                    <span class="badge badge-success">Activo</span>
                </div>
            </div>
        </div>

    </div>
@endif

@endsection
