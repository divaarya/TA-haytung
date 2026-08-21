<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
        'role' => 'required|in:admin,kandang,gudang,reseller',
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
	'no_hp' => 'nullable|string|max:20',
	'status' => 'nullable|in:Aktif,Cuti',
    ]);

    if ($request->hasFile('foto')) {
        $validated['foto'] = $request->file('foto')->store('users', 'public');
    }

    $validated['password'] = \Hash::make($validated['password']);

    $user = \App\Models\User::create($validated);

    return response()->json([
        'message' => 'User berhasil dibuat',
        'data' => $user,
        'foto_url' => $user->foto ? asset('storage/'.$user->foto) : null
    ]);
}

    // LOGIN
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($validated)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'data' => $user,
            'role' => $user->role
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    // UPDATE PROFILE
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('users', 'public');
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profile berhasil diupdate',
            'data' => $user,
            'foto_url' => $user->foto ? asset('storage/' . $user->foto) : null
        ]);
    }

    // UPDATE FCM TOKEN
    public function updateFcmToken(Request $request)
    {
        $validated = $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $request->user()->update(['fcm_token' => $validated['fcm_token']]);

        return response()->json([
            'message' => 'FCM token tersimpan'
        ]);
    }

    // CLEAR FCM TOKEN (dipanggil pas logout)
    public function clearFcmToken(Request $request)
    {
        $request->user()->update(['fcm_token' => null]);

        return response()->json([
            'message' => 'FCM token dihapus'
        ]);
    }

    // CHANGE PASSWORD
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Password saat ini salah'
            ], 422);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return response()->json([
            'message' => 'Password berhasil diubah'
        ]);
    }
}
