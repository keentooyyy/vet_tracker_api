<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

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

        if(Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('VetTracker')->plainTextToken;

            return response()->json([
                $success
            ]);
        }
        else{
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
           'Invalid Request'
        ],401);
    }

}
