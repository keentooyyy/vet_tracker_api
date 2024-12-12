<?php

use App\Http\Controllers\PetController;
use App\Http\Controllers\VetController;
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

    //complete an Appointment
    Route::patch('/update_appointment/{appointmentId}',[AppointmentController::class, 'updateAppointmentStatus']);

    //show all user appointments
    Route::get('/show_user_appointments/{user_id}',[AppointmentController::class, 'showUserAppointments']);







    //helper create pet
    Route::post('/create_pet',[PetController::class, 'createPet']);

    //helper route for logout
    Route::post('/logout', [UserController::class, 'logout']);

});


//vet only Routes
Route::middleware(['auth:sanctum', 'check.account.type:vets'])->prefix('vets')->group(function () {

    //get all
    Route::get('/get_all_pet_user', [VetController::class, 'getAllPetsAndUsers']);

    //create vaccine
    Route::post('/create_new_vaccine', [VetController::class, 'createNewVaccine']);

    //delete vaccine
    Route::delete('/delete_vaccine/{vaccine_id}', [VetController::class, 'deleteVaccine']);

    //create new species
    Route::post('/create_species', [VetController::class, 'createSpecies']);

    //delete species
    Route::delete('/delete_species/{pet_type_id}', [VetController::class, 'deleteSpecies']);

    //show all appointments
    Route::get('/show_all_appointments',[AppointmentController::class, 'showAllAppointment']);

    //complete an Appointment
    Route::patch('/update_appointment/{appointmentId}',[AppointmentController::class, 'updateAppointmentStatus']);

    //create medical record for pet
    Route::post('/create_medical_record', [PetMedicalRecordController::class, 'createMedicalRecords']);

    //delete medical record for pet
    Route::delete('/delete_medical_record/{medical_record}', [PetMedicalRecordController::class, 'deleteMedicalRecord']);

    //update vaccination status of pet
    Route::patch('/update_vaccination/{pet_id}', [PetController::class, 'updateVaccinationStatus']);
});
