<?php

namespace App\Http\Middleware;

use App\Models\UsuarioApiToken;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticateUsuarioToken
{
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $plainToken = $request->bearerToken();

        if (! $plainToken) {
            return response()->json([
                'message' => 'Token de acceso requerido.',
            ], 401);
        }

        $tokenHash = hash('sha256', $plainToken);

        $token = UsuarioApiToken::query()
            ->with('usuario')
            ->where('token_hash', $tokenHash)
            ->first();

        if (! $token || ! $token->isActive() || ! $token->usuario || ! $token->usuario->activo) {
            return response()->json([
                'message' => 'Token invalido o expirado.',
            ], 401);
        }

        $token->last_used_at = now();
        $token->save();

        $request->attributes->set('auth_usuario_token', $token);
        $request->setUserResolver(fn () => $token->usuario);

        return $next($request);
    }
}
