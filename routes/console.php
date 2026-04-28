<?php

use App\Models\Rol;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('roles:sync', function () {
    $rolesActuales = Rol::query()->pluck('nombre_rol')->map(fn ($r) => Str::lower((string) $r))->all();
    $creados = 0;

    foreach (Rol::ROLES_BASE as $rolNombre) {
        if (! in_array($rolNombre, $rolesActuales, true)) {
            Rol::query()->create(['nombre_rol' => $rolNombre]);
            $creados++;
        }
    }

    $this->info("Roles sincronizados. Nuevos roles creados: {$creados}");
})->purpose('Crea los roles base: administracion, farmacia, laboratorio, reportes');
