@extends('layouts.app')

@section('content')
<div class="container py-4">
    <a href="{{ route('owner.room-types.show', $roomType) }}" class="text-decoration-none small text-muted">&larr; Kembali</a>
    <h1 class="h4 fw-semibold mt-2 mb-4">Edit Tipe Kamar</h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="alert alert-warning">
                Form ini hanya tampilan. Buat action update untuk menyimpan perubahan.
            </div>
            <form class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Properti</label>
                    <select class="form-select" disabled>
                        @foreach ($properties as $property)
                            <option value="{{ $property->id }}" {{ $roomType->property_id === $property->id ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Tipe</label>
                    <input type="text" class="form-control" value="{{ $roomType->name }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Luas (m²)</label>
                    <input type="number" class="form-control" value="{{ $roomType->area_m2 }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Harga Dasar</label>
                    <input type="number" class="form-control" value="{{ $roomType->base_price }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Deposit</label>
                    <input type="number" class="form-control" value="{{ $roomType->deposit }}" disabled>
                </div>
                <div class="col-12">
                    <label class="form-label">Fasilitas</label>
                    <textarea class="form-control" rows="3" disabled>{{ collect($roomType->facilities_json)->implode(', ') }}</textarea>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
