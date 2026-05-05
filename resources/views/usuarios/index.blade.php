@extends('layouts.app')

@section('title', 'Usuarios — Hospital Privado AS')

@section('content')

<div class="flex-between page-header">
    <div>
        <div class="page-title">Gestión de Usuarios</div>
        <div class="page-sub">Administra los accesos al sistema</div>
    </div>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Nuevo usuario
    </a>
</div>

{{-- Buscador --}}
<div class="card" style="margin-bottom:20px;">
    <form method="GET" action="{{ route('usuarios.index') }}" class="search-bar" style="margin-bottom:0;">
        <div class="form-group" style="margin-bottom:0; flex:1;">
            <label class="form-label">Buscar</label>
            <input
                type="text"
                name="buscar"
                class="form-input"
                placeholder="Nombre, usuario o correo..."
                value="{{ request('buscar') }}"
                style="max-width:340px;"
            >
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Filtro</label>
            <div class="flex-center gap-2">
                <label class="flex-center gap-2 text-sm" style="cursor:pointer; padding:10px 14px; border:1px solid #e5e7eb; border-radius:8px; background:{{ request()->boolean('incluir_inactivos') ? '#f3f2fe' : '#fff' }};">
                    <input
                        type="checkbox"
                        name="incluir_inactivos"
                        value="1"
                        style="accent-color:#5b50d6;"
                        {{ request()->boolean('incluir_inactivos') ? 'checked' : '' }}
                        onchange="this.form.submit()"
                    >
                    Ver inactivos
                </label>
            </div>
        </div>
        <div class="form-group" style="margin-bottom:0; align-self:flex-end;">
            <button type="submit" class="btn btn-dark">Buscar</button>
        </div>
        @if(request()->hasAny(['buscar', 'incluir_inactivos']))
            <div class="form-group" style="margin-bottom:0; align-self:flex-end;">
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline">Limpiar</a>
            </div>
        @endif
    </form>
</div>

{{-- Tabla --}}
<div class="card">
    <div class="card-header flex-between" style="padding-bottom:16px; border-bottom:1px solid #f3f4f6;">
        <div>
            <div class="card-title">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
                Usuarios
            </div>
        </div>
        <span class="text-sm text-muted">{{ $usuarios->total() }} encontrado(s)</span>
    </div>

    @if($usuarios->isEmpty())
        <div style="text-align:center; padding:40px 0; color:#9ca3af;">
            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 12px; display:block; opacity:.5;">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
            </svg>
            <p class="fw-600" style="font-size:15px; margin-bottom:4px;">No se encontraron usuarios</p>
            <p class="text-sm">Prueba con otros filtros o crea un usuario nuevo.</p>
        </div>
    @else
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nombre completo</th>
                        <th>Usuario / Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th style="text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $u)
                    <tr>
                        <td>
                            <span class="fw-600">{{ $u->nombres }} {{ $u->apellidos }}</span>
                        </td>
                        <td>
                            <div class="text-sm fw-600">{{ $u->nombre_usuario }}</div>
                            <div class="text-xs text-muted">{{ $u->correo }}</div>
                        </td>
                        <td>
                            <span class="badge badge-purple">{{ ucfirst($u->rol?->nombre_rol ?? '—') }}</span>
                        </td>
                        <td>
                            @if($u->activo)
                                <span class="badge badge-success">Activo</span>
                            @else
                                <span class="badge badge-danger">Inactivo</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div class="flex-center" style="justify-content:flex-end; gap:6px; flex-wrap:wrap;">
                                <a href="{{ route('usuarios.edit', $u->id_usuario) }}" class="btn btn-outline btn-sm">
                                    Editar
                                </a>

                                @if($u->activo)
                                    <form method="POST" action="{{ route('usuarios.desactivar', $u->id_usuario) }}"
                                          onsubmit="return confirm('¿Desactivar a {{ addslashes($u->nombres) }}?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-danger btn-sm">Desactivar</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('usuarios.reactivar', $u->id_usuario) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm">Reactivar</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($usuarios->hasPages())
            <div class="pagination">
                {{-- Anterior --}}
                @if($usuarios->onFirstPage())
                    <span class="disabled">‹</span>
                @else
                    <a href="{{ $usuarios->previousPageUrl() }}">‹</a>
                @endif

                {{-- Números --}}
                @foreach($usuarios->getUrlRange(max(1, $usuarios->currentPage()-2), min($usuarios->lastPage(), $usuarios->currentPage()+2)) as $page => $url)
                    @if($page == $usuarios->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Siguiente --}}
                @if($usuarios->hasMorePages())
                    <a href="{{ $usuarios->nextPageUrl() }}">›</a>
                @else
                    <span class="disabled">›</span>
                @endif
            </div>
        @endif
    @endif
</div>

@endsection
