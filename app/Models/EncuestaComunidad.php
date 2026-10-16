<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncuestaComunidad extends Model
{
    protected $table = 'encuesta_comunidad';

    public $timestamps = false;

    protected $fillable = [
        'pregunta',
        'respuesta',
        'cantidad',
        'fecha_registro',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'fecha_registro' => 'date',
        ];
    }
}
