<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Rules\ReCaptcha;

class AuthController extends Controller
{
    // Register
    public function register(Request $request)
    {
//        _dd($request);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'g-recaptcha-response' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
    public function verifyToken(Request $request)
    {
//        _dd($request->all());
        $fullToken = $request->token;
//        _dd($token);



        if (!$fullToken) {
            return response()->json(['message' => 'Token is missing'], 400);
        }


//        $token = str_replace('Bearer ', '', $token);
        [$id, $token] = explode('|', $fullToken, 2);
        _dd($token);


        $personalAccessToken = PersonalAccessToken::where('token', $token)->first();
//        $personalAccessToken = PersonalAccessToken::findToken($token);
        _dd($personalAccessToken);



        if ($personalAccessToken) {

            return response()->json([
                'message' => 'Token is valid',
                'user' => $personalAccessToken
            ]);
        } else {

            return response()->json(['message' => 'Invalid token'], 401);
        }
    }
}
