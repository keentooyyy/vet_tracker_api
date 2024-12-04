<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
