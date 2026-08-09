<?php

use App\Http\Controllers\Api\PetaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/kabkota', [PetaController::class, 'kabkota']);
Route::get('/kabkota/{id}/faskes', [PetaController::class, 'faskesByKabkota']);
Route::get('/kecamatan/{id}/faskes', [PetaController::class, 'faskesByKecamatan']);

Route::get('/kecamatan/cari', [PetaController::class, 'cariKecamatan']);

Route::get('/kabkota/{id}/markers', [PetaController::class, 'markersByKabkota']);

Route::get('/faskes/cari', [PetaController::class, 'cariFaskes']);