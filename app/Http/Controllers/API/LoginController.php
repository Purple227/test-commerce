<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    public function login(Request $request) {

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6',
            'email' => 'required|email',
          ]);

        if ($validator->fails()) {
            $mult_response = [
                "Title" => 
                    "Operation failed",
                "message" => 
                    $validator->errors(),
                "code" => 422,
              ];
            return response()->json(['terminus' => env('APP_URL').'/'.'api/login', 'status' => 'F9', 'response' => $mult_response] , 422);        
        }

        $user = User::where('email', $request->email)->first();

        if($user == NULL) {
            $mult_response = [
                "Title" => 
                    "operation failed",
                "message" => 
                   'The Emaill Address Cannot Be Found',
                "code" => 404,
              ];
            return response()->json(['terminus' => env('APP_URL').'/'.'api/login', 'status' => 'F9', 'response' => $mult_response] , 404);
        }

        if (! $user || ! Hash::check($request->password, $user->password)) {
            $mult_response = [
                "Title" => 
                    "Operation failed",
                "message" => 
                   'Invalid Credentials',
                "code" => 401,
              ];
            return response()->json(['terminus' => env('APP_URL').'/'.'api/login', 'status' => 'F9', 'response' => $mult_response] , 401);
        }

        $random = mt_rand(100000, 999999);
        $token = $user->createToken($random)->plainTextToken;

        $mult_response = [
            "Title" => 
                "operation successful",
            "message" => 
            "Login Succesfull",
            "code" => 201,
            "token" => $token,
            "data" => $user
          ];
    
          return response()->json(['terminus' => env('APP_URL').'/'.'api/login', 'status' => 'OK', 'response' => $mult_response], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $mult_response = [
            "Title" => 
            'Operation successful',
            "message" => 
            "User Logout Succesfully",
            "code" => 202,
          ];
          return response()->json(['terminus' => env('APP_URL').'/'.'api/logout', 'status' => 'OK', 'response' => $mult_response], 202);
    }
    

}
