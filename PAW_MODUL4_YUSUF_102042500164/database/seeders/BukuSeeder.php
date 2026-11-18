<?php

namespace Database\Seeders;

use App\Models\Buku;
use Illuminate\Database\Seeder;

class BukuSeeder extends Seeder
{
    /**
     * Mengisi data awal koleksi buku perpustakaan.
     */
    public function run(): void
    {
        $daftarBuku = [
            [
                'judul'        => 'Pemrograman Laravel',
                'penulis'      => 'Taylor Otwell',
                'penerbit'     => 'Informatika',
                'tahun_terbit' => 2023,
                'stok'         => 10,
                'cover'        => null,
            ],
            [
                'judul'        => 'Basis Data',
                'penulis'      => 'Fathansyah',
                'penerbit'     => 'Informatika',
                'tahun_terbit' => 2022,
                'stok'         => 8,
                'cover'        => null,
            ],
            [
                'judul'        => 'Algoritma dan Pemrograman',
                'penulis'      => 'Rinaldi Munir',
                'penerbit'     => 'Informatika',
                'tahun_terbit' => 2021,
                'stok'         => 5,
                'cover'        => null,
            ],
            [
                'judul'        => 'Rekayasa Perangkat Lunak',
                'penulis'      => 'Roger S. Pressman',
                'penerbit'     => 'Andi Publisher',
                'tahun_terbit' => 2020,
                'stok'         => 6,
                'cover'        => null,
            ],
            [
                'judul'        => 'Sistem Informasi Manajemen',
                'penulis'      => 'Kenneth C. Laudon',
                'penerbit'     => 'Salemba Empat',
                'tahun_terbit' => 2021,
                'stok'         => 4,
                'cover'        => null,
            ],
            [
                'judul'        => 'Analisis dan Perancangan Sistem Informasi',
                'penulis'      => 'Jogiyanto Hartono',
                'penerbit'     => 'Andi Publisher',
                'tahun_terbit' => 2019,
                'stok'         => 0, // Untuk menguji filter Stok Habis
                'cover'        => null,
            ],
            [
                'judul'        => 'Jaringan Komputer',
                'penulis'      => 'Andrew S. Tanenbaum',
                'penerbit'     => 'Informatika',
                'tahun_terbit' => 2021,
                'stok'         => 7,
                'cover'        => null,
            ],
            [
                'judul'        => 'Clean Code',
                'penulis'      => 'Robert C. Martin',
                'penerbit'     => 'Prentice Hall',
                'tahun_terbit' => 2020,
                'stok'         => 3,
                'cover'        => null,
            ],
            [
                'judul'        => 'Pengantar Teknologi Informasi',
                'penulis'      => 'Abdul Kadir',
                'penerbit'     => 'Andi Publisher',
                'tahun_terbit' => 2022,
                'stok'         => 9,
                'cover'        => null,
            ],
        ];

        foreach ($daftarBuku as $buku) {
            Buku::create($buku);
        }
    }
}