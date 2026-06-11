<?php

use App\Http\Controllers\MajaController;
use App\Http\Controllers\AutenController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function() {
    return response()->json(['message' => 'API is working!']);
});

Route::get('/kategorijas', [MajaController::class, 'kategorijas']);
Route::get('/sludinajumi', [MajaController::class, 'index']);
Route::get('/sludinajumi/{id}', [MajaController::class, 'show']);

Route::post('/register', [AutenController::class, 'register']);
Route::post('/login', [AutenController::class, 'login']);
Route::post('/logout', [AutenController::class, 'logout']);

Route::get('/favorites', [FavoriteController::class, 'index']);
Route::post('/favorites', [FavoriteController::class, 'store']);
Route::delete('/favorites/{id}', [FavoriteController::class, 'destroy']);
Route::get('/favorites/check/{id}', [FavoriteController::class, 'check']);


Route::post('/properties', [MajaController::class, 'store']);
Route::put('/properties/{id}', [MajaController::class, 'update']);
Route::delete('/properties/{id}', [MajaController::class, 'destroy']);
Route::post('/categories', [MajaController::class, 'storeCategory']);
Route::delete('/categories/{id}', [MajaController::class, 'destroyCategory']);