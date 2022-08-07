<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Register;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth:api', [
//            'except' => ['login']
//        ]);
        $this->middleware('auth:api', [
            'except' => ['register', 'store']
        ]);
    }

    public function register(Request $request) {
        $register = new Register;
        $register->email = $request->input('email');
        $register->password = Hash::make($request->input('password'));
        $register->save();

        return response()->json([
            'message' => 'Created Successfully',
        ]);
    }

    public function store(Request $request) {
        // $all = User::all();
        // var_dump($all);
        // echo "fdsfds";
        $credentials = $request->only('email', 'password');
        // print_r($credentials);
        $token = $this->guard()->attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 401,
                'error' => 'Incorrect email or password'
            ]);
        } else {
            return $this->respondWithToken($token);
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            '_token' => csrf_token(),
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60 * 6000,
        ]);
    }

    public function guard()
    {
        return Auth::guard();
    }
}
