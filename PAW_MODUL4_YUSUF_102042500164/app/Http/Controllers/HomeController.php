<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman katalog buku dengan fitur pencarian, filter, sorting, dan paginasi
     */
    public function index(Request $request)
    {
        $query = Buku::query();

        // 1. Pencarian berdasarkan judul, penulis, atau penerbit
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        // 2. Filter penerbit dan ketersediaan stok
        if ($request->filled('penerbit')) {
            $query->where('penerbit', $request->penerbit);
        }

        if ($request->filled('status')) {
            if ($request->status === 'tersedia') {
                $query->where('stok', '>', 0);
            } elseif ($request->status === 'habis') {
                $query->where('stok', '<=', 0);
            }
        }

        // 3. Pengurutan data sesuai opsi pada home.blade.php
        switch ($request->input('sort')) {
            case 'judul_asc':
            case 'judul':
                $query->orderBy('judul', 'asc');
                break;
            case 'judul_desc':
                $query->orderBy('judul', 'desc');
                break;
            case 'tahun_desc':
            case 'tahun':
                $query->orderBy('tahun_terbit', 'desc');
                break;
            case 'tahun_asc':
                $query->orderBy('tahun_terbit', 'asc');
                break;
            case 'stok_desc':
            case 'stok':
                $query->orderBy('stok', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        // 4. Paginasi 8 data dengan mempertahankan parameter URL
        $buku = $query->paginate(8)->withQueryString();

        // 5. Ambil list penerbit unik untuk opsi dropdown filter
        $listPenerbit = Buku::whereNotNull('penerbit')
            ->where('penerbit', '!=', '')
            ->distinct()
            ->orderBy('penerbit', 'asc')
            ->pluck('penerbit');

        return view('home', compact('buku', 'listPenerbit'));
    }

    /**
     * Menampilkan halaman detail informasi sebuah buku untuk publik/mahasiswa
     */
    public function show(Buku $buku)
    {
        return view('home_show', compact('buku'));
    }
}