@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">Daftar</div>
                <div class="card-body">
                    <a href="{{ route('auth.redirect') }}" class="btn btn-danger w-100 mb-3">Daftar dengan Google</a>
                    <hr>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required>
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" required>
                            @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control">
                            @error('phone')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Daftar sebagai</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role" id="role-tenant" value="tenant" {{ old('role', 'tenant') === 'tenant' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="role-tenant">
                                        Tenant &mdash; cari dan kelola kontrak sewa
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role" id="role-owner" value="owner" {{ old('role') === 'owner' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="role-owner">
                                        Owner &mdash; kelola properti dan tenant
                                    </label>
                                </div>
                            </div>
                            @error('role')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                            @error('password')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Daftar</button>
                    </form>
                    <p class="mt-3 mb-0">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
