<?php

namespace App\Http\Middleware;

use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;

class WebAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        $usuarioId = session('web_usuario_id');

        if (! $usuarioId) {
            return redirect()->route('login');
        }

        $usuario = Usuario::with('rol')->find($usuarioId);

        if (! $usuario || ! $usuario->activo) {
            session()->forget('web_usuario_id');
            return redirect()->route('login')->withErrors(['correo' => 'Tu sesión ha expirado o la cuenta fue desactivada.']);
        }

        $request->attributes->set('web_usuario', $usuario);
        view()->share('webUsuario', $usuario);

        return $next($request);
    }
}
