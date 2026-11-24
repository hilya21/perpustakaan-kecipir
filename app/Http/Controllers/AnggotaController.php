<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    // GET /api/anggota : daftar semua anggota (admin)
    public function index()
    {
        return response()->json([
            'message' => 'Daftar anggota',
            'data' => Anggota::with('user:id,email,role')->get(),
        ]);
    }

    // GET /api/anggota/{id} : detail satu anggota (admin)
    public function show(Anggota $anggota)
    {
        return response()->json([
            'message' => 'Detail anggota',
            'data' => $anggota->load('user:id,email,role'),
        ]);
    }

    // PUT /api/anggota/{id} : ubah data anggota (admin)
    public function update(Request $request, Anggota $anggota)
    {
        $data = $request->validate([
            'nim' => 'sometimes|string|unique:anggotas,nim,' . $anggota->id,
            'nama' => 'sometimes|string',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        $anggota->update($data);

        return response()->json([
            'message' => 'Anggota berhasil diubah',
            'data' => $anggota,
        ]);
    }

    // DELETE /api/anggota/{id} : hapus anggota beserta akunnya (admin)
    public function destroy(Anggota $anggota)
    {
        $anggota->user()->delete();

        return response()->json(['message' => 'Anggota berhasil dihapus']);
    }
}