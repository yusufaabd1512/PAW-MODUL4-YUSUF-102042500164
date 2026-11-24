# Context & Dokumentasi Logika Codebase — CeLOE LMS

Dokumen ini menjelaskan arsitektur, struktur database, alur bisnis, serta logika implementasi dari proyek **CeLOE LMS (Telkom University)**.

---

## 1. Ringkasan Proyek & Tech Stack

**CeLOE LMS** adalah aplikasi Learning Management System berbasis web yang dibangun menggunakan **Laravel 12** untuk memfasilitasi proses pembelajaran antara dua peran utama: **Dosen** dan **Mahasiswa**.

- **Backend**: PHP `^8.2`, Laravel Framework `^12.0`
- **Frontend**: Blade Templates, CSS custom (di layout utama), Vite `^6.0.11`, Tailwind CSS `^4.0.0` (`@tailwindcss/vite`)
- **Testing**: PHPUnit `^11.5.3` (`tests/Feature/CeLOETest.php`)
- **Autentikasi**: Session-based authentication bawaan Laravel (`Auth::attempt`, `Auth::login`, `Auth::logout`)

---

## 2. Struktur Direktori Utama

- `app/Http/Controllers/` — Seluruh controller aplikasi:
  - `AuthController.php`
  - `DashboardController.php`
  - `MataKuliahController.php`
  - `MaterialController.php`
  - `AssignmentController.php`
  - `SubmissionController.php`
  - `ProfileController.php`
- `app/Http/Middleware/RoleMiddleware.php` — Middleware pengecekan peran (didaftarkan dengan alias `role` di `bootstrap/app.php`)
- `app/Models/` — Model Eloquent: `User`, `MataKuliah`, `Material`, `Assignment`, `Submission`
- `database/migrations/` — Definisi skema database (`users`, `courses`, `course_user`, `materials`, `assignments`, `submissions`)
- `database/seeders/DatabaseSeeder.php` — Seeder akun default (1 Dosen & 1 Mahasiswa)
- `routes/web.php` — Seluruh definisi route web
- `resources/views/` — Tampilan Blade (`layouts/`, `auth/`, `courses/`, `materials/`, `assignments/`, `profile/`, `dashboard.blade.php`)
- `tests/Feature/CeLOETest.php` — Feature test untuk otorisasi, registrasi, enrollment, dan submission

---

## 3. Skema Database & Relasi

Skema utama didefinisikan pada `database/migrations/0001_01_01_000000_create_users_table.php`, `database/migrations/2026_10_18_080000_create_celoe_lms_tables.php`, dan `database/migrations/2026_10_20_000001_add_status_to_course_user_table.php`.

### Tabel-tabel Utama

1. **`users`**
   - `id`, `name`, `email` (unique), `nomor_induk` (string 30, nullable; ditambahkan di migrasi CeLOE), `role` (string 20, default `'mahasiswa'`), `password`, `remember_token`, `timestamps`.
   - Peran aktif pada sistem hanya `'dosen'` dan `'mahasiswa'`.

2. **`courses`** (dipetakan oleh model `App\Models\MataKuliah`)
   - `id`, `kode_mk` (string 20, unique), `nama_mk`, `deskripsi` (nullable), `sks` (unsignedTinyInteger, default 3), `semester` (string 50), `dosen_id` (FK ke `users.id`, `restrictOnDelete`), `timestamps`.

3. **`course_user`** (tabel pivot enrollment mahasiswa ke mata kuliah)
   - `id`, `course_id` (FK ke `courses.id`, `cascadeOnDelete`), `user_id` (FK ke `users.id`, `cascadeOnDelete`), `status` (string 20, default `'pending'`), `timestamps`.
   - Unique constraint pada `['course_id', 'user_id']` dan index pada `['course_id', 'status']`.
   - Nilai `status`: `'pending'`, `'approved'`, `'rejected'`.

4. **`materials`**
   - `id`, `course_id` (FK ke `courses.id`, `cascadeOnDelete`), `judul`, `konten` (longText), `minggu_ke` (unsignedTinyInteger, default 1), `urutan` (integer, default 0), `timestamps`.

5. **`assignments`**
   - `id`, `course_id` (FK ke `courses.id`, `cascadeOnDelete`), `judul`, `deskripsi` (text), `deadline` (dateTime), `timestamps`.

6. **`submissions`**
   - `id`, `assignment_id` (FK ke `assignments.id`, `cascadeOnDelete`), `user_id` (FK ke `users.id`, `cascadeOnDelete`), `jawaban` (longText), `nilai` (`unsignedTinyInteger`, nullable — ditambahkan pada migrasi `2026_10_25_000001_add_grading_to_submissions_table.php`), `catatan_dosen` (`text`, nullable), `submitted_at` (dateTime), `timestamps`.
   - Unique constraint pada `['assignment_id', 'user_id']` sehingga setiap mahasiswa hanya memiliki satu entri jawaban per tugas (diperbarui jika mengirim ulang sebelum deadline).

