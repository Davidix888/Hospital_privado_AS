@extends('layouts.app')

@section('title', 'Mi Cuenta — Hospital Privado AS')

@section('content')

<div class="page-header">
    <div class="page-title">Mi Cuenta</div>
    <div class="page-sub">Administra la información y seguridad de tu cuenta</div>
</div>

<div style="max-width:680px; display:flex; flex-direction:column; gap:20px;">

    {{-- Información personal --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                </svg>
                Información personal
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:12px;">
            <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #f3f4f6;">
                <span class="text-sm text-muted fw-600">Nombre completo</span>
                <span class="text-sm fw-600">{{ $usuario->nombres }} {{ $usuario->apellidos }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #f3f4f6;">
                <span class="text-sm text-muted fw-600">Usuario</span>
                <span class="text-sm fw-600">{{ $usuario->nombre_usuario }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #f3f4f6;">
                <span class="text-sm text-muted fw-600">Correo</span>
                <span class="text-sm fw-600">{{ $usuario->correo }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #f3f4f6;">
                <span class="text-sm text-muted fw-600">Rol</span>
                <span class="badge badge-purple">{{ ucfirst($usuario->rol?->nombre_rol ?? '—') }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:10px 0;">
                <span class="text-sm text-muted fw-600">Estado</span>
                @if($usuario->activo)
                    <span class="badge badge-success">Activo</span>
                @else
                    <span class="badge badge-danger">Inactivo</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Seguridad de contraseña --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                Seguridad — Cambiar contraseña
            </div>
            <div class="card-subtitle">
                @if($usuario->password_changed_at)
                    Última actualización: {{ $usuario->password_changed_at->format('d/m/Y H:i') }}
                    &nbsp;·&nbsp;
                    @if($usuario->passwordExpired(90))
                        <span style="color:#dc2626; font-weight:600;">Tu contraseña ha expirado</span>
                    @else
                        Vence: {{ $usuario->password_changed_at->addDays(90)->format('d/m/Y') }}
                    @endif
                @else
                    Nunca se ha cambiado
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom:16px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('mi-cuenta.password') }}">
            @csrf @method('PATCH')

            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label" for="contrasena_actual">Contraseña actual <span style="color:#ef4444">*</span></label>
                <input
                    id="contrasena_actual" name="contrasena_actual" type="password"
                    class="form-input {{ $errors->has('contrasena_actual') ? 'is-invalid' : '' }}"
                    placeholder="••••••••"
                    autocomplete="current-password"
                >
                @error('contrasena_actual') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-row form-row-2" style="margin-bottom:16px;">
                <div class="form-group">
                    <label class="form-label" for="contrasena">Nueva contraseña <span style="color:#ef4444">*</span></label>
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
                    <label class="form-label" for="contrasena_confirmation">Confirmar contraseña <span style="color:#ef4444">*</span></label>
                    <input
                        id="contrasena_confirmation" name="contrasena_confirmation" type="password"
                        class="form-input {{ $errors->has('contrasena_confirmation') ? 'is-invalid' : '' }}"
                        placeholder="••••••••"
                        autocomplete="new-password"
                    >
                    @error('contrasena_confirmation') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                Actualizar contraseña
            </button>
        </form>
    </div>

</div>

@endsection
