<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recurrencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->unsignedBigInteger('cuenta_id')->nullable();
            $table->enum('tipo', ['ingreso','gasto']);
            $table->decimal('importe', 12, 2);
            $table->string('regla', 120); // p.ej. "RRULE:FREQ=MONTHLY;BYMONTHDAY=1"
            $table->date('proximo');
            $table->string('nota', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('categoria_id')->references('id')->on('categorias');
            $table->foreign('cuenta_id')->references('id')->on('cuentas');
            $table->index(['usuario_id','proximo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurrencias');
    }
};
