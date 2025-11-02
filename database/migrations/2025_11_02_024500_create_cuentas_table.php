<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Cuentas
        if (!Schema::hasTable('cuentas')) {
            Schema::create('cuentas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('usuario_id');
                $table->string('nombre', 80);
                $table->enum('tipo', ['efectivo','debito','credito','ahorro','inversion','otro'])->default('efectivo');
                $table->char('moneda', 3)->default('MXN');
                $table->decimal('saldo_inicial', 12, 2)->default(0);
                $table->char('color_hex', 7)->nullable()->default('#6c757d');
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('usuario_id')
                    ->references('id')->on('usuarios')
                    ->onDelete('cascade');
            });
        }

        // 2) Alter transacciones: agregar columnas + FKs
        if (Schema::hasTable('transacciones')) {
            Schema::table('transacciones', function (Blueprint $table) {
                if (!Schema::hasColumn('transacciones', 'cuenta_id')) {
                    $table->unsignedBigInteger('cuenta_id')->nullable()->after('usuario_id');
                }
                if (!Schema::hasColumn('transacciones', 'contracuenta_id')) {
                    $table->unsignedBigInteger('contracuenta_id')->nullable()->after('cuenta_id');
                }
            });

            Schema::table('transacciones', function (Blueprint $table) {
                // Si ejecutas esto en una BD limpia no habrá conflicto de nombres
                $table->foreign('cuenta_id', 'fk_transacciones_cuenta_id')->references('id')->on('cuentas');
                $table->foreign('contracuenta_id', 'fk_transacciones_contracuenta_id')->references('id')->on('cuentas');
            });

            // Opcional: CHECK de importe > 0 (según versión de MySQL/MariaDB)
            try {
                DB::statement("ALTER TABLE transacciones ADD CONSTRAINT chk_transacciones_importe_pos CHECK (importe > 0)");
            } catch (\Throwable $e) {
                // Ignorar si tu motor no soporta CHECK
            }
        }
    }

    public function down(): void
    {
        // Quitar FKs y columnas en transacciones si existen
        if (Schema::hasTable('transacciones')) {
            Schema::table('transacciones', function (Blueprint $table) {
                // Elimina CHECK si existiera (no todos los motores soportan DROP CONSTRAINT)
                try { DB::statement("ALTER TABLE transacciones DROP CONSTRAINT chk_transacciones_importe_pos"); } catch (\Throwable $e) {}
                // Quitar FKs con los nombres que pusimos arriba
                try { $table->dropForeign('fk_transacciones_contracuenta_id'); } catch (\Throwable $e) {}
                try { $table->dropForeign('fk_transacciones_cuenta_id'); } catch (\Throwable $e) {}
                if (Schema::hasColumn('transacciones', 'contracuenta_id')) {
                    $table->dropColumn('contracuenta_id');
                }
                if (Schema::hasColumn('transacciones', 'cuenta_id')) {
                    $table->dropColumn('cuenta_id');
                }
            });
        }

        Schema::dropIfExists('cuentas');
    }
};
