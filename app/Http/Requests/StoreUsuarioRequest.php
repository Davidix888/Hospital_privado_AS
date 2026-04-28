<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'correo' => ['required', 'email', 'max:150', 'unique:usuario,correo'],
            'contrasena' => ['required', 'string', 'min:8', 'max:255', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).+$/'],
            'id_rol' => ['nullable', 'integer', 'exists:rol,id_rol'],
            'rol' => ['nullable', 'string', 'in:administracion,farmacia,laboratorio,reportes'],
        ];
    }

    public function messages(): array
    {
        return [
            'contrasena.regex' => 'La contrasena debe incluir mayuscula, minuscula, numero y caracter especial.',
        ];
    }
}
