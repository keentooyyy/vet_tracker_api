<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
            'brgy' => 'sometimes',
            'city' => 'sometimes',
            'street' => 'sometimes',
        ]);
        if ($validator->fails()) {
            return response()->json([
                $validator->errors()

            ], 401);
        }


        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('VetTracker')->plainTextToken;

        return response()->json([
            $success
        ]);
    }

    public function login(Request $request)
    {
        $input = $request->all();
        $email = $input['email'];
        $password = $input['password'];

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('VetTracker')->plainTextToken;

            return response()->json([
                $success
            ]);
        } else {
            return response()->json([
                "unauthenticated"
            ]);
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::user()->tokens()->delete();
            return response()->json([
                "Successfully logged out",
            ]);
        }
        return response()->json([
            'message' => 'Invalid Request'
        ], 401);
    }

    public function getUser(User $id)
    {
        $currentUser = Auth::user();
        $toCheckUser = User::get()->findorFail($id);


        if ($currentUser->id === $toCheckUser->id) {
            $pets = $currentUser->pets()->with('petType')->get()->map(function ($pet) {
                $birthdate = Carbon::parse($pet->birthdate);
                $age = round($birthdate->diffInYears(Carbon::now()));

                $type = $pet->petType ? $pet->petType->type : 'Unknown';

                return [
                    'id' => $pet->id,
                    'name' => $pet->name,
                    'age' => $age,
                    'birthdate' => $pet->birthdate,
                    'gender' => $pet->gender,
                    'type' => $type,
                    'breed' => $pet->breed,
                ];
            });

            return response()->json([
                'user' => $currentUser->only(['id', 'first_name', 'last_name', 'email', 'brgy', 'city','street']),
                'pets' => $pets,
            ]);
        }

        return response()->json(["Unauthorized"], 401);
    }

}
