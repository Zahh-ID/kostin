@extends('layouts.app')

@section('content')
<div class="container py-4">
    <a href="{{ route('owner.shared-tasks.index') }}" class="text-decoration-none small text-muted">&larr; Batal</a>
    <h1 class="h4 fw-semibold mt-2 mb-4">Buat Tugas Operasional</h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="alert alert-info">
                Hubungkan form ini ke endpoint penyimpanan untuk menjalankan otomatisasi scheduler.
            </div>
            <form class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Properti</label>
                    <select class="form-select">
                        @forelse ($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->name }}</option>
                        @empty
                            <option value="">Belum ada properti</option>
                        @endforelse
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Judul Tugas</label>
                    <input type="text" class="form-control" placeholder="Contoh: Kebersihan Area Umum">
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" rows="3" placeholder="Ceritakan detail tugas, checklist, atau SOP singkat"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">RRULE / Jadwal</label>
                    <input type="text" class="form-control" placeholder="Mis. FREQ=WEEKLY;INTERVAL=1;BYDAY=MO">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab (User ID)</label>
                    <input type="number" class="form-control" placeholder="Opsional">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jadwal Mulai</label>
                    <input type="datetime-local" class="form-control">
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary" type="button" disabled>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
