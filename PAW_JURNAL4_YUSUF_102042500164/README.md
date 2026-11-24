# Starter Code Praktikum Modul 4 — CeLOE LMS

Selamat datang di **Praktikum Modul 4 Pengembangan Aplikasi Website (CeLOE LMS)**!  
Repositori ini adalah **Starter Code** yang telah dilengkapi fitur dasar Modul 3. Tugas Anda pada Modul 4 adalah melengkapi fitur **Proteksi Route dengan Middleware**, **Filter & Pengurutan Katalog Mata Kuliah**, **Penilaian Tugas (Grading)**, serta **Ekspor Rekap Nilai ke CSV** dengan menyelesaikan **TODO 1 hingga TODO 8**.

---

## Prasyarat Lingkungan (Prerequisites)

Sebelum melakukan clone dan menjalankan proyek ini, pastikan komputer/laptop Anda telah terpasang:
- **Git** ([Unduh Git](https://git-scm.com/))
- **PHP** versi 8.2 atau lebih baru
- **Composer** ([Unduh Composer](https://getcomposer.org/))
- **Database Server** (MySQL / MariaDB, misalnya melalui XAMPP, Laragon, Docker, atau native)
- **Node.js & NPM** (Opsional, versi LTS untuk kompilasi aset frontend)

---

## Panduan Clone & Setup Proyek dari GitHub (Lokal)

Ikuti langkah-langkah berikut secara berurutan pada terminal (Command Prompt / Git Bash / Terminal macOS):

### 1. Clone Repositori
Buka terminal di folder tempat Anda biasa menyimpan tugas/proyek kuliah, lalu jalankan:
```bash
git clone https://github.com/USERNAME_ATAU_ORGANISASI/NAMA_REPO.git
```
> Ganti URL di atas dengan tautan repositori GitHub yang telah disediakan oleh Asisten Praktikum.

Masuk ke dalam direktori proyek yang baru saja di-clone:
```bash
cd NAMA_REPO
```

---

### 2. Install Dependensi PHP (Composer)
Folder `vendor/` tidak disertakan di GitHub karena ukuran filenya yang besar. Unduh seluruh dependensi proyek Laravel dengan perintah:
```bash
composer install
```

---

### 3. Buat File Konfigurasi Lingkungan (`.env`)
Salin file `.env.example` menjadi `.env`:

- **Windows (Command Prompt / PowerShell):**
  ```bash
  copy .env.example .env
  ```
- **macOS / Linux / Git Bash:**
  ```bash
  cp .env.example .env
  ```

---

### 4. Generate Application Key
Generate kunci enkripsi aplikasi Laravel agar session dan keamanan aplikasi berfungsi:
```bash
php artisan key:generate
```

---

### 5. Konfigurasi Database
1. Pastikan service **MySQL** sudah berjalan (misal klik **Start** pada Apache & MySQL di XAMPP / Laragon).
2. Buka aplikasi manajemen database Anda (phpMyAdmin, DBeaver, TablePlus, atau CLI) lalu buat sebuah database baru, misalnya dengan nama:
   ```sql
   CREATE DATABASE jurnalmodul4;
   ```
3. Buka file `.env` menggunakan teks editor (VS Code), cari baris konfigurasi database, dan sesuaikan:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=jurnalmodul4
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   *(Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` jika MySQL Anda menggunakan password khusus).*

---

### 6. Jalankan Migrasi & Seeder Awal
Jalankan migrasi database dan pengisian data dummy awal:
```bash
php artisan migrate:fresh --seed
```
Perintah ini akan membuat struktur tabel dasar Modul 3 dan memasukkan akun default Dosen & Mahasiswa.

---

### 7. (Opsional) Install & Kompilasi Aset Frontend
Jika Anda ingin memperbarui atau membangun aset Tailwind CSS / Vite:
```bash
npm install
npm run build
```

---

### 8. Jalankan Server Lokal Laravel
Jalankan development server Laravel:
```bash
php artisan serve
```
Buka peramban (browser) Anda dan akses alamat:
```text
http://127.0.0.1:8000
```

> **Catatan Penting saat Pengerjaan:**
> Setiap kali Anda selesai mengerjakan **TODO 1** (Migration) dan **TODO 8** (Seeder), jalankan kembali:
> ```bash
> php artisan migrate:fresh --seed
> ```
> agar penambahan kolom tabel dan data seeder baru masuk ke dalam database.

---

## Akun Login Default (Seeder)

Gunakan kredensial berikut untuk menguji fitur dari sisi **Dosen** maupun **Mahasiswa**:

| Peran | Email | Password | Nomor Induk (NIP / NIM) |
| :--- | :--- | :--- | :--- |
| **Dosen** | `dosen@celoe.test` | `dosen123` | `198501012015041001` |
| **Mahasiswa** | `mahasiswa@celoe.test` | `mahasiswa123` | `1301210001` |

---

## Urutan Pengerjaan Tugas (`TODO 1` – `TODO 8`)

Kerjakan bagian-bagian yang ditandai komentar `TODO` secara berurutan pada 8 file berikut:

### 1. `TODO 1` — Migration Kolom Penilaian
- **File:** `database/migrations/2026_10_25_000001_add_grading_to_submissions_table.php`
- **Instruksi:**
  - Pada method `up()`, tambahkan kolom `nilai` (`unsignedTinyInteger`, `nullable`) dan `catatan_dosen` (`text`, `nullable`) setelah kolom `jawaban`.
  - Pada method `down()`, hapus kedua kolom tersebut menggunakan `$table->dropColumn(['nilai', 'catatan_dosen'])`.

### 2. `TODO 2` — Model & Accessor Status Nilai
- **File:** `app/Models/Submission.php`
- **Instruksi:**
  - Tambahkan `'nilai'` dan `'catatan_dosen'` ke dalam properti `$fillable`.
  - Lengkapi method accessor `getStatusNilaiAttribute()` dengan ketentuan:
    - Jika `$this->nilai === null`, kembalikan `'Belum Dinilai'`.
    - Jika `$this->nilai >= 70`, kembalikan `'Lulus'`.
    - Jika `< 70`, kembalikan `'Perlu Perbaikan'`.

### 3. `TODO 3` — Route Middleware & Aktivasi Endpoint Baru
- **File:** `routes/web.php`
- **Instruksi:**
  - Bungkus seluruh route terproteksi menggunakan `Route::middleware(['auth'])->group(...)`.
  - Kelompokkan route khusus dosen ke dalam `Route::middleware(['role:dosen'])->group(...)`.
  - Kelompokkan route khusus mahasiswa ke dalam `Route::middleware(['role:mahasiswa'])->group(...)`.
  - Hapus tanda komentar (`//`) pada route `submissions.grade` dan `assignments.export-csv` di dalam grup dosen.

### 4. `TODO 4` — Filter Semester & Sorting Katalog Mata Kuliah
- **File:** `app/Http/Controllers/MataKuliahController.php`
- **Instruksi:**
  - Pada method `index()`, ambil query string `semester` (`$request->query('semester')`) dan `sort` (`$request->query('sort', 'nama_asc')`).
  - Jika `$semester` diisi, tambahkan filter `$query->where('semester', $semester)`.
  - Terapkan pengurutan berdasarkan nilai `$sort`:
    - `'nama_desc'` -> `orderBy('nama_mk', 'desc')`
    - `'sks_desc'` -> `orderBy('sks', 'desc')`
    - `'sks_asc'` -> `orderBy('sks', 'asc')`
    - `default` (`'nama_asc'`) -> `orderBy('nama_mk', 'asc')`

### 5. `TODO 5a` & `TODO 5b` — Logika Penilaian & Export CSV
- **File:** `app/Http/Controllers/SubmissionController.php`
- **Instruksi:**
  - **`TODO 5a` (`grade`):**
    1. Pastikan dosen yang login adalah pengampu mata kuliah (`$submission->assignment->course->dosen_id === Auth::id()`), jika bukan gagalkan dengan `abort(403)`.
    2. Validasi input `nilai` (`required|integer|min:0|max:100`) dan `catatan_dosen` (`nullable|string|max:1000`).
    3. Perbarui data submission menggunakan `$submission->update(...)` lalu kembalikan `back()->with('success', ...)`.
  - **`TODO 5b` (`exportCsv`):**
    1. Pastikan dosen yang login adalah pengampu mata kuliah tugas tersebut, jika bukan `abort(403)`.
    2. Gunakan `response()->streamDownload(...)` serta `fopen('php://output', 'w')` dan `fputcsv()` untuk mengunduh daftar nilai mahasiswa dalam format CSV.

### 6. `TODO 6` — UI Dropdown Filter Semester & Sort
- **File:** `resources/views/courses/index.blade.php`
- **Instruksi:**
  - Tambahkan elemen `<select name="semester">` yang menampilkan daftar `$semesters` di dalam form pencarian.
  - Tambahkan elemen `<select name="sort">` dengan opsi `nama_asc`, `nama_desc`, `sks_desc`, dan `sks_asc`.

### 7. `TODO 7a`, `TODO 7b`, & `TODO 7c` — UI Rekapitulasi & Form Penilaian Tugas
- **File:** `resources/views/assignments/submissions.blade.php`
- **Instruksi:**
  - **`TODO 7a`:** Tampilkan kartu statistik ringkasan untuk **Total Mahasiswa**, **Sudah Mengumpulkan**, dan **Rata-rata Nilai**.
  - **`TODO 7b`:** Tambahkan tombol **Export CSV** yang mengarah ke `route('assignments.export-csv', $assignment)`.
  - **`TODO 7c`:** Tambahkan form ber-method `PATCH` menuju `route('submissions.grade', $sub->id)` berisi input `nilai` (0–100), input `catatan_dosen`, dan tombol simpan pada baris mahasiswa yang sudah mengumpulkan tugas.

### 8. `TODO 8` — Penambahan Data Seeder Modul 4
- **File:** `database/seeders/DatabaseSeeder.php`
- **Instruksi:**
  - Tambahkan minimal 2 mata kuliah lagi dengan semester dan SKS berbeda, 1 akun mahasiswa tambahan, minimal 2 tugas perkuliahan (`Assignment`), serta contoh pengumpulan tugas (`Submission`) untuk menguji fitur filter, sorting, penilaian, dan export CSV.
  - Jalankan `php artisan migrate:fresh --seed` untuk memuat seluruh data seeder baru.