### Data Default (`DatabaseSeeder.php`)
- **Dosen**: `dosen@celoe.test` / password `dosen123` (`role: 'dosen'`, `nomor_induk: '198501012015041001'`)
- **Mahasiswa 1**: `mahasiswa@celoe.test` / password `mahasiswa123` (`role: 'mahasiswa'`, `nomor_induk: '1301210001'`)
- **Mahasiswa 2**: `siti@celoe.test` / password `mahasiswa123` (`role: 'mahasiswa'`, `nomor_induk: '1301210002'`)
- **Mata Kuliah**: 3 mata kuliah (`IF301` Ganjil 2026/2027 4 SKS, `IF204` Ganjil 2026/2027 3 SKS, `IF402` Genap 2026/2027 2 SKS), 2 tugas, serta 2 contoh submission (1 sudah dinilai dengan nilai 88, 1 belum dinilai).

---

## 4. Model & Relasi Eloquent

- **`User`** (`app/Models/User.php`)
  - Helper peran: `isAdmin()` (selalu mengembalikan `false` karena peran admin telah dihapus), `isDosen()`, `isMahasiswa()`.
  - `taughtCourses()`: `hasMany(MataKuliah::class, 'dosen_id')`.
  - `enrolledCourses()`: `belongsToMany(MataKuliah::class, 'course_user', ...)` yang difilter hanya `course_user.status = 'approved'`.
  - `allCourseEnrollments()`: `belongsToMany` seluruh mata kuliah yang pernah diajukan mahasiswa (termasuk `pending` & `rejected`).
  - `submissions()`: `hasMany(Submission::class)`.

- **`MataKuliah`** (`app/Models/MataKuliah.php`)
  - Menggunakan `protected $table = 'courses';`.
  - `dosen()`: `belongsTo(User::class, 'dosen_id')`.
  - `students()`: `belongsToMany` semua mahasiswa di `course_user` dengan pivot `status`.
  - `approvedStudents()` & `pendingStudents()`: relasi `belongsToMany` yang masing-masing memfilter `course_user.status` bernilai `'approved'` dan `'pending'`.
  - Helper: `hasApprovedStudent(int $userId)` dan `getStudentEnrollmentStatus(int $userId)` (mengembalikan `'approved'`, `'pending'`, `'rejected'`, atau `'none'`).
  - `materials()`: `hasMany(Material::class, 'course_id')` terurut berdasarkan `minggu_ke` lalu `urutan`.
  - `assignments()`: `hasMany(Assignment::class, 'course_id')` terurut berdasarkan `deadline`.

- **`Material`** (`app/Models/Material.php`)
  - `course()`: `belongsTo(MataKuliah::class, 'course_id')`.

- **`Assignment`** (`app/Models/Assignment.php`)
  - `course()`: `belongsTo(MataKuliah::class, 'course_id')`.
  - `submissions()`: `hasMany(Submission::class)`.
  - `isOverdue()`: mengembalikan `true` jika `now()->greaterThan($this->deadline)`.
  - `userSubmission(?int $userId)`: mengambil objek `Submission` milik user tertentu untuk tugas ini.

- **`Submission`** (`app/Models/Submission.php`)
  - `assignment()`: `belongsTo(Assignment::class)`.
  - `user()`: `belongsTo(User::class)`.
  - Accessor `getStatusNilaiAttribute()`: mengembalikan `'Belum Dinilai'` jika `nilai === null`, `'Lulus'` jika `nilai >= 70`, dan `'Perlu Perbaikan'` jika `< 70`.

---

## 5. Autentikasi, Otorisasi, & Keamanan

1. **Guest & Rate Limiting**:
   - Route `/login` dan `/register` dibungkus middleware `guest`.
   - `POST /login` dan `POST /register` dibatasi rate limiter `throttle:5,1` (maksimal 5 percobaan per menit).
2. **Pendaftaran Akun (`AuthController@register`)**:
   - Memvalidasi `name`, `email` (unique), `nomor_induk` (unique, max 30), dan `password` (min 6, confirmed).
   - **Role Locking**: kolom `role` dikunci secara eksplisit di backend menjadi `'mahasiswa'`. Dosen tidak bisa mendaftar melalui halaman registrasi publik.
3. **Proteksi Route (`auth` & `role`) serta Controller**:
   - Seluruh route terproteksi di `routes/web.php` dibungkus dalam `Route::middleware(['auth'])->group(...)`, serta dikelompokkan berdasarkan peran menggunakan `Route::middleware(['role:dosen'])` dan `Route::middleware(['role:mahasiswa'])`.
   - Selain middleware level route, controller juga memeriksa kepemilikan resource (`$course->dosen_id === Auth::id()`) dan keanggotaan kelas (`course_user.status === 'approved'`).

