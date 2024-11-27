<?php

namespace App\Http\Controllers;

use App\Models\PetType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PetTypeController extends Controller
{
    public function createPetType(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                $validator->errors()
            ]);
        }
        $input = $request->all();
        $petType = PetType::create($input);
        return response()->json([
            $petType
        ]);
    }
}
