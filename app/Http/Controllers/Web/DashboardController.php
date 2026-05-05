<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->attributes->get('web_usuario');
        $rolNombre = strtolower($usuario->rol?->nombre_rol ?? '');
        $stats = [];

        if ($rolNombre === Rol::ADMINISTRACION) {
            $stats = [
                'total'     => Usuario::count(),
                'activos'   => Usuario::where('activo', true)->count(),
                'inactivos' => Usuario::where('activo', false)->count(),
                'recientes' => Usuario::with('rol')
                    ->where('activo', true)
                    ->orderBy('id_usuario', 'desc')
                    ->limit(5)
                    ->get(),
            ];
        }

        return view('dashboard', compact('usuario', 'stats', 'rolNombre'));
    }
}
