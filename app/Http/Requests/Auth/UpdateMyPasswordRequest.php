<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMyPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contrasena_actual' => ['required', 'string', 'max:255'],
            'contrasena_nueva' => ['required', 'string', 'confirmed', 'min:8', 'max:255', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'contrasena_nueva.regex' => 'La contrasena nueva debe incluir mayuscula, minuscula, numero y caracter especial.',
        ];
    }
}
