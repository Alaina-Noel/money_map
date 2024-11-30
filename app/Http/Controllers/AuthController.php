<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth-token')->plainTextToken;

            DB::commit();

            return response()->json([
                'message' => 'Registration successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function refresh(Request $request)
    {
        $user = $request->user();

        // Retrieve the user's latest token
        $latestToken = $user->tokens()->latest('created_at')->first();

        if ($latestToken) {
            // deleting the latest token invalidates it
            $latestToken->delete();
        }

        // Create and issue a new token
        $newToken = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'access_token' => $newToken,
            'token_type' => 'Bearer',
        ]);
    }

}
