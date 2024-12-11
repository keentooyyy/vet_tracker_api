<?php

namespace App\Http\Controllers;


use App\Models\Pet;
use App\Models\PetType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PetController extends Controller
{

    public function getPets(User $user_id)
    {
        $currentuser = Auth::user();
        $toCheckUser = User::get()->findorFail($user_id);

        if ($currentuser->id === $toCheckUser->id || $currentuser->account_type === 'vets') {
            $pet = Pet::where('user_id', $user_id->id)
                ->with(['appointments' => function ($query) {
                    $query->where('appointment_status', 'booked')->orderBy('start_time', 'asc');
                }])
                ->get();

            return response()->json([
                'pets' => $pet,
            ]);
        } else {
            return response()->json([
                'Unauthorized'
            ]);
        }

    }


    public function findPet(Pet $pet_id)
    {
        $pet = Pet::get()->findorFail($pet_id);
        return response()->json([
            'current_pet' => $pet
        ]);
    }

    public function getPetType()
    {
        $pet_types = PetType::all();
        return response()->json([
            'types' => $pet_types
        ]);
    }

    public function createPet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'breed' => 'required|string',
            'birthdate' => 'required|date|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()]);
        }

        $input = $request->all();
        $created_pet = Pet::create($input);

        return response()->json([
            'Success'
        ]);

    }

    public function editPet(User $user_id, Pet $pet_id, Request $request)
    {

        $currentUser = Auth::user();
        $toCheckUser = User::get()->findorFail($user_id);

        if ($currentUser->id === $toCheckUser->id || $currentUser->account_type == 'vets') {


            $pet = Pet::get()->where('user_id', $user_id->id)->findorFail($pet_id);
            $validator = Validator::make(request()->all(), [
                'name' => 'required|string',
                'breed' => 'required|string',
                'birthdate' => 'required|date|date_format:Y-m-d',
                'pet_type_id' => 'required|exists:pet_types,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    $validator->errors()
                ], 422);
            }

            $input = $request->all();
            $update = $pet->update($input);


            return response()->json([
                'data' => $update
            ]);

        }
        return response()->json(['message' => "Unauthorized"], 401);

    }

    public function deletePet(User $user_id, Pet $pet_id)
    {
        $currentUser = Auth::user();
        $toCheckUser = User::get()->findorFail($user_id);

        if ($currentUser->id === $toCheckUser->id) {
            $pet = Pet::get()->findorFail($pet_id);

            $deleted_pet = $pet->delete();
            return response()->json([
                $deleted_pet
            ]);
        }
        return response()->json(['message' => "Unauthorized"], 401);

    }
}
