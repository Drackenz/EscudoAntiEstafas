<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Analisis extends Model
{
    protected $table = 'analisis';

    public $timestamps = false;

    protected $fillable = [
        'hash_mensaje',
        'canal',
        'riesgo',
        'veredicto',
        'explicacion',
        'accion_recomendada',
        'metodo',
        'fecha_analisis',
    ];

    protected function casts(): array
    {
        return [
            'riesgo' => 'integer',
            'fecha_analisis' => 'datetime',
        ];
    }

    public function banderasDetectadas(): HasMany
    {
        return $this->hasMany(BanderaDetectada::class, 'analisis_id');
    }
}
