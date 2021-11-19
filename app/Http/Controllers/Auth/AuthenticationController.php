<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function createAccount(Request $request){
        $validator = Validator::make($request->all(),[
            'username' => 'required|unique:App\Models\User,username',

            'email' => 'required|unique:App\Models\User,email',
            'password' => 'required|confirmed',
        ]);
        if($validator->fails()){
            return response()->json(['message' => $validator->messages()->toJson(), 'success' => false]);
        }
        User::create([
            'username' => trim($request->get('username')),
            'email' => trim($request->get('email')),
            'password' => bcrypt(trim($request->get('password')))
        ]);
        if($validator->fails()){
            return response()->json(['message' =>'Check Input', 'success' => false]);
        }

        User::create([
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password'))
        ]);

        return response()->json(['message' =>'Account created', 'success' => true]);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'username' => 'required',
            'password' => 'required',
            'device_name' =>'required'
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->messages()->toJson(), 'success' => false]);
        }

        $user = User::where('username', 'like', $request->get('username'))->orWhere('email', 'like', 'username')->first();
        if(empty($user) || !Hash::check(trim($request->get('password')), $user->password)){
            return response()->json(['message' => "check your credentials", 'success' => false]);
        ]);

        if($validator->fails()){
            return response()->json(['message' => 'Check Input', 'success' => false]);
        }

        $user = User::where('username', 'like', $request->get('username'))->orWhere('email', 'like', 'username')->first();
        if(!$user || ! Hash::check($request->get('password'), $user->password)){
            return response()->json(['message' => 'Check your Credentials', 'success' => false]);
        }
        $user->tokens()->where('name', $request->device_name)->delete();
        return response()->json(['message' => $user->createToken($request->device_name)->plainTextToken, 'success' => true]);
    }
}
