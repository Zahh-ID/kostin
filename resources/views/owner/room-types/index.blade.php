@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-semibold mb-1">Tipe Kamar</h1>
            <p class="text-muted mb-0">Daftar tipe kamar dari seluruh properti Anda.</p>
        </div>
        <a href="{{ route('owner.room-types.create') }}" class="btn btn-primary btn-sm">Tambah Tipe</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Properti</th>
                        <th>Luas</th>
                        <th>Harga Dasar</th>
                        <th>Unit</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($roomTypes as $roomType)
                        <tr>
                            <td>{{ $roomType->name }}</td>
                            <td>{{ $roomType->property?->name }}</td>
                            <td>{{ $roomType->area_m2 ?? '-' }} m²</td>
                            <td>Rp{{ number_format($roomType->base_price ?? 0, 0, ',', '.') }}</td>
                            <td>{{ $roomType->rooms->count() }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('owner.room-types.show', $roomType) }}" class="btn btn-outline-primary">Detail</a>
                                    <a href="{{ route('owner.room-types.edit', $roomType) }}" class="btn btn-outline-secondary">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada tipe kamar.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($roomTypes instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="card-footer bg-white">
                {{ $roomTypes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
