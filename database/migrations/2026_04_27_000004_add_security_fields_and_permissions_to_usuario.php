<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('usuario')) {
            Schema::table('usuario', function (Blueprint $table): void {
                if (! Schema::hasColumn('usuario', 'activo')) {
                    $table->boolean('activo')->default(true);
                }

                if (! Schema::hasColumn('usuario', 'password_changed_at')) {
                    $table->timestamp('password_changed_at')->nullable();
                }
            });
        }

        if (! Schema::hasTable('usuario_modulo_permiso')) {
            Schema::create('usuario_modulo_permiso', function (Blueprint $table): void {
                $table->increments('id_usuario_modulo_permiso');
                $table->unsignedInteger('id_usuario');
                $table->string('modulo', 40);
                $table->timestamps();

                $table->foreign('id_usuario')
                    ->references('id_usuario')
                    ->on('usuario')
                    ->onDelete('cascade');

                $table->unique(['id_usuario', 'modulo'], 'usuario_modulo_permiso_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('usuario_modulo_permiso')) {
            Schema::dropIfExists('usuario_modulo_permiso');
        }

        if (Schema::hasTable('usuario')) {
            Schema::table('usuario', function (Blueprint $table): void {
                if (Schema::hasColumn('usuario', 'password_changed_at')) {
                    $table->dropColumn('password_changed_at');
                }

                if (Schema::hasColumn('usuario', 'activo')) {
                    $table->dropColumn('activo');
                }
            });
        }
    }
};
