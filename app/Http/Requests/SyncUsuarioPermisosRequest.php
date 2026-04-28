<?php

namespace App\Http\Requests;

use App\Models\UsuarioModuloPermiso;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncUsuarioPermisosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modulos' => ['required', 'array'],
            'modulos.*' => ['required', 'string', Rule::in(UsuarioModuloPermiso::MODULOS_VALIDOS)],
        ];
    }
}
