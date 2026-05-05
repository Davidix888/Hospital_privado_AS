<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MiCuentaController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->attributes->get('web_usuario');

        return view('mi-cuenta.index', compact('usuario'));
    }

    public function updatePassword(Request $request)
    {
        $usuario = $request->attributes->get('web_usuario');

        $data = $request->validate([
            'contrasena_actual'       => 'required|string',
            'contrasena'              => ['required', 'string', 'min:8', 'max:255', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'],
            'contrasena_confirmation' => 'required|same:contrasena',
        ], [
            'contrasena_actual.required'       => 'Ingresa tu contraseña actual.',
            'contrasena.required'              => 'La nueva contraseña es obligatoria.',
            'contrasena.min'                   => 'Mínimo 8 caracteres.',
            'contrasena.regex'                 => 'Debe incluir mayúscula, minúscula, número y símbolo.',
            'contrasena_confirmation.required' => 'Confirma la nueva contraseña.',
            'contrasena_confirmation.same'     => 'Las contraseñas no coinciden.',
        ]);

        if (! $usuario->checkPassword($data['contrasena_actual'])) {
            return back()->withErrors(['contrasena_actual' => 'La contraseña actual es incorrecta.']);
        }

        $usuario->contrasena          = $data['contrasena'];
        $usuario->password_changed_at = now();
        $usuario->save();

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
