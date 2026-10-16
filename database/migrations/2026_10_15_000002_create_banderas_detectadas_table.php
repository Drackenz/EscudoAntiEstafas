<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banderas_detectadas', function (Blueprint $tabla): void {
            $tabla->id();
            $tabla->foreignId('analisis_id')->constrained('analisis')->cascadeOnDelete();
            // Guardamos el nombre de la bandera, no un fragmento del mensaje.
            $tabla->string('nombre', 100);
            $tabla->unique(['analisis_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banderas_detectadas');
    }
};
