<?php

namespace App\Http\Controllers;

use App\Models\Pet;
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
}
