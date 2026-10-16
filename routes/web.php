<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalisisController;

Route::get('/', [AnalisisController::class, 'inicio'])->name('inicio');
Route::post('/analizar', [AnalisisController::class, 'validarEntrada'])->name('analisis.validar');
