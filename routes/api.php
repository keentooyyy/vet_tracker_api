<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\PetTypeController;
use Illuminate\Support\Facades\Route;

Route::post('user/register', [UserController::class, 'register']);
Route::post('user/login', [UserController::class, 'login']);

Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
});


Route::middleware(['auth:sanctum', 'check.account.type:users'])->prefix('user')->group(function () {
    Route::get('/{id}', [UserController::class, 'getUser']);

});


Route::middleware(['auth:sanctum', 'check.account.type:vets'])->prefix('vets')->group(function () {
    Route::post('/pet_type', [PetTypeController::class, 'createPetType']);
});
