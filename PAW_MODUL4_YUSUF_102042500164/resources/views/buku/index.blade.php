@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Daftar Buku</h2>
        <p class="text-muted mb-0">Kelola data koleksi buku perpustakaan.</p>
    </div>
    <a href="{{ route('buku.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Buku
    </a>
</div>

{{-- Form Pencarian, Filter & Sort --}}
<div class="card shadow-sm mb-3">
    <div class="card-body py-3">
        <form action="{{ url()->current() }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari judul, penulis..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="penerbit" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Penerbit</option>
                    @foreach($listPenerbit as $p)
                        <option value="{{ $p }}" {{ request('penerbit') === $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Stok Tersedia</option>
                    <option value="habis" {{ request('status') === 'habis' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="sort" class="form-select" onchange="this.form.submit()">
                    <option value="">Urutkan: Terbaru</option>
                    <option value="judul_asc" {{ request('sort') === 'judul_asc' ? 'selected' : '' }}>Judul (A - Z)</option>
                    <option value="judul_desc" {{ request('sort') === 'judul_desc' ? 'selected' : '' }}>Judul (Z - A)</option>
                    <option value="tahun_desc" {{ request('sort') === 'tahun_desc' ? 'selected' : '' }}>Tahun (Terbaru)</option>
                    <option value="tahun_asc" {{ request('sort') === 'tahun_asc' ? 'selected' : '' }}>Tahun (Terlama)</option>
                    <option value="stok_desc" {{ request('sort') === 'stok_desc' ? 'selected' : '' }}>Stok Terbanyak</option>
                </select>
            </div>
            <div class="col-md-1 d-flex">
                @if(request('search') || request('penerbit') || request('status') || request('sort'))
                    <a href="{{ url()->current() }}" class="btn btn-outline-danger w-100" title="Reset Semua Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @else
                    <button type="submit" class="btn btn-primary w-100" title="Terapkan">
                        <i class="bi bi-funnel"></i>
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th style="width: 80px;">Sampul</th>
                        <th>Judul Buku</th>
                        <th>Penulis</th>
                        <th>Penerbit</th>
                        <th class="text-center">Tahun</th>
                        <th class="text-center">Stok</th>
                        <th class="text-end pe-3" style="width: 170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($buku as $index => $item)
                        <tr>
                            <td class="ps-3 text-muted">{{ $buku->firstItem() + $index }}</td>
                            <td>
                                @if($item->cover)
                                    <img src="{{ asset('storage/' . $item->cover) }}" alt="{{ $item->judul }}" class="img-thumbnail" style="width: 50px; height: 70px; object-fit: cover;">
                                @else
                                    <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 70px;" title="Tidak ada sampul">
                                        <i class="bi bi-book"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="fw-semibold text-dark">{{ $item->judul }}</td>
                            <td>{{ $item->penulis }}</td>
                            <td>{{ $item->penerbit }}</td>
                            <td class="text-center">{{ $item->tahun_terbit }}</td>
                            <td class="text-center">
                                @if($item->stok > 0)
                                    <span class="badge bg-secondary px-2 py-1">{{ $item->stok }}</span>
                                @else
                                    <span class="badge bg-dark px-2 py-1">Habis (0)</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('buku.show', $item->id) }}" class="btn btn-sm btn-outline-secondary me-1" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('buku.edit', $item->id) }}" class="btn btn-sm btn-outline-secondary me-1" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('buku.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini beserta berkas gambarnya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                @if(request('search'))
                                    Buku dengan kata kunci <strong>"{{ request('search') }}"</strong> tidak ditemukan.
                                @else
                                    Belum ada data buku tersedia.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($buku->hasPages())
        <div class="card-footer bg-white py-3 d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Menampilkan {{ $buku->firstItem() ?? 0 }} - {{ $buku->lastItem() ?? 0 }} dari {{ $buku->total() }} buku
            </small>
            <div>
                {{ $buku->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
@endsection