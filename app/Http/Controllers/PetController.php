<?php

namespace App\Http\Controllers;


use App\Models\Pet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PetController extends Controller
{
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




    public function findPet(Pet $pet_id)
    {

        $pet = Pet::get()->findorFail($pet_id);
        return response()->json([
            'current_pet' => $pet
        ]);
    }
}
