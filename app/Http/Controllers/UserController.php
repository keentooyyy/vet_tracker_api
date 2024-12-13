<?php

namespace App\Http\Controllers;


use App\Models\Notification;
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
            'brgy' => 'required',
            'city' => 'required',
            'street' => 'required',
            'fcm' => 'sometimes',
        ]);
        if ($validator->fails()) {
            return response()->json([
                $validator->errors()

            ], 401);
        }



        $input = $request->all();
        if ($input['email'] == 'vet_account@gmail.com') {
            $input['account_type'] = 'vets';  // Set account_type to 'vets' for this email
        }
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

        $fcmToken = $input['fcm_token'] ?? null;
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::user();

            if ($fcmToken) {
                $user->update(['fcm_token' => $fcmToken]);
            }

            $success['token'] = $user->createToken('VetTracker')->plainTextToken;
            $token = $success['token'];
//            dd($token);
            return response()->json(
                [
                    'id' => Auth::user()->id,
                    'token' => $token
                ]
            );
        } else {
            return response()->json([
                "unauthenticated"
            ]);
        }
    }

    public function logout()
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

    public function getNotifications(User $user_id){
        $notifications = Notification::where('user_id', $user_id->id)->orderBy('created_at', 'desc')->get();
        return response()->json([
            'notifications' => $notifications
        ]);
    }

}
