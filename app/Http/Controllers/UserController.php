<?php

namespace App\Http\Controllers;

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
        $token['token'] = $user->createToken('VetTracker')->plainTextToken;
        $data = [
            'id' => $user->id,
            'token'=> $token['token'],
        ];

        return response()->json($data);
    }

    public function login(Request $request)
    {
        $input = $request->all();
        $email = $input['email'];
        $password = $input['password'];

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('VetTracker')->plainTextToken;
            $token = $success['token'];
//            dd($token);
            return response()->json(
                [
                    'id' => Auth::id(),
                    'token'=>$token
                ]
            );
        } else {
            return response()->json([
                "unauthenticated"
            ]);
        }
    }

    public function logout(Request $request)
    {
        if (auth()->check()) {
            auth()->user()->tokens()->delete();
            return response()->json([
                "Successfully logged out",
            ]);
        }
        return response()->json([
            'message' => 'Invalid Request'
        ]);
    }

    public function getUser(User $id)
    {
        $currentUser = auth()->user();
        $toCheckUser = User::get()->findorFail($id);
        if ($currentUser->id === $toCheckUser->id) {
            $pets = $currentUser->pets()->with('petType')->get();
//            $appointments = $currentUser->appointments()->with('pet')->get();
            return response()->json([
                'user' => $currentUser,
//                'pets' => $pets,
//                'appointments' => $appointments,
            ]);
        }

        return response()->json(["Unauthorized"], 401);
    }

}
