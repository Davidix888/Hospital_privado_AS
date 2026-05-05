@extends('layouts.app')

@section('title', 'Editar Usuario — Hospital Privado AS')

@section('content')

<div class="page-header">
    <div class="flex-center gap-2">
        <a href="{{ route('usuarios.index') }}" class="text-muted text-sm" style="text-decoration:none; display:flex; align-items:center; gap:4px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Usuarios
        </a>
        <span class="text-muted text-sm">/</span>
        <span class="text-sm fw-600">{{ $usuario->nombres }} {{ $usuario->apellidos }}</span>
    </div>
    <div class="page-title" style="margin-top:8px;">Editar Usuario</div>
    <div class="page-sub">Modifica los datos de la cuenta</div>
</div>

<div style="max-width:680px; display:flex; flex-direction:column; gap:20px;">

    {{-- Datos generales --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                Información general
            </div>
            <div class="card-subtitle">
                Usuario: <strong>{{ $usuario->nombre_usuario }}</strong> &nbsp;·&nbsp;
                @if($usuario->activo)
                    <span class="badge badge-success">Activo</span>
                @else
                    <span class="badge badge-danger">Inactivo</span>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('usuarios.update', $usuario->id_usuario) }}">
            @csrf @method('PUT')

            <div class="form-row form-row-2" style="margin-bottom:16px;">
                <div class="form-group">
                    <label class="form-label" for="nombres">Nombres <span style="color:#ef4444">*</span></label>
                    <input
                        id="nombres" name="nombres" type="text"
                        class="form-input {{ $errors->has('nombres') ? 'is-invalid' : '' }}"
                        value="{{ old('nombres', $usuario->nombres) }}"
                    >
                    @error('nombres') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="apellidos">Apellidos <span style="color:#ef4444">*</span></label>
                    <input
                        id="apellidos" name="apellidos" type="text"
                        class="form-input {{ $errors->has('apellidos') ? 'is-invalid' : '' }}"
                        value="{{ old('apellidos', $usuario->apellidos) }}"
                    >
                    @error('apellidos') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label" for="correo">Correo electrónico <span style="color:#ef4444">*</span></label>
                <input
                    id="correo" name="correo" type="email"
                    class="form-input {{ $errors->has('correo') ? 'is-invalid' : '' }}"
                    value="{{ old('correo', $usuario->correo) }}"
                >
                @error('correo') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label" for="id_rol">Rol <span style="color:#ef4444">*</span></label>
                @if($roles->isEmpty())
                    <div class="alert alert-danger" style="margin-bottom:0;">No hay roles disponibles.</div>
                @else
                    <select id="id_rol" name="id_rol" class="form-input {{ $errors->has('id_rol') ? 'is-invalid' : '' }}">
                        <option value="">— Selecciona un rol —</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id_rol }}"
                                {{ old('id_rol', $usuario->id_rol) == $rol->id_rol ? 'selected' : '' }}>
                                {{ ucfirst($rol->nombre_rol) }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_rol') <span class="form-error">{{ $message }}</span> @enderror
                @endif
            </div>

            <div class="flex-center gap-3">
                <button type="submit" class="btn btn-primary">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Guardar cambios
                </button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>

    {{-- Cambiar contraseña --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                Cambiar contraseña
            </div>
            <div class="card-subtitle">Establece una nueva contraseña para este usuario</div>
        </div>

        <form method="POST" action="{{ route('usuarios.password', $usuario->id_usuario) }}">
            @csrf @method('PATCH')

            <div class="form-row form-row-2" style="margin-bottom:16px;">
                <div class="form-group">
                    <label class="form-label" for="contrasena">Nueva contraseña</label>
                    <input
                        id="contrasena" name="contrasena" type="password"
                        class="form-input {{ $errors->has('contrasena') ? 'is-invalid' : '' }}"
                        placeholder="••••••••"
                        autocomplete="new-password"
                    >
                    @error('contrasena') <span class="form-error">{{ $message }}</span> @enderror
                    <span class="form-hint">Mín. 8 caracteres, mayúscula, minúscula, número y símbolo.</span>
                </div>
                <div class="form-group">
                    <label class="form-label" for="contrasena_confirmation">Confirmar contraseña</label>
                    <input
                        id="contrasena_confirmation" name="contrasena_confirmation" type="password"
                        class="form-input {{ $errors->has('contrasena_confirmation') ? 'is-invalid' : '' }}"
                        placeholder="••••••••"
                        autocomplete="new-password"
                    >
                    @error('contrasena_confirmation') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-dark">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                Actualizar contraseña
            </button>
        </form>
    </div>

    {{-- Activar / Desactivar --}}
    <div class="card" style="border: 1px solid {{ $usuario->activo ? '#fee2e2' : '#d1fae5' }};">
        <div class="card-title" style="margin-bottom:8px; color: {{ $usuario->activo ? '#dc2626' : '#059669' }};">
            @if($usuario->activo)
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Desactivar usuario
            @else
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Reactivar usuario
            @endif
        </div>
        <p class="text-sm text-muted" style="margin-bottom:14px;">
            @if($usuario->activo)
                El usuario no podrá iniciar sesión si lo desactivas.
            @else
                Permitir que este usuario vuelva a acceder al sistema.
            @endif
        </p>

        @if($usuario->activo)
            <form method="POST" action="{{ route('usuarios.desactivar', $usuario->id_usuario) }}"
                  onsubmit="return confirm('¿Confirmas desactivar a {{ addslashes($usuario->nombres) }} {{ addslashes($usuario->apellidos) }}?')">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-danger">Desactivar usuario</button>
            </form>
        @else
            <form method="POST" action="{{ route('usuarios.reactivar', $usuario->id_usuario) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-success">Reactivar usuario</button>
            </form>
        @endif
    </div>

</div>

@endsection
