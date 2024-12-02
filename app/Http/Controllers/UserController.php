<?php

namespace App\Http\Controllers;

use App\Http\Resources\PetResource;
use App\Models\User;
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
            $pets = PetResource::collection($currentUser->pets()->with('petType')->get());
            return response()->json([
                'user' => $currentUser->only(['id', 'first_name', 'last_name', 'email', 'brgy', 'city','street']),
                'pets' => $pets,
            ]);
        }

        return response()->json(["Unauthorized"], 401);
    }

}
