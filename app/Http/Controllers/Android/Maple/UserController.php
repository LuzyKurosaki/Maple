<?php

namespace App\Http\Controllers\Android\Maple;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function search($search){
        $user = Auth::user();

        $users = User::select('id','username')->where('username', 'like', "%{$search}%")->where('id', '!=', $user->id)->paginate(5);
        $data = [];
        foreach($users as $user){
            $data[] = [
                'id' => $user->id,
                'username' => $user->username,
                'profileImagePath' => null
            ];
        }

        $return = [
            'message' => "fetched",
            'data' => $data,
            'success' => true
        ];
        return response()->json($return);
    }
}
