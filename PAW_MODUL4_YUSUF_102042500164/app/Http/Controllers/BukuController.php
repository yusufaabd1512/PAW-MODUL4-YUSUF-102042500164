<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BukuController extends Controller
{
    /**
     * Menampilkan daftar semua buku untuk admin dengan fitur pencarian, filter, dan sorting.
     */
    public function index(Request $request)
    {
        $query = Buku::query();

        // 1. Pencarian
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        // 2. Filter Penerbit
        if ($request->filled('penerbit')) {
            $query->where('penerbit', $request->input('penerbit'));
        }

        // 3. Filter Status Ketersediaan
        if ($request->filled('status')) {
            if ($request->status === 'tersedia') {
                $query->where('stok', '>', 0);
            } elseif ($request->status === 'habis') {
                $query->where('stok', '<=', 0);
            }
        }

        // 4. Sorting
        switch ($request->input('sort')) {
            case 'judul_asc':
                $query->orderBy('judul', 'asc');
                break;
            case 'judul_desc':
                $query->orderBy('judul', 'desc');
                break;
            case 'tahun_desc':
                $query->orderBy('tahun_terbit', 'desc');
                break;
            case 'tahun_asc':
                $query->orderBy('tahun_terbit', 'asc');
                break;
            case 'stok_desc':
                $query->orderBy('stok', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $buku = $query->paginate(10)->withQueryString();

        // Ambil daftar unik penerbit untuk opsi dropdown filter
        $listPenerbit = Buku::select('penerbit')->distinct()->orderBy('penerbit', 'asc')->pluck('penerbit');

        return view('buku.index', compact('buku', 'listPenerbit'));
    }

    /**
     * Menampilkan form tambah buku baru.
     */
    public function create()
    {
        return view('buku.create');
    }

    /**
     * Menyimpan buku baru beserta berkas sampul ke storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'        => 'required|string|max:100',
            'penulis'      => 'required|string|max:100',
            'penerbit'     => 'required|string|max:100',
            'tahun_terbit' => 'required|integer',
            'stok'         => 'required|integer|min:0',
            'cover'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'judul.required'        => 'Judul buku wajib diisi.',
            'judul.max'             => 'Judul buku maksimal 100 karakter.',
            'penulis.required'      => 'Penulis buku wajib diisi.',
            'penulis.max'           => 'Penulis buku maksimal 100 karakter.',
            'penerbit.required'     => 'Penerbit buku wajib diisi.',
            'penerbit.max'          => 'Penerbit buku maksimal 100 karakter.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.integer'  => 'Tahun terbit harus berupa angka.',
            'stok.required'         => 'Stok buku wajib diisi.',
            'stok.integer'          => 'Stok buku harus berupa angka.',
            'stok.min'              => 'Stok buku tidak boleh kurang dari 0.',
            'cover.image'           => 'Berkas yang diunggah harus berupa gambar.',
            'cover.mimes'           => 'Format gambar harus JPG, JPEG, atau PNG.',
            'cover.max'             => 'Ukuran gambar maksimal 2 MB.',
        ]);

        // Simpan berkas cover jika diunggah
        if ($request->hasFile('cover')) {
            $validated['cover'] = $request->file('cover')->store('buku', 'public');
        }

        Buku::create($validated);

        return redirect()->route('buku.index')
            ->with('success', 'Buku berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail buku.
     */
    public function show(Buku $buku)
    {
        $buku->load(['peminjaman.anggota']);

        return view('buku.show', compact('buku'));
    }

    /**
     * Menampilkan form edit buku.
     */
    public function edit(Buku $buku)
    {
        return view('buku.edit', compact('buku'));
    }

    /**
     * Memperbarui data buku dan mengganti berkas sampul lama.
     */
    public function update(Request $request, Buku $buku)
    {
        $validated = $request->validate([
            'judul'        => 'required|string|max:100',
            'penulis'      => 'required|string|max:100',
            'penerbit'     => 'required|string|max:100',
            'tahun_terbit' => 'required|integer',
            'stok'         => 'required|integer|min:0',
            'cover'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'judul.required'        => 'Judul buku wajib diisi.',
            'judul.max'             => 'Judul buku maksimal 100 karakter.',
            'penulis.required'      => 'Penulis buku wajib diisi.',
            'penulis.max'           => 'Penulis buku maksimal 100 karakter.',
            'penerbit.required'     => 'Penerbit buku wajib diisi.',
            'penerbit.max'          => 'Penerbit buku maksimal 100 karakter.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.integer'  => 'Tahun terbit harus berupa angka.',
            'stok.required'         => 'Stok buku wajib diisi.',
            'stok.integer'          => 'Stok buku harus berupa angka.',
            'stok.min'              => 'Stok buku tidak boleh kurang dari 0.',
            'cover.image'           => 'Berkas yang diunggah harus berupa gambar.',
            'cover.mimes'           => 'Format gambar harus JPG, JPEG, atau PNG.',
            'cover.max'             => 'Ukuran gambar maksimal 2 MB.',
        ]);

        // Hapus berkas cover lama jika ada berkas baru yang diunggah
        if ($request->hasFile('cover')) {
            if ($buku->cover && Storage::disk('public')->exists($buku->cover)) {
                Storage::disk('public')->delete($buku->cover);
            }
            $validated['cover'] = $request->file('cover')->store('buku', 'public');
        }

        $buku->update($validated);

        return redirect()->route('buku.index')
            ->with('success', 'Buku berhasil diperbarui!');
    }

    /**
     * Menghapus buku beserta berkas fisik gambar sampulnya dari database dan storage.
     */
    public function destroy(Buku $buku)
    {
        // Cek jika buku sedang dipinjam
        $adaPeminjamanAktif = $buku->peminjaman()->where('status', 'Dipinjam')->exists();
        if ($adaPeminjamanAktif) {
            return redirect()->route('buku.index')
                ->with('error', 'Buku tidak dapat dihapus karena sedang dalam status dipinjam.');
        }

        // Hapus berkas fisik cover sebelum record buku dihapus
        if ($buku->cover && Storage::disk('public')->exists($buku->cover)) {
            Storage::disk('public')->delete($buku->cover);
        }

        $buku->delete();

        return redirect()->route('buku.index')
            ->with('success', 'Buku berhasil dihapus!');
    }
}