<?php

namespace App\Http\Controllers;


use App\Models\Pet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PetController extends Controller
{
    //
    public function getPets(User $user_id){
        $currentuser = Auth::user();
        $toCheckUser = User::get()->findorFail($user_id);

        if ($currentuser->id === $toCheckUser->id) {
            $pet = Pet::where('user_id', $user_id->id)
                ->with(['appointments' => function($query) {
                    $query->where('appointment_status', 'booked')->first();
                }])
                ->get();

            return response()->json([
                'pets' => $pet,
            ]);
        }
        else {
            return response()->json([
               'Unauthorized'
            ]);
        }

    }
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
            $pet = Pet::get()->findorFail($pet_id);
            $medicalRecords = $pet->medicalRecords()->get();
            return response()->json([
                'records'=>$medicalRecords
            ]);
        }

        return response()->json([
            'Unauthorized'
        ]);
    }

    public function editPet(User $user_id, Pet $pet_id){
        $pet = Pet::where('user_id', $user_id->id)->findorFail($pet_id);
        return response()->json([
           'data' => $pet
        ]);
    }
    public function showPets(User $user_id){
//        dd($user_id->id);
        $pets = Pet::with('user')->where('user_id', $user_id->id)->get();
//        dd($pets);
//        $pets = PetMedicalRecordResource::collection($pets);
     return response()->json([
         'pet' => $pets
     ]);
    }
}
