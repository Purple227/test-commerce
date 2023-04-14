<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Profile;

class UserController extends Controller
{

    public function create(Request $request) {

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6',
            'email' => 'required|email|unique:users',
            'name' => 'required',
          ]);

         if ($validator->fails()) {
            $mult_response = [
                "Title" => 
                    "Operation failed",
                "message" => 
                    $validator->errors(),
                "code" => 422,
              ];
            return response()->json(['terminus' => env('APP_URL').'/'.'api/register', 'status' => 'F9', 'response' => $mult_response] , 422);
          }

          $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'password'          => Hash::make($request->password),
            'profile_picture'   => 'default.png',
        ]);

        $mult_response = [
            "Title" => 
                "Operation successful",
            "message" => 
            "Registration Succesfull",
            "code" => 201,
            "data" => $user
          ];

        return response()->json(['terminus' => env('APP_URL').'/'.'api/register', 'status' => 'OK', 'response' => $mult_response], 201);
    }

    public function loginUser(Request $request)
    {
        $mult_response = [
            "Title" => 
                "operation successful",
            "message" => 
            'Authenticated User',
            "code" => 200,
            "data" => $request->user()
          ];
          return response()->json(['terminus' => env('APP_URL').'/'.'api/user', 'status' => 'OK', 'response' => $mult_response], 200);

    }

    public function updateProfile(Request $request) {

        $validator = Validator::make($request->all(), [
            'profile_picture' => 'profile_picture|mimes:jpeg,png,jpg,gif,svg|max:2048',
          ]);
          $user = Profile::where('user_id', $request->user()->id)->first();


        if($user == NULL) {
            
            $imageName = time().'.'.$request->profile_picture->extension();  
            $request->profile_picture->move(public_path('profile'), $imageName);


            $user = Profile::create([
                'profile_picture'  => $imageName,
                'address'          => $request->address,
                'user_id'            => $request->user()->id,
            ]);

            
        } else {

            $imageName = time().'.'.$request->profile_picture->extension();  
            $request->profile_picture->move(public_path('profile'), $imageName);
            
            $user->update([
                'profile_picture'  => $imageName,
                'address'          => $request->address,
            ]);

        }

        $mult_response = [
            "Title" => 
                "Operation successful",
            "message" => 
            "User Profile Updated Succesfully",
            "code" => 201,
            "data" => $user
          ];

        return response()->json(['terminus' => env('APP_URL').'/'.'api/user/profile', 'status' => 'OK', 'response' => $mult_response], 201);

    }

}
