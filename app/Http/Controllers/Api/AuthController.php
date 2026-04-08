<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:traveler,approver',
        ]);

        $user = User::where('role', $data['role'])->first();

        if (!$user) {
            $company = \App\Models\Company::first();
            $user = User::create([
                'name'       => $data['name'],
                'email'      => \Illuminate\Support\Str::slug($data['name']) . '@onfly.local',
                'password'   => bcrypt('password'),
                'role'       => $data['role'],
                'company_id' => $company?->id,
            ]);
        } else {
            $user->update(['name' => $data['name']]);
        }

        $token = $user->createToken('spa-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user->only(['id', 'name', 'email', 'role', 'company_id', 'department', 'position']),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('company')->only(['id', 'name', 'email', 'role', 'company_id', 'department', 'position']),
        ]);
    }
}
