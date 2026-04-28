<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('usuario')) {
            return;
        }

        DB::statement('ALTER TABLE usuario ALTER COLUMN correo TYPE VARCHAR(150)');
        DB::statement('ALTER TABLE usuario ALTER COLUMN contrasena TYPE VARCHAR(255)');
    }

    public function down(): void
    {
        if (! Schema::hasTable('usuario')) {
            return;
        }

        DB::statement('ALTER TABLE usuario ALTER COLUMN correo TYPE VARCHAR(100)');
        DB::statement('ALTER TABLE usuario ALTER COLUMN contrasena TYPE VARCHAR(50)');
    }
};
