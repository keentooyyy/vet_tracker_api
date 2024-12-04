<?php

namespace App\Http\Controllers;

use App\Models\Pet;
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
            'token' => $token['token'],
        ];

        return response()->json($data);
    }

    public function login(Request $request)
    {
        $input = $request->all();
        $email = $input['email'];
        $password = $input['password'];
//        auth()->attempt(['email' => $email, 'password' => $password]);
//        if (auth()->attempt(['email' => $email, 'password' => $password])) {
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = auth()->user();
            $success['token'] = $user->createToken('VetTracker')->plainTextToken;
            $token = $success['token'];
//            dd($token);
            return response()->json(
                [
                    'id' => auth()->id(),
                    'token' => $token
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
        if (Auth::check()) {
            Auth::user()->tokens()->delete();
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
        $currentUser = Auth::user();
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

    public function editPet(User $user_id, Pet $pet_id)
    {

        $currentUser = Auth::user();
        $toCheckUser = User::get()->findorFail($user_id);

        if ($currentUser->id === $toCheckUser->id) {
            $pet = Pet::where('user_id', $user_id->id)->findorFail($pet_id);
            return response()->json([
                'data' => $pet
            ]);
        }
        return response()->json([
            'message' => "Unauthorized"
        ]);
    }

}
