<?php

use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\MiCuentaController;
use App\Http\Controllers\Web\UsuarioWebController;
use Illuminate\Support\Facades\Route;

/* Autenticación */
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->name('login.post');

/* Rutas protegidas */
Route::middleware('web.auth')->group(function () {

    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

    /* Dashboard */
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    /* Gestión de usuarios (solo administración) */
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/',              [UsuarioWebController::class, 'index'])->name('index');
        Route::get('/crear',         [UsuarioWebController::class, 'create'])->name('create');
        Route::post('/',             [UsuarioWebController::class, 'store'])->name('store');
        Route::get('/{id}/editar',   [UsuarioWebController::class, 'edit'])->name('edit');
        Route::put('/{id}',          [UsuarioWebController::class, 'update'])->name('update');
        Route::patch('/{id}/contrasena', [UsuarioWebController::class, 'updatePassword'])->name('password');
        Route::patch('/{id}/desactivar', [UsuarioWebController::class, 'desactivar'])->name('desactivar');
        Route::patch('/{id}/reactivar',  [UsuarioWebController::class, 'reactivar'])->name('reactivar');
    });

    /* Mi Cuenta */
    Route::get('/mi-cuenta',              [MiCuentaController::class, 'index'])->name('mi-cuenta');
    Route::patch('/mi-cuenta/contrasena', [MiCuentaController::class, 'updatePassword'])->name('mi-cuenta.password');
});
