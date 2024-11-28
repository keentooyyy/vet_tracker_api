<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\PetTypeController;
use \App\Http\Controllers\PetMedicalRecordController;
use Illuminate\Support\Facades\Route;

Route::post('user/register', [UserController::class, 'register']);
Route::post('user/login', [UserController::class, 'login']);

Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
});


Route::middleware(['auth:sanctum', 'check.account.type:users'])->prefix('user')->group(function () {
    Route::get('/{id}', [UserController::class, 'getUser']);
    Route::post('/register_pet/{user_id}', [PetController::class, 'createPet']);
    Route::get('/get_pet_records/{user_id}/{pet_id}', [PetController::class, 'getPetMedicalRecords']);

});


Route::middleware(['auth:sanctum', 'check.account.type:vets'])->prefix('vets')->group(function () {
    Route::post('/create_new_pet_type', [PetTypeController::class, 'createPetType']);
    Route::post('/create_pet_medical_record/{pet_id}',[PetMedicalRecordController::class, 'createPetMedicalRecord']);
});
