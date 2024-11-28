<?php

namespace App\Http\Controllers;

use App\Http\Resources\PetMedicalRecordResource;
use App\Models\Pet;
use App\Models\PetMedicalRecord;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PetController extends Controller
{
    //
    public function createPet(User $user_id){
        $currentUser = Auth::user();
        $checkUser = User::get()->findorFail($user_id);

        if ($currentUser->id === $checkUser->id) {

            request()->merge(['user_id' => $currentUser->id]);
            $validator = Validator::make(request()->all(),[
                'pet_type_id' => 'required|exists:pet_types,id',
                'user_id' => 'required|in:'. $currentUser->id,
                'name' => 'required|string',
                'breed' => 'required|string',
                'birthdate' => 'required|date|date_format:Y-m-d',
            ]);

            if($validator->fails()){
                return response()->json([
                    $validator->errors()
                ]);
            }

            $input = request()->all();
            $pet = Pet::create($input);
            return response()->json([
                "pet"=>$pet
            ]);
        }


        return response()->json([
            'Unauthorized'
        ]);

    }
    public function getPetMedicalRecords(User $user_id ,Pet $pet_id)
    {

        $currentUser = Auth::user();
        $checkUser = User::get()->findorFail($user_id);

        if ($currentUser->id === $checkUser->id) {
            $pet = Pet::get()->find($pet_id);
            $medicalRecords = $pet->medicalRecords()->with('vaccine')->get();
            $resourcedMedicalRecords = PetMedicalRecordResource::collection($medicalRecords);
            return response()->json([
                'records'=>$resourcedMedicalRecords
            ]);
        }

        return response()->json([
            'Unauthorized'
        ]);
    }
}
