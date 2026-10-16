<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analisis', function (Blueprint $tabla): void {
            $tabla->id();
            // Guardamos un hash para identificar el mensaje sin conservar su texto.
            $tabla->char('hash_mensaje', 64);
            $tabla->string('canal', 30);
            $tabla->unsignedTinyInteger('riesgo');
            $tabla->string('veredicto', 20);
            $tabla->text('explicacion');
            $tabla->text('accion_recomendada');
            $tabla->string('metodo', 20);
            $tabla->dateTime('fecha_analisis');
            $tabla->index('fecha_analisis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analisis');
    }
};
