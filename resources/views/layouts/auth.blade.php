<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KostIn') }}</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        @stack('styles')
        @livewireStyles

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .auth-bg {
                background: radial-gradient(95% 85% at 25% 20%, rgba(71, 138, 255, 0.18), transparent 50%),
                    radial-gradient(85% 80% at 90% 0%, rgba(111, 201, 173, 0.18), transparent 45%),
                    radial-gradient(75% 75% at 10% 90%, rgba(255, 193, 7, 0.16), transparent 50%),
                    linear-gradient(135deg, #f8fafc, #ffffff);
            }

            .auth-card {
                background: #ffffff;
                box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
                border: 1px solid #e5ecf5;
                border-radius: 1.25rem;
            }
        </style>
    </head>
    <body class="bg-light">
        <div class="auth-bg min-vh-100 d-flex flex-column">
            <header class="py-3">
                <div class="container d-flex justify-content-between align-items-center">
                    <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none text-dark fw-semibold">
                        <i class="bi bi-house-fill text-primary"></i>
                        <span>{{ config('app.name', 'KostIn') }}</span>
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-grid me-1"></i> {{ __('Kembali ke Dashboard') }}
                        </a>
                    @endauth
                </div>
            </header>

            <main class="container pb-5 flex-grow-1">
                {{ $slot ?? '' }}
            </main>

            <footer class="py-3 text-center text-muted small">
                &copy; {{ now()->year }} {{ config('app.name', 'KostIn') }}
            </footer>
        </div>

        @livewireScripts
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        @stack('scripts')
    </body>
</html>
