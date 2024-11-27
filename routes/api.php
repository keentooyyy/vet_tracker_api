<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('user/register', [UserController::class, 'register']);
Route::post('user/login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    Route::get('/{id}', [UserController::class, 'getUser']);
    Route::post('/logout', [UserController::class, 'logout']);
});


