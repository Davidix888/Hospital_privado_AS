<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'correo' => ['required', 'email', 'max:150'],
            'contrasena' => ['required', 'string', 'confirmed', 'min:8', 'max:255', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'contrasena.regex' => 'La contrasena debe incluir mayuscula, minuscula, numero y caracter especial.',
        ];
    }
}
