<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BanderaDetectada extends Model
{
    protected $table = 'banderas_detectadas';

    public $timestamps = false;

    protected $fillable = [
        'analisis_id',
        'nombre',
    ];

    public function analisis(): BelongsTo
    {
        return $this->belongsTo(Analisis::class, 'analisis_id');
    }
}
