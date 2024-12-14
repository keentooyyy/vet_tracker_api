<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetMedicalRecord;
use App\Models\PetType;
use App\Models\User;
use App\Models\VaccineType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VetController extends Controller
{
    public function getAllPetsAndUsers(){
        $users = User::where('account_type', 'users')->with('pets')->get();
        return response()->json([
            'users' => $users
        ]);
    }
    public function createNewVaccine(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ]);
        }
        $input = $request->all();
        $vaccine = VaccineType::create($input);

        return response()->json([
           'vaccine' => $vaccine
        ]);
    }
    public function deleteVaccine(VaccineType $vaccine_id){
        $vaccine = VaccineType::get()->findorFail($vaccine_id);
        $vaccine->delete();
        return response()->json([
            'vaccine' => $vaccine
        ]);
    }

    public function createSpecies(Request $request){
        $validator = Validator::make($request->all(), [
           'type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()]);
        }
        $input = $request->all();
        $type = PetType::create($input);
        return response()->json([
           'type' => $type
        ]);
    }
    public function deleteSpecies(PetType $pet_type_id) {
        $pet_type = PetType::get()->findorFail($pet_type_id);

        $pet_type->delete();

        return response()->json([
            'type' => $pet_type
        ]);
    }

    public function totalStatistics()
    {
        $total_vaccinated = PetMedicalRecord::with('vaccine')
            ->whereHas('vaccine', function ($query) {
                $query->whereRaw('LOWER(name) != ?', ['general checkup']); // Exclude "General Check Up" (case-insensitive)
            })
            ->count();

        $total_checkups = PetMedicalRecord::with('vaccine')
            ->whereHas('vaccine', function ($query) {
                $query->whereRaw('LOWER(name) = ?', ['general checkup']); // Include "General Check Up" (case-insensitive)
            })
            ->count();

        $total_canine = Pet::with('petType')->whereHas('petType', function ($query) {
            $query->whereRaw('LOWER(type) = ?', ['canine']); // Exclude "canine" (case-insensitive)
        })
            ->count();

        $total_feline = Pet::with('petType')->whereHas('petType', function ($query) {
            $query->whereRaw('LOWER(type) = ?', ['feline']); // Include "feline" (case-insensitive)
        })
            ->count();

        $total_male = Pet::whereRaw('LOWER(gender) = ?', ['male'])->count();
        $total_female = Pet::whereRaw('LOWER(gender) = ?', ['female'])->count();
        $total_others = Pet::whereRaw('LOWER(gender) = ?', ['other species'])->count();


        return response()->json([
            'total_vaccinated' => $total_vaccinated,
            'total_checkups' => $total_checkups,
            'total_canine' => $total_canine,
            'total_feline' => $total_feline,
            'total_male' => $total_male,
            'total_female' => $total_female,
            'total_others' => $total_others,
        ]);
    }

}
