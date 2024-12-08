<?php

use App\Http\Controllers\PetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PetMedicalRecordController;
use App\Http\Controllers\VaccineTypeController;
use Illuminate\Support\Facades\Route;


Route::post('user/register', [UserController::class, 'register']);
Route::post('user/login', [UserController::class, 'login']);


//helper route for get pet types
Route::get('/get_pet_type', [PetController::class, 'getPetType']);

//helper method to get Vaccines
Route::get('/get_vaccines',[VaccineTypeController::class, 'getVaccineTypes']);


Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
    //get user
    Route::get('/{id}', [UserController::class, 'getUser']);

    //get user pets
    Route::get('{user_id}/pets', [PetController::class, 'getPets']);

    //edit pet by pet id and user id
    Route::patch('{user_id}/edit_pet/{pet_id}', [PetController::class, 'editPet']);

    //create appointment for user
    Route::post('/create_appointment/{user_id}',[AppointmentController::class, 'createAppointment']);

    //delete user pet
    Route::delete('{user_id}/pet/delete_pet/{pet_id}',[PetController::class, 'deletePet']);

    //get medical record for a pet
    Route::get('pet/get_records/{pet_id}',[PetMedicalRecordController::class, 'getRecords']);






    //helper create pet
    Route::post('/create_pet',[PetController::class, 'createPet']);

    //helper get pet by id
    Route::get('/get_pet/{pet_id}', [PetController::class, 'findPet']);

    //helper route for logout
    Route::post('/logout', [UserController::class, 'logout']);

});


//vet only Routes
Route::middleware(['auth:sanctum', 'check.account.type:vets'])->prefix('vets')->group(function () {
});