---

## 6. Logika Bisnis per Modul (Controllers)

### A. `AuthController`
- `showLoginForm()` / `showRegisterForm()`: Redirect ke `dashboard` jika sudah login; jika belum, tampilkan view `auth.login` / `auth.register`.
- `register()`: Validasi input, buat user baru dengan `role = 'mahasiswa'`, langsung login (`Auth::login`), regenerasi session, dan redirect ke `dashboard`.
- `login()`: Validasi email & password, panggil `Auth::attempt($credentials, $request->boolean('remember'))`, regenerasi session, dan redirect ke `intended(route('dashboard'))`.
- `logout()`: `Auth::logout()`, invalidasi session, regenerasi CSRF token, lalu redirect ke `login`.

### B. `DashboardController`
- Mengirimkan daftar pengumuman statis (`$announcements`), daftar mata kuliah (`$courses`), tugas mendatang (`$upcomingAssignments`), dan ringkasan statistik (`$stats`) ke view `dashboard`.
- **Untuk Dosen**:
  - Menampilkan mata kuliah yang diampu (`dosen_id = $user->id`) beserta jumlah `approvedStudents`, `materials`, dan `assignments`.
  - Menampilkan 5 tugas terdekat dari mata kuliah miliknya beserta jumlah `submissions`.
  - Menghitung `pending_approvals` (jumlah mahasiswa berstatus `'pending'` pada seluruh mata kuliah yang diampu).
- **Untuk Mahasiswa**:
  - Menampilkan mata kuliah yang diikuti melalui relasi `enrolledCourses()` (hanya yang berstatus `'approved'`).
  - Menampilkan 6 tugas terdekat dari mata kuliah yang diikuti beserta status pengumpulan masing-masing mahasiswa.
  - Statistik meliputi `enrolled_courses`, `total_materials`, `total_assignments`, dan `submitted_count`.

### C. `MataKuliahController`
- `index()`:
  - Dosen hanya melihat mata kuliah yang diampunya; Mahasiswa melihat seluruh katalog mata kuliah di LMS.
  - Mendukung pencarian (`search`) berdasarkan `kode_mk`, `nama_mk`, atau `semester`, dengan pagination 9 item per halaman (`paginate(9)->withQueryString()`).
- `create()` / `store()`: Khusus Dosen. Validasi `kode_mk` (unique), `nama_mk`, `deskripsi`, `sks` (1–6), `semester`. `dosen_id` otomatis diisi `Auth::id()`.
- `show(MataKuliah $course)`:
  - **Jika Dosen**: Harus pemilik mata kuliah (`dosen_id === $user->id`). Memuat `materials`, `assignments.submissions`, `approvedStudents`, dan `pendingStudents`.
  - **Jika Mahasiswa**: Memeriksa status di `course_user` (`$enrollmentStatus`: `'approved'`, `'pending'`, `'rejected'`, atau `'none'`).
    - Jika `'approved'`: Memuat `materials`, `assignments` (beserta submission milik mahasiswa tersebut saja), dan `approvedStudents`.
    - Jika selain `'approved'`: **Hanya** memuat relasi `dosen` agar konten materi dan tugas tidak terekspos ke view.
- `edit()` / `update()` / `destroy()`: Khusus Dosen pengampu mata kuliah tersebut.
- **Workflow Enrollment**:
  - `enrollRequest(MataKuliah $course)`: Khusus Mahasiswa.
    - Jika belum pernah mendaftar: `attach` dengan `status = 'pending'`.
    - Jika sudah `'approved'` atau `'pending'`: kembalikan pesan `info`.
    - Jika sebelumnya `'rejected'`: `updateExistingPivot` menjadi `'pending'` (mengizinkan pengajuan ulang).
  - `approveStudent(MataKuliah $course, User $user)`: Khusus Dosen pengampu. Mengubah pivot `status` menjadi `'approved'`.
  - `rejectStudent(MataKuliah $course, User $user)`: Khusus Dosen pengampu. Mengubah pivot `status` menjadi `'rejected'`.
  - `unenrollStudent(MataKuliah $course, User $user)`: Khusus Dosen pengampu. Menghapus relasi mahasiswa dari kelas (`detach`).

### D. `MaterialController`
- Semua aksi (`create`, `store`, `edit`, `update`, `destroy`) hanya dapat diakses oleh **Dosen pengampu** mata kuliah terkait.
- `create()`: Menghitung `$nextMinggu` otomatis berdasarkan nilai maksimum `minggu_ke` yang sudah ada + 1.
- `store()` & `update()`: Validasi `judul` (max 255), `konten` (max 50.000), `minggu_ke` (integer 1–16), `urutan` (nullable, default 0).

