<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\SyncUsuarioPermisosRequest;
use App\Http\Requests\UpdateUsuarioPasswordRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\UsuarioModuloPermiso;
use App\Services\UsernameGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    public function __construct(private readonly UsernameGenerator $usernameGenerator)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $query = Usuario::query()->with(['rol', 'permisosModulo']);

        if (! $request->boolean('incluir_inactivos', false)) {
            $query->where('activo', true);
        }

        if ($request->filled('correo')) {
            $query->where('correo', 'ilike', '%'.$request->string('correo')->value().'%');
        }

        if ($request->filled('nombre_usuario')) {
            $query->where('nombre_usuario', 'ilike', '%'.$request->string('nombre_usuario')->value().'%');
        }

        $usuarios = $query->orderBy('id_usuario', 'desc')->paginate(15);

        return response()->json($usuarios);
    }

    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        $data = $request->validated();
        $idRol = $this->resolveRoleId($data);
        $nombreUsuario = $this->usernameGenerator->fromNombreApellido($data['nombres'], $data['apellidos']);

        $usuario = Usuario::create([
            'nombres' => $data['nombres'] ?? null,
            'apellidos' => $data['apellidos'] ?? null,
            'nombre_usuario' => $nombreUsuario,
            'correo' => $data['correo'],
            'contrasena' => $data['contrasena'],
            'id_rol' => $idRol,
            'activo' => true,
            'password_changed_at' => now(),
        ]);

        $usuario->load(['rol', 'permisosModulo']);

        return response()->json([
            'message' => 'Usuario registrado correctamente.',
            'data' => $usuario,
            'seguridad' => $this->securityState($usuario),
        ], 201);
    }

    public function show(Usuario $usuario): JsonResponse
    {
        $usuario->load(['rol', 'permisosModulo']);

        return response()->json([
            'data' => $usuario,
            'seguridad' => $this->securityState($usuario),
        ]);
    }

    public function update(UpdateUsuarioRequest $request, Usuario $usuario): JsonResponse
    {
        $data = $request->validated();

        if (array_key_exists('nombre_usuario', $data)) {
            $data['nombre_usuario'] = $this->usernameGenerator->makeUnique($data['nombre_usuario']);
        }

        if (array_key_exists('id_rol', $data) || array_key_exists('rol', $data)) {
            $data['id_rol'] = $this->resolveRoleId($data);
        }

        $usuario->fill($data);
        $usuario->save();
        $usuario->load(['rol', 'permisosModulo']);

        return response()->json([
            'message' => 'Usuario actualizado correctamente.',
            'data' => $usuario,
            'seguridad' => $this->securityState($usuario),
        ]);
    }

    public function updatePassword(UpdateUsuarioPasswordRequest $request, Usuario $usuario): JsonResponse
    {
        $usuario->contrasena = $request->validated('contrasena');
        $usuario->password_changed_at = now();
        $usuario->save();

        return response()->json([
            'message' => 'Contrasena actualizada correctamente.',
            'seguridad' => $this->securityState($usuario),
        ]);
    }

    public function destroy(Usuario $usuario): JsonResponse
    {
        $usuario->activo = false;
        $usuario->save();

        return response()->json([
            'message' => 'Usuario inactivado correctamente.',
        ]);
    }

    public function reactivar(Usuario $usuario): JsonResponse
    {
        $usuario->activo = true;
        $usuario->save();

        return response()->json([
            'message' => 'Usuario reactivado correctamente.',
            'data' => $usuario,
            'seguridad' => $this->securityState($usuario),
        ]);
    }

    public function suggestUsername(Request $request): JsonResponse
    {
        if ($request->filled('nombres') && $request->filled('apellidos')) {
            return response()->json([
                'sugerencia' => $this->usernameGenerator->fromNombreApellido(
                    (string) $request->input('nombres'),
                    (string) $request->input('apellidos')
                ),
            ]);
        }

        $base = (string) ($request->input('base') ?: Str::before((string) $request->input('correo'), '@'));

        if ($base === '') {
            return response()->json([
                'message' => 'Debes enviar "nombres"+"apellidos", o "base", o "correo" para generar una sugerencia.',
            ], 422);
        }

        return response()->json([
            'sugerencia' => $this->usernameGenerator->makeUnique($base),
        ]);
    }

    public function syncPermisosModulo(SyncUsuarioPermisosRequest $request, Usuario $usuario): JsonResponse
    {
        $modulos = collect($request->validated('modulos'))
            ->map(fn ($modulo) => Str::lower((string) $modulo))
            ->unique()
            ->values();

        DB::transaction(function () use ($usuario, $modulos): void {
            UsuarioModuloPermiso::query()->where('id_usuario', $usuario->id_usuario)->delete();

            foreach ($modulos as $modulo) {
                UsuarioModuloPermiso::query()->create([
                    'id_usuario' => $usuario->id_usuario,
                    'modulo' => $modulo,
                ]);
            }
        });

        $usuario->load('permisosModulo');

        return response()->json([
            'message' => 'Permisos adicionales actualizados correctamente.',
            'data' => $usuario->permisosModulo,
        ]);
    }

    public function accessCheck(Request $request, Usuario $usuario): JsonResponse
    {
        $request->validate([
            'modulo' => ['required', 'string', 'in:administracion,farmacia,laboratorio,reportes'],
        ]);

        $allowed = $usuario->load('rol')->canAccessModule((string) $request->string('modulo'));

        return response()->json([
            'id_usuario' => $usuario->id_usuario,
            'modulo' => (string) $request->string('modulo'),
            'permitido' => $allowed,
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

    private function resolveRoleId(array $payload): int
    {
        if (! empty($payload['id_rol'])) {
            return (int) $payload['id_rol'];
        }

        if (! empty($payload['rol'])) {
            $rol = Rol::query()
                ->whereRaw('LOWER(nombre_rol) = ?', [Str::lower($payload['rol'])])
                ->first();

            if ($rol) {
                return (int) $rol->id_rol;
            }
        }

        $rolDefecto = Rol::query()
            ->whereRaw('LOWER(nombre_rol) = ?', [Rol::REPORTES])
            ->first();

        if ($rolDefecto) {
            return (int) $rolDefecto->id_rol;
        }

        abort(422, 'No existe el rol solicitado. Ejecuta "php artisan roles:sync" para crear los 4 roles base.');
    }
}
