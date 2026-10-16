<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encuesta_comunidad', function (Blueprint $tabla): void {
            $tabla->id();
            $tabla->string('pregunta', 255);
            $tabla->string('respuesta', 255);
            $tabla->unsignedInteger('cantidad')->default(0);
            $tabla->date('fecha_registro');
            $tabla->index(['pregunta', 'fecha_registro']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encuesta_comunidad');
    }
};
