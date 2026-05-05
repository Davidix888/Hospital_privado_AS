@extends('layouts.app')

@section('title', 'Nuevo Usuario — Hospital Privado AS')

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
        <span class="text-sm fw-600">Nuevo usuario</span>
    </div>
    <div class="page-title" style="margin-top:8px;">Registrar Nuevo Usuario</div>
    <div class="page-sub">Completa los datos para crear la cuenta</div>
</div>

<div style="max-width:680px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                Datos del usuario
            </div>
        </div>

        <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf

            {{-- Nombres y apellidos --}}
            <div class="form-row form-row-2" style="margin-bottom:16px;">
                <div class="form-group">
                    <label class="form-label" for="nombres">Nombres <span style="color:#ef4444">*</span></label>
                    <input
                        id="nombres" name="nombres" type="text"
                        class="form-input {{ $errors->has('nombres') ? 'is-invalid' : '' }}"
                        value="{{ old('nombres') }}"
                        placeholder="Ej. Ana Patricia"
                        autofocus
                    >
                    @error('nombres') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="apellidos">Apellidos <span style="color:#ef4444">*</span></label>
                    <input
                        id="apellidos" name="apellidos" type="text"
                        class="form-input {{ $errors->has('apellidos') ? 'is-invalid' : '' }}"
                        value="{{ old('apellidos') }}"
                        placeholder="Ej. Morales Vega"
                    >
                    @error('apellidos') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Correo --}}
            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label" for="correo">Correo electrónico <span style="color:#ef4444">*</span></label>
                <input
                    id="correo" name="correo" type="email"
                    class="form-input {{ $errors->has('correo') ? 'is-invalid' : '' }}"
                    value="{{ old('correo') }}"
                    placeholder="usuario@hospital.com"
                >
                @error('correo') <span class="form-error">{{ $message }}</span> @enderror
                <span class="form-hint">El usuario podrá iniciar sesión con este correo.</span>
            </div>

            {{-- Rol --}}
            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label" for="id_rol">Rol <span style="color:#ef4444">*</span></label>
                @if($roles->isEmpty())
                    <div class="alert alert-danger" style="margin-bottom:0;">
                        No hay roles disponibles. Ejecuta <code>php artisan roles:sync</code> primero.
                    </div>
                @else
                    <select id="id_rol" name="id_rol" class="form-input {{ $errors->has('id_rol') ? 'is-invalid' : '' }}">
                        <option value="">— Selecciona un rol —</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id_rol }}" {{ old('id_rol') == $rol->id_rol ? 'selected' : '' }}>
                                {{ ucfirst($rol->nombre_rol) }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_rol') <span class="form-error">{{ $message }}</span> @enderror
                @endif
            </div>

            <hr class="divider">

            {{-- Contraseña --}}
            <div class="form-row form-row-2" style="margin-bottom:16px;">
                <div class="form-group">
                    <label class="form-label" for="contrasena">Contraseña <span style="color:#ef4444">*</span></label>
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

            {{-- Acciones --}}
            <div class="flex-center gap-3" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Crear usuario
                </button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@endsection
