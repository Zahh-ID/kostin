<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        @stack('styles')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-light">
        <div class="d-flex flex-column min-vh-100">
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                <div class="container">
                    <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                        <x-application-logo style="width: 48px; height: auto;" />
                        <span class="fw-semibold">{{ config('app.name', 'KostIn') }}</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbar" aria-controls="publicNavbar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="publicNavbar">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link @if (request()->routeIs('home')) active @endif" href="{{ route('home') }}">Beranda</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if (request()->routeIs('faq')) active @endif" href="{{ route('faq') }}">FAQ</a>
                            </li>
                        </ul>
                        <div class="d-flex gap-2">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary btn-sm">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">Masuk</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <main class="flex-grow-1">
                @if (trim($__env->yieldContent('content')))
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>

            <footer class="bg-white border-top py-3">
                <div class="container text-center text-muted small">
                    &copy; {{ now()->year }} {{ config('app.name', 'KostIn') }}. All rights reserved.
                </div>
            </footer>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        @stack('scripts')
    </body>
</html>
