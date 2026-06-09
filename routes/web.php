<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/diagnostico/{token}', \App\Livewire\ResponderTamizaje::class)->name('tamizaje.publico');
