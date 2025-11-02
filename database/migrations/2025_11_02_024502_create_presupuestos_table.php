<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('categoria_id')->nullable(); // null = presupuesto global
            $table->smallInteger('anio');
            $table->unsignedTinyInteger('mes'); // 1..12
            $table->decimal('limite', 12, 2);
            $table->boolean('carryover')->default(false);
            $table->timestamps();

            $table->unique(['usuario_id','categoria_id','anio','mes'], 'ux_presupuesto_periodo');

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('categoria_id')->references('id')->on('categorias');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
