<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usuario = $this->route('usuario');
        $idUsuario = is_object($usuario) ? $usuario->id_usuario : $usuario;

        return [
            'nombres' => ['sometimes', 'nullable', 'string', 'max:120'],
            'apellidos' => ['sometimes', 'nullable', 'string', 'max:120'],
            'nombre_usuario' => [
                'sometimes',
                'nullable',
                'string',
                'min:4',
                'max:80',
                Rule::unique('usuario', 'nombre_usuario')->ignore($idUsuario, 'id_usuario'),
            ],
            'correo' => [
                'sometimes',
                'email',
                'max:150',
                Rule::unique('usuario', 'correo')->ignore($idUsuario, 'id_usuario'),
            ],
            'activo' => ['sometimes', 'boolean'],
            'id_rol' => ['sometimes', 'nullable', 'integer', 'exists:rol,id_rol'],
            'rol' => ['sometimes', 'nullable', 'string', 'in:administracion,farmacia,laboratorio,reportes'],
        ];
    }
}
