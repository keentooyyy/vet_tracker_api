<?php

namespace App\Http\Controllers;

use App\Models\PetType;

class PetTypeController extends Controller
{
    public function getPetTypes()
    {
        $petTypes = PetType::all();
        return response()->json([
            'types'=>$petTypes
        ]);
    }


}
