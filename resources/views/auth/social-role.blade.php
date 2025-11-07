@extends('layouts.guest')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    {{ __('Pilih Peran Akun') }}
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('Kami mendeteksi login Google baru. Pilih apakah Anda ingin menjadi tenant atau owner.') }}</p>
                    <form method="POST" action="{{ route('auth.social-role.store') }}">
                        @csrf
                        <div class="form-check mb-2">
                            <input class="form-check-input @error('role') is-invalid @enderror" type="radio" name="role" id="roleTenantGoogle" value="tenant" checked>
                            <label class="form-check-label" for="roleTenantGoogle">
                                {{ __('Tenant (Penyewa) – mencari dan mengelola kontrak sewa.') }}
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input @error('role') is-invalid @enderror" type="radio" name="role" id="roleOwnerGoogle" value="owner">
                            <label class="form-check-label" for="roleOwnerGoogle">
                                {{ __('Owner (Pemilik) – mengelola properti dan tenant.') }}
                            </label>
                        </div>
                        @error('role')
                            <div class="text-danger small mb-3">{{ $message }}</div>
                        @enderror
                        <button class="btn btn-primary w-100" type="submit">{{ __('Lanjutkan') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
