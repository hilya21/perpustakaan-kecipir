<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    const LAMA_PINJAM_HARI = 7;
    const DENDA_PER_HARI = 1000;

    // GET /api/peminjaman : admin lihat semua, anggota lihat miliknya sendiri
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Peminjaman::with(['anggota:id,nim,nama', 'buku:id,judul']);

        if (!$user->isAdmin()) {
            $query->where('anggota_id', $user->anggota->id);
        }

        return response()->json([
            'message' => 'Daftar peminjaman',
            'data' => $query->latest()->get(),
        ]);
    }

    // POST /api/peminjaman : anggota meminjam buku
    public function store(Request $request)
    {
        $data = $request->validate([
            'buku_id' => 'required|exists:bukus,id',
        ]);

        $user = $request->user();

        if ($user->isAdmin() || !$user->anggota) {
            return response()->json(['message' => 'Hanya anggota yang bisa meminjam buku'], 403);
        }

        $peminjaman = DB::transaction(function () use ($data, $user) {
            $buku = Buku::lockForUpdate()->find($data['buku_id']);

            if ($buku->stok < 1) {
                return null;
            }

            $buku->decrement('stok');

            return Peminjaman::create([
                'anggota_id' => $user->anggota->id,
                'buku_id' => $buku->id,
                'tanggal_pinjam' => Carbon::today(),
                'tanggal_jatuh_tempo' => Carbon::today()->addDays(self::LAMA_PINJAM_HARI),
                'status' => 'dipinjam',
            ]);
        });

        if (!$peminjaman) {
            return response()->json(['message' => 'Stok buku habis'], 400);
        }

        return response()->json([
            'message' => 'Peminjaman berhasil',
            'data' => $peminjaman->load('buku:id,judul'),
        ], 201);
    }

    // GET /api/peminjaman/{id} : detail satu peminjaman
    public function show(Request $request, Peminjaman $peminjaman)
    {
        $user = $request->user();

        if (!$user->isAdmin() && $peminjaman->anggota_id !== optional($user->anggota)->id) {
            return response()->json(['message' => 'Tidak berhak mengakses data ini'], 403);
        }

        return response()->json([
            'message' => 'Detail peminjaman',
            'data' => $peminjaman->load(['anggota:id,nim,nama', 'buku:id,judul']),
        ]);
    }

    // PUT /api/peminjaman/{id}/kembali : admin memproses pengembalian
    public function kembali(Peminjaman $peminjaman)
    {
        if ($peminjaman->status === 'dikembalikan') {
            return response()->json(['message' => 'Buku sudah dikembalikan'], 400);
        }

        DB::transaction(function () use ($peminjaman) {
            $hariIni = Carbon::today();
            $jatuhTempo = Carbon::parse($peminjaman->tanggal_jatuh_tempo);

            $denda = 0;
            if ($hariIni->gt($jatuhTempo)) {
                $denda = $jatuhTempo->diffInDays($hariIni) * self::DENDA_PER_HARI;
            }

            $peminjaman->update([
                'tanggal_kembali' => $hariIni,
                'denda' => $denda,
                'status' => 'dikembalikan',
            ]);

            $peminjaman->buku()->increment('stok');
        });

        return response()->json([
            'message' => 'Pengembalian berhasil diproses',
            'data' => $peminjaman->fresh()->load(['anggota:id,nim,nama', 'buku:id,judul']),
        ]);
    }
}