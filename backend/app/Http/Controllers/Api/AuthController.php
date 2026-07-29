<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }


        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'is_active' => true,
        ]);


        $token = $user->createToken('auth_token')->plainTextToken;


        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
            'token' => $token
        ], 201);
    }



    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);


        $user = User::where('email', $request->email)->first();


        if (!$user || !Hash::check($request->password, $user->password)) {

            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);

        }


        if (!$user->is_active) {

            return response()->json([
                'message' => 'Account is deactivated'
            ], 403);

        }


        $token = $user->createToken('auth_token')->plainTextToken;


        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }




    public function logout(Request $request)
    {

        if ($request->user() && $request->user()->currentAccessToken()) {

            $request->user()
                ->currentAccessToken()
                ->delete();

        }


        return response()->json([
            'message' => 'Logged out successfully'
        ]);

    }




    public function me(Request $request)
    {
        return response()->json(
            $request->user()
        );
    }

}