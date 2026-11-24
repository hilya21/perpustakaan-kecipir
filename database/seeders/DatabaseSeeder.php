<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin awal
        User::create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@perpus.test',
            'password' => 'admin123',
            'role' => 'admin',
        ]);

        // Data buku contoh
        $buku = [
            ['judul' => 'Laskar Pelangi', 'penulis' => 'Andrea Hirata', 'penerbit' => 'Bentang Pustaka', 'tahun' => 2005, 'stok' => 5],
            ['judul' => 'Bumi Manusia', 'penulis' => 'Pramoedya Ananta Toer', 'penerbit' => 'Hasta Mitra', 'tahun' => 1980, 'stok' => 3],
            ['judul' => 'Negeri 5 Menara', 'penulis' => 'Ahmad Fuadi', 'penerbit' => 'Gramedia', 'tahun' => 2009, 'stok' => 4],
        ];

        foreach ($buku as $b) {
            Buku::create($b);
        }
    }
}