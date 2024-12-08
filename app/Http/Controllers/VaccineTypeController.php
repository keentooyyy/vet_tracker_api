<?php

namespace App\Http\Controllers;

use App\Models\VaccineType;
use Illuminate\Http\Request;

class VaccineTypeController extends Controller
{
    public function getVaccineTypes(){
        $vaccines = VaccineType::all();
        return response()->json([
            'vaccines' => $vaccines
        ]);
    }
}
