<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('usuario')) {
            return;
        }

        Schema::table('usuario', function (Blueprint $table): void {
            if (! Schema::hasColumn('usuario', 'nombres')) {
                $table->string('nombres', 120)->nullable()->after('id_usuario');
            }

            if (! Schema::hasColumn('usuario', 'apellidos')) {
                $table->string('apellidos', 120)->nullable()->after('nombres');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('usuario')) {
            return;
        }

        Schema::table('usuario', function (Blueprint $table): void {
            if (Schema::hasColumn('usuario', 'apellidos')) {
                $table->dropColumn('apellidos');
            }

            if (Schema::hasColumn('usuario', 'nombres')) {
                $table->dropColumn('nombres');
            }
        });
    }
};