### E. `AssignmentController`
- Semua aksi (`create`, `store`, `edit`, `update`, `destroy`, `submissions`) hanya dapat diakses oleh **Dosen pengampu** mata kuliah terkait.
- `store()`: Validasi `judul`, `deskripsi` (max 10.000), dan `deadline` (`required|date|after:now` — saat pembuatan harus di masa mendatang).
- `update()`: Validasi `deadline` tidak mensyaratkan `after:now` agar dosen tetap bisa mengedit tugas yang deadline-nya sudah dekat atau lewat.
- `submissions(Assignment $assignment)`: Menampilkan daftar seluruh `approvedStudents` pada mata kuliah tersebut dipasangkan dengan `submissions` yang dipetakan berdasarkan `user_id`, sehingga dosen bisa melihat siapa saja yang sudah maupun belum mengumpulkan tugas.

### F. `SubmissionController`
- `store(Request $request, Assignment $assignment)`:
  1. Memastikan user adalah `mahasiswa` (`403` jika bukan).
  2. Memastikan mahasiswa terdaftar dengan status `'approved'` pada mata kuliah tugas tersebut (`403` jika tidak/belum disetujui).
  3. Memeriksa `$assignment->isOverdue()`. Jika sudah melewati deadline, redirect `back()` dengan flash `error`.
  4. Validasi `jawaban` (string, min 10 karakter, max 20.000 karakter).
  5. Menyimpan atau memperbarui jawaban menggunakan `Submission::updateOrCreate` berdasarkan pasangan `['assignment_id', 'user_id']` dan mencatat `submitted_at = now()`.

### G. `ProfileController`
- `edit()` & `update()`: Mengizinkan pengguna yang sedang login memperbarui `name`, `email` (unique kecuali milik sendiri), `nomor_induk` (nullable), dan `password` (opsional; hanya di-hash dan disimpan jika diisi).

---

## 7. Daftar Routing (`routes/web.php`)

- `/` — Redirect ke `dashboard` jika sudah login, atau `login` jika belum.
- **Auth (`guest`)**: `GET/POST /login`, `GET/POST /register`, `POST /logout`.
- **Dashboard & Profil**: `GET /dashboard`, `GET /profile`, `PUT /profile`.
- **Mata Kuliah**: `GET /courses`, `GET /courses/create`, `POST /courses`, `GET /courses/{course}`, `GET /courses/{course}/edit`, `PUT /courses/{course}`, `DELETE /courses/{course}`.
- **Enrollment**:
  - `POST /courses/{course}/enroll-request`
  - `PATCH /courses/{course}/enrollments/{user}/approve`
  - `PATCH /courses/{course}/enrollments/{user}/reject`
  - `DELETE /courses/{course}/unenroll/{user}`
- **Materi**: `GET /courses/{course}/materials/create`, `POST /courses/{course}/materials`, `GET /materials/{material}/edit`, `PUT /materials/{material}`, `DELETE /materials/{material}`.
- **Tugas & Pengumpulan**:
  - `GET /courses/{course}/assignments/create`, `POST /courses/{course}/assignments`
  - `GET /assignments/{assignment}/edit`, `PUT /assignments/{assignment}`, `DELETE /assignments/{assignment}`
  - `GET /assignments/{assignment}/submissions`
  - `POST /assignments/{assignment}/submit`

*(Catatan: Semua parameter ID pada route menggunakan constraint `.whereNumber(...)`.)*

---

## 8. Pengujian (`tests/Feature/CeLOETest.php`)

Suite feature test memverifikasi skenario inti berikut:
1. Mahasiswa dilarang mengakses `/courses/create` (`403`).
2. Dosen dapat membuat mata kuliah baru.
3. Dosen tidak dapat mengedit mata kuliah atau membuat materi pada mata kuliah milik dosen lain (`403`).
4. Mahasiswa dapat mendaftar akun baru dan otomatis mendapat peran `'mahasiswa'`.
5. Alur enrollment lengkap: Mahasiswa mengajukan pendaftaran (`pending`) -> diblokir dari submit tugas (`403`) -> Dosen menyetujui (`approved`) -> Mahasiswa berhasil mengirim submission.
6. Request tanpa autentikasi ke `/dashboard`, `/profile`, `/courses`, `/courses/create` diarahkan ke `/login`.
7. Mahasiswa berstatus `pending` tidak dapat melihat konten materi pada halaman detail mata kuliah.
8. Dosen dapat menolak pendaftaran mahasiswa (`rejected`), dan mahasiswa yang ditolak tetap diblokir dari pengiriman tugas (`403`).

