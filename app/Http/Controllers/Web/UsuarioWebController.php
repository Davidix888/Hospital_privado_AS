<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\Usuario;
use App\Services\UsernameGenerator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsuarioWebController extends Controller
{
    public function __construct(private readonly UsernameGenerator $usernameGenerator) {}

    private function checkAdmin(Request $request): Usuario
    {
        $usuario = $request->attributes->get('web_usuario');
        $rolNombre = strtolower($usuario->rol?->nombre_rol ?? '');

        if ($rolNombre !== Rol::ADMINISTRACION) {
            abort(403, 'Solo los administradores pueden acceder a esta sección.');
        }

        return $usuario;
    }

    public function index(Request $request)
    {
        $this->checkAdmin($request);

        $query = Usuario::with('rol');

        if (! $request->boolean('incluir_inactivos')) {
            $query->where('activo', true);
        }

        if ($request->filled('buscar')) {
            $buscar = strtolower($request->string('buscar')->value());
            $query->where(function ($q) use ($buscar) {
                $q->whereRaw('LOWER(correo) LIKE ?', ["%{$buscar}%"])
                  ->orWhereRaw('LOWER(nombres) LIKE ?', ["%{$buscar}%"])
                  ->orWhereRaw('LOWER(apellidos) LIKE ?', ["%{$buscar}%"])
                  ->orWhereRaw('LOWER(nombre_usuario) LIKE ?', ["%{$buscar}%"]);
            });
        }

        $usuarios = $query->orderBy('id_usuario', 'desc')->paginate(15)->withQueryString();
        $roles = Rol::all();

        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    public function create(Request $request)
    {
        $this->checkAdmin($request);
        $roles = Rol::all();

        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->checkAdmin($request);

        $data = $request->validate([
            'nombres'                 => 'required|string|max:120',
            'apellidos'               => 'required|string|max:120',
            'correo'                  => 'required|email|max:150|unique:usuario,correo',
            'contrasena'              => ['required', 'string', 'min:8', 'max:255', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'],
            'contrasena_confirmation' => 'required|same:contrasena',
            'id_rol'                  => 'required|exists:rol,id_rol',
        ], [
            'nombres.required'                 => 'El nombre es obligatorio.',
            'apellidos.required'               => 'Los apellidos son obligatorios.',
            'correo.required'                  => 'El correo es obligatorio.',
            'correo.unique'                    => 'Este correo ya está registrado.',
            'contrasena.required'              => 'La contraseña es obligatoria.',
            'contrasena.min'                   => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasena.regex'                 => 'Debe incluir mayúscula, minúscula, número y símbolo.',
            'contrasena_confirmation.required' => 'Confirma la contraseña.',
            'contrasena_confirmation.same'     => 'Las contraseñas no coinciden.',
            'id_rol.required'                  => 'Selecciona un rol.',
            'id_rol.exists'                    => 'El rol seleccionado no es válido.',
        ]);

        $nombreUsuario = $this->usernameGenerator->fromNombreApellido($data['nombres'], $data['apellidos']);

        Usuario::create([
            'nombres'             => $data['nombres'],
            'apellidos'           => $data['apellidos'],
            'nombre_usuario'      => $nombreUsuario,
            'correo'              => strtolower($data['correo']),
            'contrasena'          => $data['contrasena'],
            'id_rol'              => $data['id_rol'],
            'activo'              => true,
            'password_changed_at' => now(),
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(Request $request, int $id)
    {
        $this->checkAdmin($request);
        $usuario = Usuario::with('rol')->findOrFail($id);
        $roles = Rol::all();

        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, int $id)
    {
        $this->checkAdmin($request);
        $usuario = Usuario::findOrFail($id);

        $data = $request->validate([
            'nombres'   => 'required|string|max:120',
            'apellidos' => 'required|string|max:120',
            'correo'    => ['required', 'email', 'max:150', Rule::unique('usuario', 'correo')->ignore($usuario->id_usuario, 'id_usuario')],
            'id_rol'    => 'required|exists:rol,id_rol',
        ], [
            'nombres.required'   => 'El nombre es obligatorio.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'correo.required'    => 'El correo es obligatorio.',
            'correo.unique'      => 'Este correo ya está registrado por otro usuario.',
            'id_rol.required'    => 'Selecciona un rol.',
        ]);

        $usuario->fill($data);
        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function updatePassword(Request $request, int $id)
    {
        $this->checkAdmin($request);
        $usuario = Usuario::findOrFail($id);

        $data = $request->validate([
            'contrasena'              => ['required', 'string', 'min:8', 'max:255', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'],
            'contrasena_confirmation' => 'required|same:contrasena',
        ], [
            'contrasena.required'              => 'La contraseña es obligatoria.',
            'contrasena.min'                   => 'Mínimo 8 caracteres.',
            'contrasena.regex'                 => 'Debe incluir mayúscula, minúscula, número y símbolo.',
            'contrasena_confirmation.required' => 'Confirma la contraseña.',
            'contrasena_confirmation.same'     => 'Las contraseñas no coinciden.',
        ]);

        $usuario->contrasena          = $data['contrasena'];
        $usuario->password_changed_at = now();
        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', "Contraseña de {$usuario->nombres} actualizada.");
    }

    public function desactivar(Request $request, int $id)
    {
        $this->checkAdmin($request);
        $usuario = Usuario::findOrFail($id);
        $usuario->activo = false;
        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario desactivado correctamente.');
    }

    public function reactivar(Request $request, int $id)
    {
        $this->checkAdmin($request);
        $usuario = Usuario::findOrFail($id);
        $usuario->activo = true;
        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario reactivado correctamente.');
    }
}
