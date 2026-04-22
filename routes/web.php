<?php

use App\Livewire\Agenda;
use App\Livewire\Ajustes;
use App\Livewire\Clientes;
use App\Livewire\Estadisticas;
use App\Livewire\Resumen;
use App\Livewire\Tratamientos;
use Illuminate\Support\Facades\Route;

Route::get('/manifest.json', function () {
    return response()->file(public_path('manifest.json'), [
        'Content-Type' => 'application/manifest+json',
    ]);
});

Route::get('/',              fn () => redirect('/agenda'));
Route::get('/agenda',        Agenda::class);
Route::get('/clientes',      Clientes::class);
Route::get('/tratamientos',  Tratamientos::class);
Route::get('/resumen',       Resumen::class);
Route::get('/estadisticas',  Estadisticas::class);
Route::get('/ajustes',       Ajustes::class);
