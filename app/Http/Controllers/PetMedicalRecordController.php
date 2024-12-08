<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetMedicalRecord;
use App\Models\User;
use Illuminate\Http\Request;

class PetMedicalRecordController extends Controller
{
    public function getRecords(Pet $pet_id){
        $medical_records = PetMedicalRecord::where('pet_id', $pet_id->id)->get();
        return response()->json([
            'records' => $medical_records
        ]);
    }
}
