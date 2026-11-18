<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Mahasiswa - TelkomLibrary</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .register-card {
            width: 100%;
            max-width: 460px;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>

<div class="container p-3">
    <div class="card register-card mx-auto bg-white">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-book me-1"></i> TelkomLibrary</h4>
                </a>
                <p class="text-muted small">Pendaftaran Akun Mahasiswa</p>
            </div>

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold small">Nama Lengkap</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Ahmad Pratama" required autofocus>
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold small">Alamat Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="nama@student.telkomuniversity.ac.id" required>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold small">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Minimal 6 karakter" required>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold small">Konfirmasi Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary py-2 fw-semibold">
                        Daftar Sebagai Mahasiswa
                    </button>
                </div>

                <div class="text-center">
                    <span class="text-muted small">Sudah punya akun? </span>
                    <a href="{{ route('login') }}" class="small text-decoration-none fw-semibold">Masuk di sini</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

