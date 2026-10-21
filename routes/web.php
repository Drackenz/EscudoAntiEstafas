<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalisisController;
use App\Http\Controllers\JuegoController;
use App\Http\Controllers\EncuestaComunidadController;

Route::get('/', [AnalisisController::class, 'inicio'])->name('inicio');
Route::post('/analizar', [AnalisisController::class, 'validarEntrada'])->name('analisis.validar');
Route::get('/juego', [JuegoController::class, 'inicio'])->name('juego.inicio');
Route::get('/comunidad', [EncuestaComunidadController::class, 'inicio'])->name('comunidad.inicio');
