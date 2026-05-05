<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;

class AuthWebController extends Controller
{
    public function showLogin()
    {
        if (session('web_usuario_id')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo'    => 'required|email',
            'contrasena' => 'required|string',
        ], [
            'correo.required'    => 'El correo es obligatorio.',
            'correo.email'       => 'Ingresa un correo válido.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ]);

        $usuario = Usuario::with('rol')
            ->whereRaw('LOWER(correo) = ?', [strtolower($request->correo)])
            ->first();

        if (! $usuario || ! $usuario->checkPassword($request->contrasena)) {
            return back()
                ->withErrors(['correo' => 'Correo o contraseña incorrectos.'])
                ->withInput(['correo' => $request->correo]);
        }

        if (! $usuario->activo) {
            return back()
                ->withErrors(['correo' => 'Tu cuenta está inactiva. Contacta al administrador.'])
                ->withInput(['correo' => $request->correo]);
        }

        session(['web_usuario_id' => $usuario->id_usuario]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        session()->forget('web_usuario_id');

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}
