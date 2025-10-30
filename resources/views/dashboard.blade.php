@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Dashboard KostIn</div>
                <div class="card-body">
                    <h5 class="card-title">Halo, {{ auth()->user()->name }}!</h5>
                    <p class="card-text">Gunakan Swagger di <a href="{{ url('/api/docs') }}">/api/docs</a> untuk eksplorasi API. Endpoint backend dilindungi role admin/owner/tenant.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Role Anda: <strong>{{ auth()->user()->role }}</strong></li>
                        <li class="list-group-item">Email: {{ auth()->user()->email }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
