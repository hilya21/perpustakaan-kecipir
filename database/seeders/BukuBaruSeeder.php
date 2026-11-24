<?php

namespace Database\Seeders;

use App\Models\Buku;
use Illuminate\Database\Seeder;

class BukuBaruSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'Fiksi', 5],
            ['Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980, 'Fiksi', 3],
            ['Negeri 5 Menara', 'Ahmad Fuadi', 'Gramedia', 2009, 'Fiksi', 4],
            ['Laut Bercerita', 'Leila S. Chudori', 'KPG', 2017, 'Fiksi', 4],
            ['Atomic Habits', 'James Clear', 'Gramedia', 2018, 'Non-Fiksi', 6],
            ['Filosofi Teras', 'Henry Manampiring', 'Kompas', 2018, 'Non-Fiksi', 5],
        ];

        foreach ($data as [$judul, $penulis, $penerbit, $tahun, $kategori, $stok]) {
            Buku::updateOrCreate(
                ['judul' => $judul],
                compact('penulis', 'penerbit', 'tahun', 'kategori') + ['stok' => $stok]
            );
        }
    }
}