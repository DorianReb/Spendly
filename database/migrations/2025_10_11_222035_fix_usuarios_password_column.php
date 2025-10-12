<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Asegurar columna destino
        if (!Schema::hasColumn('usuarios', 'password_hash')) {
            Schema::table('usuarios', function (Blueprint $table) {
                // la ubicamos tras email para mantener orden lógico
                $table->string('password_hash', 255)->after('email');
            });
        }

        // 2) Detectar la columna "vieja" cuyo nombre comienza con 'contra'
        //    (mojibake posible por 'ñ')
        $col = DB::selectOne("
            SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'usuarios'
              AND COLUMN_NAME LIKE 'contra%'
              AND COLUMN_NAME <> 'password_hash'
            LIMIT 1
        ");

        // 3) Si existe columna vieja, migrar datos y eliminarla
        if ($col && isset($col->COLUMN_NAME)) {
            $old = $col->COLUMN_NAME;

            // Copiar valores no nulos/no vacíos a password_hash
            DB::statement("
                UPDATE `usuarios`
                SET `password_hash` = `{$old}`
                WHERE (`{$old}` IS NOT NULL AND `{$old}` <> '')
                  AND (`password_hash` IS NULL OR `password_hash` = '')
            ");

            // Eliminar la columna vieja con nombre raro
            DB::statement("ALTER TABLE `usuarios` DROP COLUMN `{$old}`");
        }

        // 4) (Opcional) remember_token para "Recordarme"
        if (!Schema::hasColumn('usuarios', 'remember_token')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->rememberToken()->nullable()->after('password_hash');
            });
        }
    }

    public function down(): void
    {
        // Revertir lo mínimo: quitar remember_token y password_hash
        if (Schema::hasColumn('usuarios', 'remember_token')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('remember_token');
            });
        }

        if (Schema::hasColumn('usuarios', 'password_hash')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('password_hash');
            });
        }

        // Nota: No recreamos la columna con mojibake a propósito.
    }
};
