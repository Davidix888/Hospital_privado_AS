<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Hospital Privado AS</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f2f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-wrap {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
            padding: 40px;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-logo-icon {
            font-size: 40px;
            margin-bottom: 12px;
            display: block;
        }
        .login-logo h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
        }
        .login-logo p {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 9px;
            font-size: 14px;
            color: #1f2937;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            background: #fff;
        }
        .form-input:focus { border-color: #5b50d6; box-shadow: 0 0 0 3px rgba(91,80,214,.12); }
        .form-input.is-invalid { border-color: #ef4444; }
        .form-error { font-size: 12px; color: #ef4444; margin-top: 5px; }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #5b50d6;
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
            margin-top: 6px;
        }
        .btn-login:hover { background: #4a40c5; }

        .alert-danger {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
            border-radius: 9px;
            padding: 11px 14px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            border-radius: 9px;
            padding: 11px 14px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .login-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-logo">
            <span class="login-logo-icon">🏥</span>
            <h1>Hospital Privado AS</h1>
            <p>Ingresa tus credenciales para continuar</p>
        </div>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->has('correo'))
            <div class="alert-danger">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                {{ $errors->first('correo') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="correo">Correo electrónico</label>
                <input
                    id="correo"
                    name="correo"
                    type="email"
                    class="form-input {{ $errors->has('correo') ? 'is-invalid' : '' }}"
                    value="{{ old('correo') }}"
                    placeholder="ejemplo@hospital.com"
                    autofocus
                    autocomplete="email"
                >
            </div>

            <div class="form-group">
                <label class="form-label" for="contrasena">Contraseña</label>
                <input
                    id="contrasena"
                    name="contrasena"
                    type="password"
                    class="form-input"
                    placeholder="••••••••"
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="btn-login">Ingresar</button>
        </form>
    </div>

    <div class="login-footer">
        © {{ date('Y') }} Hospital Privado AS — Sistema interno
    </div>
</div>
</body>
</html>
