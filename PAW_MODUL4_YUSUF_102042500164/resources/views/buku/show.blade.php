@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="fw-bold mb-1">Detail Buku</h3>
                <p class="text-muted small mb-0">Informasi lengkap dan riwayat peminjaman buku ini.</p>
            </div>
            <div>
                <a href="{{ route('buku.edit', $buku->id) }}" class="btn btn-primary btn-sm me-1">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body p-4">
                <div class="row g-4 align-items-center">
                    {{-- Sampul Buku --}}
                    <div class="col-md-3 text-center">
                        @if($buku->cover)
                            <img src="{{ asset('storage/' . $buku->cover) }}" alt="{{ $buku->judul }}" class="img-fluid rounded shadow-sm" style="max-height: 240px; object-fit: cover;">
                        @else
                            <div class="bg-light text-muted rounded d-flex align-items-center justify-content-center" style="height: 200px; border: 2px dashed #cbd5e1;">
                                <div class="text-center">
                                    <i class="bi bi-image fs-1 d-block mb-1 text-secondary"></i>
                                    <span class="small">Belum Ada Sampul</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Data Rincian Buku --}}
                    <div class="col-md-6">
                        <h4 class="fw-bold text-dark mb-3">{{ $buku->judul }}</h4>
                        <div class="row text-secondary g-2">
                            <div class="col-sm-4 fw-semibold text-muted">Penulis</div>
                            <div class="col-sm-8 text-dark">{{ $buku->penulis }}</div>

                            <div class="col-sm-4 fw-semibold text-muted">Penerbit</div>
                            <div class="col-sm-8 text-dark">{{ $buku->penerbit }}</div>

                            <div class="col-sm-4 fw-semibold text-muted">Tahun Terbit</div>
                            <div class="col-sm-8 text-dark">{{ $buku->tahun_terbit }}</div>
                        </div>
                    </div>

                    {{-- Status Ketersediaan Stok --}}
                    <div class="col-md-3 text-center border-start">
                        <span class="text-muted small mb-1 d-block">Status Ketersediaan Stok</span>
                        <h2 class="fw-bold mb-1 text-dark">
                            {{ $buku->stok }} <span class="fs-6 text-muted">eksemplar</span>
                        </h2>
                        @if($buku->stok > 0)
                            <span class="badge bg-success px-3 py-2 mt-2">
                                <i class="bi bi-check-circle me-1"></i> Tersedia untuk dipinjam
                            </span>
                        @else
                            <span class="badge bg-danger px-3 py-2 mt-2">
                                <i class="bi bi-x-circle me-1"></i> Stok Habis
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Peminjaman Buku Ini -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Riwayat Peminjaman Buku Ini</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 50px;">No</th>
                                <th>Nama Anggota</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($buku->peminjaman as $index => $pinjam)
                                <tr>
                                    <td class="ps-3 text-muted">{{ $index + 1 }}</td>
                                    <td class="fw-semibold">{{ $pinjam->anggota->nama ?? '-' }}</td>
                                    <td>{{ $pinjam->tanggal_pinjam }}</td>
                                    <td>{{ $pinjam->tanggal_kembali ?? '-' }}</td>
                                    <td>
                                        @if($pinjam->status === 'Dipinjam')
                                            <span class="badge bg-warning text-dark">Dipinjam</span>
                                        @else
                                            <span class="badge bg-success">Dikembalikan</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Buku ini belum pernah dipinjam.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
