<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetMedicalRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PetMedicalRecordController extends Controller
{
    public function getRecords(Pet $pet_id){
        $medical_records = PetMedicalRecord::where('pet_id', $pet_id->id)->get();
        return response()->json([
            'records' => $medical_records
        ]);
    }
    public function deleteMedicalRecord(PetMedicalRecord $medical_record){
        $medical_record = PetMedicalRecord::get()->findorFail($medical_record);

        $medical_record->delete();

        return response()->json([
           'message' => 'Record deleted'
        ]);
    }
    public function createMedicalRecords(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'pet_id' => 'required|exists:pets,id',
            'date_of_administration' => 'required|date_format:Y-m-d',
            'vaccine_id' => 'required|exists:vaccine_types,id',
            'date_of_next_administration' => 'nullable|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);  // Return a 400 Bad Request response if validation fails
        }

        try {
            // Create the pet medical record
            $medicalRecord = PetMedicalRecord::create([
                'pet_id' => $request->pet_id,
                'date_of_administration' => $request->date_of_administration,
                'vaccine_id' => $request->vaccine_id,
                'date_of_next_administration' => $request->date_of_next_administration,
            ]);

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Medical record created successfully.',
                'data' => $medicalRecord
            ], 201);  // HTTP 201 Created

        } catch (\Exception $e) {
            // Handle exceptions and errors
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the medical record.',
                'error' => $e->getMessage()
            ], 500);  // Return a 500 Internal Server Error if something goes wrong
        }
    }
}
