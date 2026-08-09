<?php

use App\Http\Controllers\PetaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('index');
});

Route::get('/peta', [PetaController::class, 'index']);