<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });

    Route::middleware('auth.usuario-token')->group(function (): void {
        Route::prefix('auth')->group(function (): void {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });

        Route::patch('mi-cuenta/contrasena', [AuthController::class, 'updateMyPassword']);
    });

    Route::get('roles', [RolController::class, 'index']);

    Route::prefix('usuarios')->group(function (): void {
        Route::get('sugerir-username', [UsuarioController::class, 'suggestUsername']);
        Route::get('/', [UsuarioController::class, 'index']);
        Route::post('/', [UsuarioController::class, 'store']);
        Route::get('{usuario}', [UsuarioController::class, 'show']);
        Route::put('{usuario}', [UsuarioController::class, 'update']);
        Route::patch('{usuario}/contrasena', [UsuarioController::class, 'updatePassword']);
        Route::patch('{usuario}/reactivar', [UsuarioController::class, 'reactivar']);
        Route::put('{usuario}/permisos-modulo', [UsuarioController::class, 'syncPermisosModulo']);
        Route::get('{usuario}/acceso-modulo', [UsuarioController::class, 'accessCheck']);
        Route::delete('{usuario}', [UsuarioController::class, 'destroy']);
    });
});
