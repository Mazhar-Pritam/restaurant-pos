<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $credentials['is_active'] = true;   // deactivated accounts cannot log in

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials or inactive account.',
            ]);
        }

        $request->session()->regenerate();   // protects against session fixation

        return response()->json(['message' => 'Logged in']);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
    }

    // Current user + permission names. React uses the list only to hide buttons;
    // the server still checks every request.
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('role.permissions');

        abort_unless($user->is_active, 403, 'Account is inactive.');

        return response()->json([
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'role'        => $user->role
                ? ['name' => $user->role->name, 'label' => $user->role->label]
                : null,
            'permissions' => $user->role
                ? $user->role->permissions->pluck('name')->values()
                : [],
        ]);
    }
}
