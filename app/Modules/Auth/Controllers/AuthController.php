<?php

namespace App\Modules\Auth\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Modules\Auth\Requests\LoginRequest;

class AuthController
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }
    public function me()
    {
    return response()->json([
        'user' => auth()->user(),
    ]);
    }
    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
}

}