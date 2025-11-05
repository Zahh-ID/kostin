@extends('layouts.app')

@section('content')
<div class="container py-4">
    <a href="{{ route('owner.room-types.index') }}" class="text-decoration-none small text-muted">&larr; Batal</a>
    <h1 class="h4 fw-semibold mt-2 mb-4">Tambah Tipe Kamar</h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="alert alert-info">
                Implementasikan route penyimpanan untuk mengaktifkan form ini.
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
                    <label class="form-label">Nama Tipe</label>
                    <input type="text" class="form-control" placeholder="Contoh: Deluxe Single">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Luas (m²)</label>
                    <input type="number" class="form-control" placeholder="12">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipe Kamar Mandi</label>
                    <input type="text" class="form-control" placeholder="Dalam / Luar">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Harga Dasar / Bulan</label>
                    <input type="number" class="form-control" placeholder="1500000">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deposit</label>
                    <input type="number" class="form-control" placeholder="500000">
                </div>
                <div class="col-12">
                    <label class="form-label">Fasilitas (pisahkan dengan koma)</label>
                    <textarea class="form-control" rows="3" placeholder="AC, Wi-Fi, Air panas"></textarea>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary" type="button" disabled>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
