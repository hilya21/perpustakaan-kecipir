<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BukuController extends Controller
{
    // Simpan file foto ke public/uploads/buku, kembalikan path-nya
    private function simpanFoto($file): string
    {
        $nama = time() . '_' . Str::random(6) . '.' . $file->extension();
        $file->move(public_path('uploads/buku'), $nama);
        return 'uploads/buku/' . $nama;
    }

    private function hapusFoto(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            @unlink(public_path($path));
        }
    }

    // GET /api/buku : daftar buku (bisa dicari dengan ?cari=kata)
    public function index(Request $request)
    {
        $query = Buku::query();

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->cari . '%')
                  ->orWhere('penulis', 'like', '%' . $request->cari . '%');
            });
        }

        return response()->json([
            'message' => 'Daftar buku',
            'data' => $query->latest('id')->get(),
        ]);
    }

    // POST /api/buku : tambah buku (admin), boleh dengan foto
    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string',
            'penulis' => 'required|string',
            'penerbit' => 'required|string',
            'tahun' => 'required|digits:4',
            'kategori' => 'required|string',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $this->simpanFoto($request->file('foto'));
        }

        $buku = Buku::create($data);

        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data' => $buku,
        ], 201);
    }

    // GET /api/buku/{id}
    public function show(Buku $buku)
    {
        return response()->json([
            'message' => 'Detail buku',
            'data' => $buku,
        ]);
    }

    // PUT atau POST /api/buku/{id}(/ubah) : ubah buku (admin)
    public function update(Request $request, Buku $buku)
    {
        $data = $request->validate([
            'judul' => 'sometimes|string',
            'penulis' => 'sometimes|string',
            'penerbit' => 'sometimes|string',
            'tahun' => 'sometimes|digits:4',
            'kategori' => 'sometimes|string',
            'stok' => 'sometimes|integer|min:0',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('foto')) {
            $this->hapusFoto($buku->foto);
            $data['foto'] = $this->simpanFoto($request->file('foto'));
        } else {
            unset($data['foto']); // tanpa file baru, foto lama dipertahankan
        }

        $buku->update($data);

        return response()->json([
            'message' => 'Buku berhasil diubah',
            'data' => $buku->fresh(),
        ]);
    }

    // DELETE /api/buku/{id}
    public function destroy(Buku $buku)
    {
        $this->hapusFoto($buku->foto);
        $buku->delete();

        return response()->json(['message' => 'Buku berhasil dihapus']);
    }
}