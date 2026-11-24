@extends('layouts.app')

@section('title', 'Daftar Pengumpulan — ' . $assignment->judul)

@section('content')
<div style="margin-bottom: 1.25rem;">
    <a href="{{ route('courses.show', $assignment->course_id) }}" class="btn btn-secondary btn-sm">
        Kembali ke Mata Kuliah
    </a>
</div>

@php
    $gradedSubmissions = $submissions->whereNotNull('nilai');
    $averageScore = $gradedSubmissions->count() > 0 ? round($gradedSubmissions->avg('nilai'), 1) : null;
@endphp

<!-- Header Penugasan -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                <span class="code-pill">{{ $assignment->course->kode_mk }}</span>
                <span style="font-size: 0.85rem; color: var(--text-muted);">{{ $assignment->course->nama_mk }}</span>
            </div>
            <h1 class="card-title">{{ $assignment->judul }}</h1>
            <p class="card-subtitle">
                Batas Pengumpulan: <strong>{{ $assignment->deadline ? $assignment->deadline->translatedFormat('l, d F Y — H:i') . ' WIB' : '-' }}</strong>
                @if($assignment->isOverdue())
                    <span style="color: #dc2626; font-size: 0.8125rem; font-weight: 600; margin-left: 0.5rem;">Batas Waktu Terlewat</span>
                @else
                    <span style="color: #059669; font-size: 0.8125rem; font-weight: 600; margin-left: 0.5rem;">Sedang Berlangsung</span>
                @endif
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ route('assignments.export-csv', $assignment) }}" class="btn btn-primary btn-sm">
                Export CSV
            </a>

            <a href="{{ route('assignments.edit', $assignment->id) }}" class="btn btn-secondary btn-sm">
                Edit Tugas
            </a>
        </div>
    </div>

    <div class="card-body" style="background-color: #f8fafc; border-top: 1px solid var(--border-color); padding: 1rem 1.25rem;">
        <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.25rem;">PETUNJUK PENUGASAN:</div>
        <div style="font-size: 0.925rem; color: var(--text-main); white-space: pre-wrap; line-height: 1.6;">{{ $assignment->deskripsi }}</div>
    </div>
</div>

<!-- Statistik Pengumpulan & Nilai -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="margin: 0; padding: 1.25rem;">
        <div style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--slate-500); margin-bottom: 0.5rem;">
            Total Mahasiswa
        </div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--slate-900);">
            {{ $students->count() }}
        </div>
    </div>
    <div class="card" style="margin: 0; padding: 1.25rem;">
        <div style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--slate-500); margin-bottom: 0.5rem;">
            Sudah Mengumpulkan
        </div>
        <div style="font-size: 1.75rem; font-weight: 700; color: #059669;">
            {{ $submissions->count() }}
        </div>
    </div>
    <div class="card" style="margin: 0; padding: 1.25rem;">
        <div style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--slate-500); margin-bottom: 0.5rem;">
            Rata-rata Nilai
        </div>
        <div style="font-size: 1.75rem; font-weight: 700; color: #2563eb;">
            {{ $averageScore !== null ? $averageScore : '-' }}
        </div>
    </div>
</div>

<!-- Daftar Pengumpulan Mahasiswa -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 class="card-title" style="font-size: 1.1rem;">Rekapitulasi Pengumpulan &amp; Penilaian Mahasiswa</h2>
            <p class="card-subtitle">Semua mahasiswa yang terdaftar resmi di kelas mata kuliah ini</p>
        </div>
    </div>

    <div class="card-body" style="padding: 0;">
        @if($students->isEmpty())
            <div class="empty-state" style="padding: 3rem 1.5rem;">
                <p style="font-weight: 600; margin-bottom: 0.25rem;">Belum ada mahasiswa yang disetujui.</p>
                <p style="font-size: 0.875rem; color: var(--slate-500);">Mahasiswa perlu mengajukan pendaftaran dan disetujui untuk mengikuti kelas mata kuliah ini terlebih dahulu.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 45px;">No</th>
                            <th>Mahasiswa</th>
                            <th style="width: 130px;">NIM</th>
                            <th style="width: 160px;">Status &amp; Waktu</th>
                            <th>Jawaban Teks</th>
                            <th style="min-width: 280px;">Penilaian Dosen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $student)
                            @php
                                $sub = $submissions->get($student->id);
                            @endphp
                            <tr>
                                <td style="color: var(--text-muted); font-size: 0.875rem;">{{ $index + 1 }}</td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-main);">{{ $student->name }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $student->email }}</div>
                                </td>
                                <td>
                                    <span class="code-pill">{{ $student->nomor_induk ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($sub)
                                        <div style="color: #059669; font-weight: 600; font-size: 0.8125rem;">Sudah Mengumpulkan</div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.125rem;">
                                            {{ $sub->submitted_at ? $sub->submitted_at->translatedFormat('d M Y, H:i') : $sub->updated_at->translatedFormat('d M Y, H:i') }} WIB
                                        </div>
                                        <div style="margin-top: 0.35rem;">
                                            @if($sub->nilai !== null)
                                                <span style="display: inline-block; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; background-color: {{ $sub->nilai >= 70 ? '#d1fae5' : '#fef3c7' }}; color: {{ $sub->nilai >= 70 ? '#065f46' : '#92400e' }};">
                                                    {{ $sub->nilai }}/100 ({{ $sub->status_nilai }})
                                                </span>
                                            @else
                                                <span style="display: inline-block; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; background-color: #f1f5f9; color: #475569;">
                                                    {{ $sub->status_nilai }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span style="color: #d97706; font-weight: 600; font-size: 0.8125rem;">Belum Mengumpulkan</span>
                                    @endif
                                </td>
                                <td>
                                    @if($sub)
                                        <details style="cursor: pointer;">
                                            <summary style="font-weight: 600; color: var(--tel-red); font-size: 0.875rem; outline: none;">
                                                Lihat Jawaban ({{ strlen($sub->jawaban) }} Karakter)
                                            </summary>
                                            <div style="margin-top: 0.75rem; padding: 0.875rem; background: #f8fafc; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap; max-height: 300px; overflow-y: auto;">{{ $sub->jawaban }}</div>
                                        </details>
                                    @else
                                        <span style="font-size: 0.85rem; color: var(--text-muted); font-style: italic;">Tidak ada kiriman</span>
                                    @endif
                                </td>
                                <td>
                                    @if($sub)
                                        <form action="{{ route('submissions.grade', $sub->id) }}" method="POST" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: nowrap;">
                                            @csrf
                                            @method('PATCH')
                                            <input
                                                type="number"
                                                name="nilai"
                                                min="0"
                                                max="100"
                                                value="{{ $sub->nilai }}"
                                                placeholder="0-100"
                                                required
                                                class="form-control"
                                                style="width: 78px; padding: 0.375rem 0.5rem; font-size: 0.8125rem;"
                                            >
                                            <input
                                                type="text"
                                                name="catatan_dosen"
                                                value="{{ $sub->catatan_dosen }}"
                                                placeholder="Catatan untuk mahasiswa..."
                                                maxlength="1000"
                                                class="form-control"
                                                style="flex: 1; min-width: 140px; padding: 0.375rem 0.625rem; font-size: 0.8125rem;"
                                            >
                                            <button type="submit" class="btn btn-primary btn-sm" style="white-space: nowrap; padding: 0.4375rem 0.75rem;">
                                                Simpan Nilai
                                            </button>
                                        </form>
                                    @else
                                        <span style="font-size: 0.8rem; color: var(--text-muted);">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
