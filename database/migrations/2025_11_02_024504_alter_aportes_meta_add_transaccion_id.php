<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('aportes_meta') && !Schema::hasColumn('aportes_meta', 'transaccion_id')) {
            Schema::table('aportes_meta', function (Blueprint $table) {
                $table->unsignedBigInteger('transaccion_id')->nullable()->after('usuario_id');
            });

            Schema::table('aportes_meta', function (Blueprint $table) {
                $table->foreign('transaccion_id')->references('id')->on('transacciones');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('aportes_meta') && Schema::hasColumn('aportes_meta', 'transaccion_id')) {
            Schema::table('aportes_meta', function (Blueprint $table) {
                $table->dropForeign(['transaccion_id']);
                $table->dropColumn('transaccion_id');
            });
        }
    }
};
