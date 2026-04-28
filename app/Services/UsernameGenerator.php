<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Str;

class UsernameGenerator
{
    public function makeUnique(string $base): string
    {
        $normalized = Str::of($base)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '.')
            ->trim('.')
            ->value();

        if ($normalized === '') {
            $normalized = 'usuario';
        }

        $candidate = $normalized;
        $suffix = 1;

        while (Usuario::where('nombre_usuario', $candidate)->exists()) {
            $candidate = "{$normalized}.{$suffix}";
            $suffix++;
        }

        return $candidate;
    }

    public function fromCorreo(string $correo): string
    {
        $local = Str::before($correo, '@');

        return $this->makeUnique($local);
    }

    public function fromNombreApellido(string $nombres, string $apellidos): string
    {
        $nombresTokens = $this->splitTokens($nombres);
        $apellidosTokens = $this->splitTokens($apellidos);

        $inicial = $nombresTokens[0][0] ?? '';
        $apellidoPrincipal = $apellidosTokens[0] ?? '';

        $base = $inicial.$apellidoPrincipal;

        if ($base === '') {
            $base = implode('.', array_filter([$nombresTokens[0] ?? '', $apellidosTokens[0] ?? '']));
        }

        return $this->makeUnique($base);
    }

    private function splitTokens(string $value): array
    {
        $normalized = Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9\\s]+/', ' ')
            ->squish()
            ->value();

        if ($normalized === '') {
            return [];
        }

        return preg_split('/\\s+/', $normalized) ?: [];
    }
}
