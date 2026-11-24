<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Daftar sebagai anggota baru
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'nim' => 'required|unique:anggotas,nim',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'anggota',
            ]);

            Anggota::create([
                'user_id' => $user->id,
                'nim' => $data['nim'],
                'nama' => $data['name'],
                'no_hp' => $data['no_hp'] ?? null,
                'alamat' => $data['alamat'] ?? null,
            ]);

            return $user;
        });

        return response()->json([
            'message' => 'Registrasi berhasil',
            'data' => $user->load('anggota'),
        ], 201);
    }

    // Login, mengembalikan token
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('token-perpustakaan')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'role' => $user->role,
            'token' => $token,
        ]);
    }

    // Logout, menghapus token yang sedang dipakai
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }

    // GET /api/profil : data akun yang sedang login
    public function profil(Request $request)
    {
        return response()->json([
            'message' => 'Profil akun',
            'data' => $request->user()->load('anggota'),
        ]);
    }

    // PUT /api/profil : ubah nama, email, dan data anggota
    public function updateProfil(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        DB::transaction(function () use ($user, $data) {
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            if ($user->anggota) {
                $user->anggota->update([
                    'nama' => $data['name'],
                    'no_hp' => $data['no_hp'] ?? null,
                    'alamat' => $data['alamat'] ?? null,
                ]);
            }
        });

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'data' => $user->fresh()->load('anggota'),
        ]);
    }

    // PUT /api/profil/password : ganti password
    public function ubahPassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return response()->json([
                'message' => 'Password lama salah',
                'errors' => ['password_lama' => ['Password lama salah']],
            ], 422);
        }

        $user->update(['password' => $request->password_baru]);

        return response()->json(['message' => 'Password berhasil diubah']);
    }
}