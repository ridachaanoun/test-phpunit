<?php

namespace App\Http\Controllers\api\auth;

use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{
    // Register new user
    public function register(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:6',
                'role_id' => 'nullable|integer|exists:roles,id',
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            // Check if the first user to register
            $role_id = User::count() == 0 
            ? Role::where('name', 'Admin')->first()->id 
            : Role::where('name', 'User')->first()->id;

            $user = User::create([
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'role_id' => $role_id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User registered successfully',
                'user' => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Login user
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['username', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Wrong username and/or password',
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Logout user
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'User logged out successfully',
        ], 200);
    }
}