@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="fw-bold mb-1">Edit Buku</h3>
                <p class="text-muted small mb-0">Ubah informasi data buku.</p>
            </div>
            <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('buku.update', $buku->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="judul" class="form-label fw-semibold">Judul Buku <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $buku->judul) }}" required maxlength="100">
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="penulis" class="form-label fw-semibold">Penulis <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('penulis') is-invalid @enderror" id="penulis" name="penulis" value="{{ old('penulis', $buku->penulis) }}" required maxlength="100">
                            @error('penulis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="penerbit" class="form-label fw-semibold">Penerbit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('penerbit') is-invalid @enderror" id="penerbit" name="penerbit" value="{{ old('penerbit', $buku->penerbit) }}" required maxlength="100">
                            @error('penerbit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tahun_terbit" class="form-label fw-semibold">Tahun Terbit <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('tahun_terbit') is-invalid @enderror" id="tahun_terbit" name="tahun_terbit" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" required>
                            @error('tahun_terbit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="stok" class="form-label fw-semibold">Jumlah Stok <span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control @error('stok') is-invalid @enderror" id="stok" name="stok" value="{{ old('stok', $buku->stok) }}" required>
                            @error('stok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block">Sampul Saat Ini</label>
                        @if($buku->cover)
                            <img src="{{ asset('storage/' . $buku->cover) }}" alt="{{ $buku->judul }}" class="img-thumbnail mb-2" style="max-height: 160px;">
                        @else
                            <div class="text-muted small fst-italic mb-2">Belum ada gambar sampul.</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="cover" class="form-label fw-semibold">Ganti Sampul (Opsional)</label>
                        <input type="file" class="form-control @error('cover') is-invalid @enderror" id="cover" name="cover" accept="image/*" onchange="previewImage(event)">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah sampul. Format: JPG, JPEG, PNG. Maks: 2 MB.</small>
                        @error('cover')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="mt-2">
                            <img id="imagePreview" src="#" alt="Pratinjau Sampul Baru" class="img-thumbnail d-none" style="max-height: 160px;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('buku.index') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Perbarui Buku
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        var input = event.target;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('imagePreview');
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection