<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\UpdateMyPasswordRequest;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $usuario = Usuario::query()
            ->whereRaw('LOWER(correo) = ?', [Str::lower($data['correo'])])
            ->first();

        if (! $usuario || ! $usuario->activo || ! $usuario->checkPassword($data['contrasena'])) {
            return response()->json([
                'message' => 'Credenciales invalidas.',
            ], 401);
        }

        $passwordExpirada = $usuario->passwordExpired(90);
        $abilities = $passwordExpirada ? ['password:update'] : ['*'];
        $tokenPayload = $usuario->issueApiToken($abilities);

        return response()->json([
            'message' => $passwordExpirada
                ? 'Inicio de sesion correcto. Debes cambiar la contrasena porque vencio el periodo de 90 dias.'
                : 'Inicio de sesion correcto.',
            'access_token' => $tokenPayload['plain_text_token'],
            'token_type' => 'Bearer',
            'abilities' => $tokenPayload['token']->abilities,
            'seguridad' => $this->securityState($usuario),
            'data' => [
                'id_usuario' => $usuario->id_usuario,
                'nombre_usuario' => $usuario->nombre_usuario,
                'correo' => $usuario->correo,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->attributes->get('auth_usuario_token');

        if ($token) {
            $token->revoked_at = now();
            $token->save();
        }

        return response()->json([
            'message' => 'Sesion cerrada correctamente.',
        ]);
    }

    public function updateMyPassword(UpdateMyPasswordRequest $request): JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        $token = $request->attributes->get('auth_usuario_token');

        if (! $usuario->checkPassword($request->validated('contrasena_actual'))) {
            return response()->json([
                'message' => 'La contrasena actual no es correcta.',
            ], 422);
        }

        if ($usuario->checkPassword($request->validated('contrasena_nueva'))) {
            return response()->json([
                'message' => 'La contrasena nueva no puede ser igual a la actual.',
            ], 422);
        }

        $usuario->contrasena = $request->validated('contrasena_nueva');
        $usuario->password_changed_at = now();
        $usuario->save();

        $usuario->revokeAllApiTokens();
        $newTokenPayload = $usuario->issueApiToken(['*']);

        return response()->json([
            'message' => 'Contrasena actualizada correctamente.',
            'access_token' => $newTokenPayload['plain_text_token'],
            'token_type' => 'Bearer',
            'abilities' => $newTokenPayload['token']->abilities,
            'seguridad' => $this->securityState($usuario),
            'token_anterior_revocado' => $token !== null,
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $correo = Str::lower($request->validated('correo'));

        $usuario = Usuario::query()
            ->whereRaw('LOWER(correo) = ?', [$correo])
            ->where('activo', true)
            ->first();

        if ($usuario) {
            $token = Password::broker('usuarios')->createToken($usuario);
            $usuario->sendPasswordResetNotification($token);
        }

        return response()->json([
            'message' => 'Si el correo existe en el sistema, se envio un enlace de recuperacion.',
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();

        $status = Password::broker('usuarios')->reset(
            [
                'correo' => $data['correo'],
                'token' => $data['token'],
                'password' => $data['contrasena'],
                'password_confirmation' => (string) $request->input('contrasena_confirmation'),
            ],
            function (Usuario $usuario, string $password): void {
                $usuario->contrasena = $password;
                $usuario->password_changed_at = now();
                $usuario->save();
                $usuario->revokeAllApiTokens();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => __($status),
            ], 422);
        }

        return response()->json([
            'message' => 'Contrasena restablecida correctamente.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $usuario->load(['rol', 'permisosModulo']);

        return response()->json([
            'data' => $usuario,
            'seguridad' => $this->securityState($usuario),
        ]);
    }

    private function securityState(Usuario $usuario): array
    {
        $changedAt = $usuario->password_changed_at;
        $expiresAt = $changedAt ? $changedAt->copy()->addDays(90) : null;

        return [
            'activo' => (bool) $usuario->activo,
            'password_changed_at' => $changedAt?->toIso8601String(),
            'password_expires_at' => $expiresAt?->toIso8601String(),
            'password_expirada' => $usuario->passwordExpired(90),
            'requiere_cambio_password' => $usuario->passwordExpired(90),
        ];
    }
}
